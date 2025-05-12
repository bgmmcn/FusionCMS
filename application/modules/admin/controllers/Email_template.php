<?php

defined('BASEPATH') or exit('禁止直接访问');

use MX\MX_Controller;

/**
 * 邮件模板控制器类
 * @property email_template_model $email_template_model 邮件模板模型类
 */
class Email_template extends MX_Controller
{
    public function __construct()
    {
        // 确保加载管理员库
        $this->load->library('administrator');
        $this->load->model('email_template_model');
        $this->load->helper('file');

        parent::__construct();

        requirePermission("查看备份");
    }

    public function index()
    {
        // 设置页面标题
        $this->administrator->setTitle("邮件模板管理");

        $templates = $this->email_template_model->getTemplates();

        // 准备模板数据
        $data = array(
            'url' => $this->template->page_url,
            'templates' => $templates,
        );

        // 加载视图模板
        $output = $this->template->loadPage("email_template/index.tpl", $data);

        // 将视图放入带标题的主框中
        $content = $this->administrator->box('邮件模板列表', $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/email_template.js");
    }

    public function edit($id)
    {
        // 设置页面标题
        $this->administrator->setTitle("编辑模板");

        if (!$id || !is_numeric($id)) {
            header('Location: ' . pageURL . 'admin/email_templates');
            die();
        }

        $template = $this->email_template_model->getTemplate($id);
        $content = read_file(APPPATH . '/views/email_templates/' . $template['template_name'] . '');

        // 准备编辑数据
        $data = array(
            'url' => $this->template->page_url,
            'content' => $content,
            'template' => $template
        );

        // 加载编辑视图
        $output = $this->template->loadPage("email_template/edit.tpl", $data);

        // 将视图放入带标题的主框中
        $content = $this->administrator->box('编辑邮件模板', $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/email_template.js");
    }

    public function save($id)
    {
        $content = $this->input->post('code', false);
        $template = $this->email_template_model->getTemplate($id);

        if (empty($content)) {
            die("模板内容不能为空！");
        }

        if (!write_file(APPPATH . '/views/email_templates/' . $template['template_name'] . '', $content)) {
            die('文件写入失败');
        } else {
            die('yes');
        }
    }
}
