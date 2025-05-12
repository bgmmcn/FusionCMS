<?php

use MX\MX_Controller;

class Message extends MX_Controller
{
    public function __construct()
    {
        // 确保加载管理后台库
        $this->load->library('administrator');

        // 加载配置编辑器
        require_once('application/libraries/ConfigEditor.php');

        parent::__construct();

        // 验证维护消息查看权限
        requirePermission("viewMessage");
    }

    public function index()
    {
        // 设置页面标题
        $this->administrator->setTitle("全局维护设置");

        // 准备配置数据
        $data = array(
            'url' => $this->template->page_url,
            'message_enabled' => $this->config->item('message_enabled'), // 维护模式开关
            'message_headline' => $this->config->item('message_headline'), // 维护标题
            'message_headline_size' => $this->config->item('message_headline_size'), // 标题字号
            'message_text' => $this->config->item('message_text') // 维护详细内容
        );

        // 加载消息模板
        $output = $this->template->loadPage("message.tpl", $data);

        // 将内容放入管理面板
        $content = $this->administrator->box('全局维护设置', $output);

        // 渲染页面并加载资源
        $this->administrator->view($content, "modules/admin/css/message.css", "modules/admin/js/settings.js");
    }

    public function save()
    {
        // 验证修改权限
        requirePermission("editMessage");

        $fusionConfig = new ConfigEditor("application/config/message.php");

        // 处理多语言标题
        $headline = array();
        foreach (config_item('languages') as $key => $value) {
            $headline[$key] = $this->input->post($key.'_headline');
        }

        // 更新配置项
        $fusionConfig->set('message_enabled', $this->input->post('message_enabled'));
        $fusionConfig->set('message_headline', $headline);
        $fusionConfig->set('message_headline_size', $this->input->post('message_headline_size'));
        $fusionConfig->set('message_text', $this->input->post('message_text'));

        // 保存配置
        $fusionConfig->save();

        die("维护设置已保存！");
    }
}
