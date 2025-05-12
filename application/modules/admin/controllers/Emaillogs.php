<?php

use MX\MX_Controller;

/**
 * 邮件日志控制器类
 * @property dashboard_model $dashboard_model 仪表盘模型类
 */
class Emaillogs extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('dashboard_model');

        $this->load->library('administrator');
    }

    public function index()
    {
        $emaillogs = $this->dashboard_model->getEmailLogs();

        $data = array(
            'emaillogs' => $emaillogs
        );

        // 加载邮件日志模板
        $output = $this->template->loadPage("emaillogs.tpl", $data);

        // 将视图放入管理面板框中
        $content = $this->administrator->box('邮件日志', $output);

        // 渲染最终页面
        $this->administrator->view($content, false, false);
    }
}
