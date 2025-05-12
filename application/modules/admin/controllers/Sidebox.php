<?php

use MX\MX_Controller;

/**
 * 侧边栏控制器类
 * @property sidebox_model $sidebox_model 侧边栏模型类
 */
class Sidebox extends MX_Controller
{
    private $sideboxModules;

    public function __construct()
    {
        // 加载管理员库
        $this->load->library('administrator');
        $this->load->model('sidebox_model');

        parent::__construct();

        // 验证侧边栏查看权限
        requirePermission("viewSideboxes");
    }

    public function index()
    {
        $this->sideboxModules = $this->getSideboxModules();

        // 设置页面标题
        $this->administrator->setTitle("侧边栏管理");

        $sideboxes = $this->sidebox_model->getSideboxes();

        if ($sideboxes)
        {
            foreach ($sideboxes as $key => $value)
            {
                // 本地化显示名称
                $sideboxes[$key]['name'] = $this->sideboxModules["sidebox_" . $value['type']]['name'];

                // 本地化显示名称
                $sideboxes[$key]['displayName'] = langColumn($sideboxes[$key]['displayName']);

                // 显示名称长度限制
                if (strlen($sideboxes[$key]['displayName']) > 15)
                {
                    $sideboxes[$key]['displayName'] = mb_substr($sideboxes[$key]['displayName'], 0, 15) . '...';
                }
            }
        }

        // 准备视图数据
        $data = array(
            'url' => $this->template->page_url,
            'sideboxes' => $sideboxes,
            'sideboxModules' => $this->sideboxModules
        );

        // 加载模板文件
        $output = $this->template->loadPage("sidebox/sidebox.tpl", $data);

        // 将内容放入管理面板
        $content = $this->administrator->box('侧边栏列表', $output);

        // 渲染最终页面
        $this->administrator->view($content, false, "modules/admin/js/sidebox.js");
    }

    private function getSideboxModules()
    {
        $sideboxes = array();

        $this->administrator->loadModules();

        foreach ($this->administrator->getModules() as $name => $manifest)
        {
            if (preg_match("/sidebox_/i", $name))
            {
                $sideboxes[$name] = $manifest;
            }
        }

        return $sideboxes;
    }

    public function create_submit()
    {
        // 验证添加侧边栏权限
        requirePermission('addSideboxes');

        // 准备侧边栏数据
        $data = [
            'type'        => preg_replace('/sidebox_/', '', $this->input->post('type')),
            'pages'       => $this->input->post('pages'),
            'location'    => $this->input->post('location'),
            'displayName' => $this->input->post('displayName')
        ];

        // 验证页面
        if(!$data['pages'] || !is_array($data['pages']))
            die('请选择至少一个页面.');

        // 格式化页面
        $data['pages'] = json_encode($data['pages'], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        // 验证显示名称
        if(!$data['displayName'])
            die('名称不能为空');

        // 添加侧边栏
        $id = $this->sidebox_model->add($data);

        // 设置侧边栏权限（如果需要）
        if($this->input->post('visibility') == 'group')
            $this->sidebox_model->setPermission($id);

        // 处理自定义侧边栏文本
        if($data['type'] == 'custom')
        {
            // 获取侧边栏文本
            $text = $this->input->post('content', false);

            // 验证文本
            if(!$text)
                die('内容不能为空');

            // 添加自定义侧边栏文本
            $this->sidebox_model->addCustom($text);
        }

        die('yes');
    }

    public function new()
    {
        // 验证编辑侧边栏权限
        requirePermission('editSideboxes');

        // 设置页面标题
        $this->administrator->setTitle('添加侧边栏');

        // 加载侧边栏模块
        $this->sideboxModules = $this->getSideboxModules();

        // 准备页面数据
        $data = array(
            'url'            => $this->template->page_url,
            'pages'          => self::getModules(),
            'sideboxModules' => $this->sideboxModules
        );

        // 加载页面模板
        $output = $this->template->loadPage('sidebox/add_sidebox.tpl', $data);

        // 将内容放入管理面板
        $content = $this->administrator->box('', $output);

        // 渲染最终页面
        $this->administrator->view($content, false, 'modules/admin/js/sidebox.js');
    }

    public function edit($id = false)
    {
        // 验证编辑侧边栏权限
        requirePermission('editSideboxes');

        // 验证ID
        if(!is_numeric($id) || !$id)
            die();

        // 获取侧边栏数据
        $sidebox           = $this->sidebox_model->getSidebox($id);
        $sideboxCustomText = $this->sidebox_model->getCustomText($id);

        // 验证侧边栏
        if(!$sidebox)
            show_error('没有找到ID为 ' . $id . ' 的侧边栏', 400);

        // 格式化页面
        $sidebox['pages'] = json_decode($sidebox['pages'], true);

        // 验证页面
        if(!$sidebox['pages'] || !is_array($sidebox['pages']))
            $sidebox['pages'] = [];

        // 设置页面标题
        $this->administrator->setTitle(langColumn($sidebox['displayName']));

        // 加载侧边栏模块
        $this->sideboxModules = $this->getSideboxModules();

        // 准备页面数据
        $data = array(
            'url'               => $this->template->page_url,
            'pages'             => self::getModules(),
            'sidebox'           => $sidebox,
            'sideboxModules'    => $this->sideboxModules,
            'sideboxCustomText' => $sideboxCustomText
        );

        // 加载页面模板
        $output = $this->template->loadPage('sidebox/edit_sidebox.tpl', $data);

        // 将内容放入管理面板
        $content = $this->administrator->box('', $output);

        // 渲染最终页面
        $this->administrator->view($content, false, 'modules/admin/js/sidebox.js');
    }

    public function move($id = false, $direction = false)
    {
        requirePermission("editSideboxes");

        if (!$id || !$direction)
        {
            die();
        } else {
            $order = $this->sidebox_model->getOrder($id);

            if (!$order)
            {
                die();
            } else {
                if ($direction == "up")
                {
                    $target = $this->sidebox_model->getPreviousOrder($order);
                } else {
                    $target = $this->sidebox_model->getNextOrder($order);
                }

                if (!$target)
                {
                    die();
                } else {
                    $this->sidebox_model->setOrder($id, $target['order']);
                    $this->sidebox_model->setOrder($target['id'], $order);
                }
            }
        }
    }

    public function save($id = false)
    {
        // 验证编辑侧边栏权限
        requirePermission('editSideboxes');

        // 验证ID
        if(!$id || !is_numeric($id))
            die('没有ID');

        // 准备侧边栏数据
        $data = [
            'type'        => preg_replace('/sidebox_/', '', $this->input->post('type')),
            'pages'       => $this->input->post('pages'),
            'location'    => $this->input->post('location'),
            'displayName' => $this->input->post('displayName'),
        ];

        // 验证页面
        if(!$data['pages'] || !is_array($data['pages']))
            die('请选择至少一个页面.');

        // 格式化页面
        $data['pages'] = json_encode($data['pages'], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        // 验证数据
        foreach($data as $value)
            if(!$value)
                die('字段不能为空');

        // 保存修改
        $this->sidebox_model->edit($id, $data);

        // 处理自定义侧边栏文本
        if($data['type'] == 'custom')
            $this->sidebox_model->editCustom($id, $this->input->post('content', false));

        // 检查侧边栏权限
        $hasPermission = $this->sidebox_model->hasPermission($id);

        // 设置侧边栏权限
        if($this->input->post('visibility') == 'group' && !$hasPermission)
        {
            $this->sidebox_model->setPermission($id);
        }
        elseif($this->input->post('visibility') != 'group' && $hasPermission)
        {
            $this->sidebox_model->deletePermission($id);
        }

        die('yes');
    }

    public function delete($id = false)
    {
        requirePermission("deleteSideboxes");

        if (!$id || !is_numeric($id))
        {
            die();
        }

        $this->sidebox_model->delete($id);
    }

    /**
     * 获取模块
     * 返回可用模块
     *
     * @return array $modules
     */
    private static function getModules()
    {
        // 模块：初始化
        $modules = [];

        // 黑名单：初始化
        $blacklist = ['admin', 'api', 'icon'];

        // 模块：获取
        if(!empty($modules = glob(realpath(APPPATH) . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR)))
        {
            // 循环模块
            foreach($modules as $key => $module)
            {
                // 获取模块名称
                $modules[$key] = basename($modules[$key]);

                // 过滤
                if(in_array($modules[$key], $blacklist) || strpos($modules[$key], 'sidebox_') === 0)
                    unset($modules[$key]);
            }
        }

        return $modules;
    }
}
