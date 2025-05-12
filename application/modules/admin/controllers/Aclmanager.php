<?php

use MX\MX_Controller;

class Aclmanager extends MX_Controller
{
    /**
     * 执行权限检查并初始化管理员库
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('administrator');

        requirePermission("viewPermissions");
    }

    /**
     * 显示权限管理首页
     */
    public function index()
    {
        // 加载视图模板
        $output = $this->template->loadPage("aclmanager/index.tpl");

        // 输出内容到管理面板
        $output = $this->administrator->box("用户组与权限管理", $output);
        $this->administrator->view($output, "modules/admin/css/aclmanager.css");
    }

    // --- 用户组相关方法 ---

    /**
     * 管理用户组
     */
    public function groups()
    {
        // 准备视图数据
        $data = [
            "groups" => $this->acl_model->getGroups(),
            "modules" => $this->getAllRoles(),
            "guestId" => $this->config->item('default_guest_group'),
            "playerId" => $this->config->item('default_player_group'),
            "links" => $this->cms_model->getLinks("all"),
            "sideboxes" => $this->cms_model->getSideboxes(),
            "pages" => $this->cms_model->getPages()
        ];

        if ($data['groups']) {
            foreach ($data['groups'] as $k => $v) {
                $data['groups'][$k]['memberCount'] = $this->acl_model->getGroupMemberCount($v['id']);
            }
        }

        // 加载视图模板
        $output = $this->template->loadPage("aclmanager/groups.tpl", $data);

        // 输出到管理面板
        $output = $this->administrator->box("<a href='" . pageURL . "admin/aclmanager'>用户组与权限管理</a> &rarr; 用户组", $output);

        $this->administrator->view($output, "modules/admin/css/aclmanager.css", "modules/admin/js/groups.js");
    }

    /**
     * 编辑用户组
     */
    public function editGroup($id = false)
    {
        requirePermission("editPermissions");

        if (!is_numeric($id) || !$id) {
            die();
        }

        $group = $this->acl_model->getGroup($id);

        if (!$group) {
            show_error("没有找到ID为 " . $id . " 的用户组", 400);

            die();
        }

        // 修改标题
        $this->administrator->setTitle($group['name']);

        // 准备视图数据
        $data = [
            "group" => $group,
            "modules" => $this->getAllRoles($id),
            "members" => $this->acl_model->getGroupMembers($id),
            "guestId" => $this->config->item('default_guest_group'),
            "playerId" => $this->config->item('default_player_group'),
            "links" => $this->cms_model->getLinks("all"),
            "sideboxes" => $this->cms_model->getSideboxes(),
            "pages" => $this->cms_model->getPages()
        ];

        // 链接
        foreach ($data['links'] as $key => $value) {
            $data['links'][$key]['has'] = $this->acl_model->groupHasRole($id, $value['id'], "--MENU--");
        }

        // 边栏
        foreach ($data['sideboxes'] as $key => $value) {
            $data['sideboxes'][$key]['has'] = $this->acl_model->groupHasRole($id, $value['id'], "--SIDEBOX--");
        }

        // 页面
        foreach ($data['pages'] as $key => $value) {
            $data['pages'][$key]['has'] = $this->acl_model->groupHasRole($id, $value['id'], "--PAGE--");
        }

        // 模块
        foreach ($data['modules'] as $key => $value) {
            // 数据库角色
            if ($data['modules'][$key]['db']) {
                foreach ($data['modules'][$key]['db'] as $subKey => $subValue) {
                    $data['modules'][$key]['db'][$subKey]['has'] = $this->acl_model->groupHasRole($id, $subValue['role_name'], $key);
                }
            }

            // 清单角色
            if ($data['modules'][$key]['manifest']) {
                foreach ($data['modules'][$key]['manifest'] as $subKey => $subValue) {
                    $data['modules'][$key]['manifest'][$subKey]['has'] = $this->acl_model->groupHasRole($id, $subKey, $key);
                }
            }
        }

        // 加载视图模板
        $output = $this->template->loadPage("aclmanager/edit_group.tpl", $data);

        // 输出到管理面板
        $content = $this->administrator->box('<a href="' . $this->template->page_url . 'admin/aclmanager/groups">用户组</a> &rarr; ' . $group['name'], $output);

        $this->administrator->view($content, "modules/admin/css/aclmanager.css", "modules/admin/js/groups.js");
    }

    /**
     * 删除用户组
     */
    public function groupDelete($id)
    {
        requirePermission("deletePermissions");

        $this->acl_model->deleteGroup($id);
    }

    /**
     * 创建用户组
     */
    public function groupCreate()
    {
        requirePermission("addPermissions");

        $name = $this->input->post('name');
        $color = $this->input->post('color');
        $priority = $this->input->post('priority');
        $description = $this->input->post('description');
        $roles = array();

        // 确保有用户组名
        if (!$name) {
            die('请指定用户组名!');
        }

        // 循环所有POST数据以获取角色
        foreach ($_POST as $k => $v) {
            // 确保这是一个角色
            if (!in_array($k, array("name", "description", "color"))) {
                if ($v == "true") {
                    array_push($roles, $k);
                }
            }
        }

        $data = array(
            'name' => $name,
            'color' => $color,
            'priority' => $priority,
            'description' => $description
        );

        $id = $this->acl_model->createGroup($data);

        foreach ($roles as $role) {
            // 处理可见性权限
            if (preg_match("/^(PAGE|SIDEBOX|MENU)_/", $role)) {
                /**
                 * [0] 可见性类型
                 * [1] ID/角色
                 */
                $parts = explode("_", $role);
                $roleName = $parts[1];
                $moduleName = "--" . $parts[0] . "--";

                $this->acl_model->addRoleToGroup($id, $roleName, $moduleName);
            } elseif (preg_match("/-/", $role)) {
                /**
                 * [0] 模块
                 * [1] 角色
                 */
                $roleParts = explode("-", $role);

                $this->acl_model->addRoleToGroup($id, $roleParts[1], $roleParts[0]);
            }
        }

        die('1');
    }

    /**
     * 保存用户组
     */
    public function groupSave($id)
    {
        requirePermission("editPermissions");

        $name = $this->input->post('name');
        $color = $this->input->post('color');
        $priority = $this->input->post('priority');
        $description = $this->input->post('description');
        $roles = array();

        // 确保有用户组名
        if (!$name) {
            die('请指定用户组名!');
        }

        // 循环所有POST数据以获取角色
        foreach ($_POST as $k => $v) {
            // 确保这是一个角色
            if (!in_array($k, array("name", "description", "color"))) {
                if ($v == "true") {
                    array_push($roles, $k);
                }
            }
        }

        $this->acl_model->saveGroup($id, array('name' => $name, 'priority' => $priority, 'color' => $color, 'description' => $description));

        $this->acl_model->deleteAllRoleFromGroup($id);

        foreach ($roles as $role) {
            // 处理可见性权限
            if (preg_match("/^(PAGE|SIDEBOX|MENU)_/", $role)) {
                /**
                 * [0] 可见性类型
                 * [1] ID/角色
                 */
                $parts = explode("_", $role);
                $roleName = $parts[1];
                $moduleName = "--" . $parts[0] . "--";

                $this->acl_model->addRoleToGroup($id, $roleName, $moduleName);
            } elseif (preg_match("/-/", $role)) {
                /**
                 * [0] 模块
                 * [1] 角色
                 */
                $roleParts = explode("-", $role);

                $this->acl_model->addRoleToGroup($id, $roleParts[1], $roleParts[0]);
            }
        }

        die('1');
    }

    /**
     * 添加用户组成员
     */
    public function addMember()
    {
        $groupId = $this->input->post('groupId');
        $account = $this->input->post('name');

        $accountId = $this->user->getId($account);

        if (!$accountId) {
            die("invalid");
        }

        $this->acl_model->assignGroupToUser($groupId, $accountId);
    }

    /**
     * 移除用户组成员
     */
    public function removeMember()
    {
        $groupId = $this->input->post('groupId');
        $account = $this->input->post('name');

        $accountId = $this->user->getId($account);

        $this->acl_model->removeGroupFromUser($groupId, $accountId);
    }

    // --- 用户相关方法 ---

    /**
     * 管理用户
     */
    public function users()
    {
        $data = array();

        $output = $this->template->loadPage("aclmanager/users.tpl", $data);

        $output = $this->administrator->box("<a href='" . pageURL . "admin/aclmanager'>用户组与权限管理</a> &rarr; 用户权限", $output);

        $this->administrator->view($output, "modules/admin/css/aclmanager.css", "modules/admin/js/users.js");
    }

    // --- 私有工具方法 ---

    /**
     * 获取所有角色
     */
    private function getAllRoles($group = 0)
    {
        $modules = [];
        $dangerLevel = [
            3 => "#A11500", // 所有者操作
            2 => "#DF5500", // 管理员操作
            1 => "#A11D73" // 管理员操作
        ];

        foreach (glob("application/modules/*") as $module) {
            if (is_dir($module)) {
                $data = file_get_contents($module . "/manifest.json");
                $manifest = json_decode($data, true);

                $module = preg_replace("/^application\/modules\//", "", $module);

                if (is_array($manifest)) {
                    $modules[$module]['name'] = (array_key_exists("name", $manifest)) ? $manifest['name'] : $module;
                    $modules[$module]['manifest'] = (array_key_exists("roles", $manifest)) ? $manifest['roles'] : false;

                    if ($modules[$module]['manifest']) {
                        foreach ($modules[$module]['manifest'] as $k => $v) {
                            if (array_key_exists("dangerLevel", $v) && array_key_exists($v['dangerLevel'], $dangerLevel)) {
                                $modules[$module]['manifest'][$k]['color'] = $dangerLevel[$v['dangerLevel']];
                            }
                        }
                    }

                    $modules[$module]['db'] = $this->acl_model->getRolesByModule($module, $group);
                }
            }
        }

        return $modules;
    }
}
