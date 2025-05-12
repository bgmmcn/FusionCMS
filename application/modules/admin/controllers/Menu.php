<?php

use MX\MX_Controller;

/**
 * 菜单管理控制器
 * @property menu_model $menu_model 菜单模型类
 */
class Menu extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library("administrator");
        $this->load->model("menu_model");

        // 验证菜单查看权限
        requirePermission("viewMenuLinks");
    }

    /**
     * 加载菜单管理页面
     */
    public function index()
    {
        // 设置页面标题
        $this->administrator->setTitle("菜单链接管理");

        $links = $this->menu_model->getMenuLinks();

        if ($links) {
            foreach ($links as $key => $value) {
                // 缩短长链接显示
                if (strlen($value['link']) > 12) {
                    $links[$key]['link_short'] = mb_substr($value['link'], 0, 12) . '...';
                } else {
                    $links[$key]['link_short'] = $value['link'];
                }

                // 为内部链接添加网站路径前缀
                if (!preg_match("/https?:\/\//", $value['link'])) {
                    $links[$key]['link'] = $this->template->page_url . $value['link'];
                }

                // 加载多语言名称
                $links[$key]['name'] = langColumn($links[$key]['name']);

                // 缩短长名称显示
                if (strlen($links[$key]['name']) > 15) {
                    $links[$key]['name'] = mb_substr($links[$key]['name'], 0, 15) . '...';
                }
            }
        }

        if ($pages = $this->menu_model->getPages()) {
            foreach ($pages as $k => $v) {
                $pages[$k]['name'] = langColumn($v['name']);
            }
        }

        // 准备数据
        $data = array(
            'url' => $this->template->page_url,
            'links' => $links,
            'pages' => $pages
        );

        // 加载视图
        $output = $this->template->loadPage("menu/menu.tpl", $data);

        // 将视图放入主框架中
        $content = $this->administrator->box('菜单链接管理', $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/menu.js");
    }

    public function create()
    {
        // 验证添加菜单权限
        requirePermission("addMenuLinks");

        $name = $this->input->post('name');
        $link = $this->input->post('link');
        $type = $this->input->post('type');
        $side = $this->input->post('side');
        $dropdown = $this->input->post('dropdown');
        $parent_id = $this->input->post('parent_id');

        $array = json_decode($name, true);

        foreach ($array as $key => $value)
        {
            if (empty($value))
            {
                die("$key 名称不能为空");
            }
        }

        if (empty($link)) {
            die("链接不能为空");
        }

        $id = $this->menu_model->add($name, $link, $type, $side, $dropdown, $parent_id);

        if ($this->input->post('visibility') == "group") {
            $this->menu_model->setPermission($id);
        }

        die("yes");
    }

    public function delete($id)
    {
        // 验证删除菜单权限
        requirePermission("deleteMenuLinks");

        if ($this->menu_model->delete($id)) {
            die("success");
        } else {
            die("删除菜单失败");
        }
    }

    public function edit($id = false)
    {
        // 验证编辑菜单权限
        requirePermission("editMenuLinks");

        if (!is_numeric($id) || !$id) {
            die();
        }

        $link = $this->menu_model->getMenuLink($id);

        if (!$link) {
            show_error("菜单不存在", 400);

            die();
        }

        // 设置页面标题
        $this->administrator->setTitle(langColumn($link['name']));

        // 准备数据
        $data = array(
            'url' => $this->template->page_url,
            'links' => $this->menu_model->getMenuLinks(),
            'link' => $link
        );

        // 加载视图
        $output = $this->template->loadPage("menu/edit_menu.tpl", $data);

        // 将视图放入主框架中
        $content = $this->administrator->box('<a href="' . $this->template->page_url . 'admin/menu">菜单链接管理</a> &rarr; ' . langColumn($link['name']), $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/menu.js");
    }

    public function move($id = false, $direction = false)
    {
        // 验证编辑菜单权限
        requirePermission("editMenuLinks");

        if (!$id || !$direction) {
            die();
        } else {
            $order = $this->menu_model->getOrder($id);

            if (!$order) {
                die();
            } else {
                if ($direction == "up") {
                    $target = $this->menu_model->getPreviousOrder($order);
                } else {
                    $target = $this->menu_model->getNextOrder($order);
                }

                if (!$target) {
                    die();
                } else {
                    $this->menu_model->setOrder($id, $target['order']);
                    $this->menu_model->setOrder($target['id'], $order);
                }
            }
        }
    }

    public function save($id = false)
    {
        // 验证编辑菜单权限
        requirePermission("editMenuLinks");

        if (!$id || !is_numeric($id)) {
            die();
        }

        $data['name'] = $this->input->post('name');
        $data['link'] = $this->input->post('link');
        $data['type'] = $this->input->post('type');
        $data['side'] = $this->input->post('side');
        $data['dropdown'] = $this->input->post('dropdown');
        $data['parent_id'] = $this->input->post('parent_id');

        $this->menu_model->edit($id, $data);

        $hasPermission = $this->menu_model->hasPermission($id);

        if ($this->input->post('visibility') == "group" && !$hasPermission) {
            $this->menu_model->setPermission($id);
        } elseif ($this->input->post('visibility') != "group" && $hasPermission) {
            $this->menu_model->deletePermission($id);
        }

        die('yes');
    }
}
