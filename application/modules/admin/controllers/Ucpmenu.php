<?php

use MX\MX_Controller;

/**
 * 菜单控制器类
 * @property ucpmenu_model $ucpmenu_model ucpmenu_model 类
 */
class Ucpmenu extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library("administrator");
        $this->load->model("ucpmenu_model");
        $this->load->model("menu_model");

        requirePermission("viewMenuLinks");
    }

    /**
     * 加载页面
     */
    public function index()
    {
        // 设置标题
        $this->administrator->setTitle("UCP菜单链接");

        $links = $this->ucpmenu_model->getMenuLinks();

        if ($links) {
            foreach ($links as $key => $value) {
                // 缩短链接
                if (strlen($value['link']) > 12) {
                    $links[$key]['link_short'] = mb_substr($value['link'], 0, 12) . '...';
                } else {
                    $links[$key]['link_short'] = $value['link'];
                }

                // 添加网站路径（内部链接）
                if (!preg_match("/https?:\/\//", $value['link'])) {
                    $links[$key]['link'] = $this->template->page_url . $value['link'];
                }

                $links[$key]['name'] = langColumn($links[$key]['name']);

                // 缩短名称
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
        $data = [
            'url' => $this->template->page_url,
            'links' => $links,
            'pages' => $pages
        ];

        // 加载视图
        $output = $this->template->loadPage('menu/ucp_menu.tpl', $data);

        // 将视图放入管理面板
        $content = $this->administrator->box('UCP菜单链接', $output);

        // 输出内容
        $this->administrator->view($content, false, 'modules/admin/js/ucp_menu.js');
    }

    public function create()
    {
        requirePermission('addMenuLinks');

        $name = $this->input->post('name');
        $link = $this->input->post('link');
        $icon = $this->input->post('icon');
        $group = $this->input->post('group');
        $permission = $this->input->post('permission');
        $permissionModule = $this->input->post('permissionModule');

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

        $this->ucpmenu_model->add($name, $link, $icon, $group, $permission, $permissionModule);

        die("yes");
    }

    public function delete($id)
    {
        requirePermission("deleteMenuLinks");

        if ($this->ucpmenu_model->delete($id)) {
            die("success");
        } else {
            die("删除菜单链接时发生错误");
        }
    }

    public function edit($id = false)
    {
        requirePermission("editMenuLinks");

        if (!is_numeric($id) || !$id) {
            die();
        }

        $link = $this->ucpmenu_model->getMenuLink($id);

        if (!$link) {
            show_error("找不到ID为 " . $id . " 的链接", 400);
        }

        // 设置标题
        $this->administrator->setTitle(langColumn($link['name']));

        if ($pages = $this->menu_model->getPages()) {
            foreach ($pages as $k => $v) {
                $pages[$k]['name'] = langColumn($v['name']);
            }
        }

        // 准备数据
        $data = [
            'url' => $this->template->page_url,
            'links' => $this->ucpmenu_model->getMenuLinks(),
            'link' => $link,
            'pages' => $pages
        ];

        // 加载视图
        $output = $this->template->loadPage("menu/edit_ucp_menu.tpl", $data);

        // 将视图放入管理面板
        $content = $this->administrator->box('<a href="' . $this->template->page_url . 'admin/ucpmenu">菜单链接</a> &rarr; ' . langColumn($link['name']), $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/ucp_menu.js");
    }

    public function move($id = false, $direction = false)
    {
        requirePermission("editMenuLinks");

        if (!$id || !$direction) {
            die();
        } else {
            $order = $this->ucpmenu_model->getOrder($id);

            if (!$order) {
                die();
            } else {
                if ($direction == "up") {
                    $target = $this->ucpmenu_model->getPreviousOrder($order);
                } else {
                    $target = $this->ucpmenu_model->getNextOrder($order);
                }

                if (!$target) {
                    die();
                } else {
                    $this->ucpmenu_model->setOrder($id, $target['order']);
                    $this->ucpmenu_model->setOrder($target['id'], $order);
                }
            }
        }
    }

    public function save($id = false)
    {
        requirePermission("editMenuLinks");

        if (!$id || !is_numeric($id)) {
            die();
        }

        $data['name'] = $this->input->post('name');
        $data['link'] = $this->input->post('link');
        $data['icon'] = $this->input->post('icon');
        $data['group'] = $this->input->post('group');
        $data['permission'] = $this->input->post('permission');
        $data['permissionModule'] = $this->input->post('permissionModule');

        $this->ucpmenu_model->edit($id, $data);

        die('yes');
    }
}
