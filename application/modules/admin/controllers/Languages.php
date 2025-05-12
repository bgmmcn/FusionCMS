<?php

use MX\MX_Controller;

/**
 * 多语言管理控制器
 */
class Languages extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library("administrator");

        // 验证查看语言权限
        requirePermission("viewLanguages");
    }

    /**
     * 加载语言管理页面
     * @param bool $default 当前默认语言
     */
    public function index($default = false)
    {
        // 设置页面标题
        $this->administrator->setTitle("可用语言列表");

        // 准备视图数据
        $data = array(
            'languages' => $this->language->getAllLanguages(),
            'default' => ($default) ? $default : $this->config->item('language')
        );

        // 加载模板文件
        $output = $this->template->loadPage("languages/languages.tpl", $data);

        // 将内容放入管理面板容器
        $content = $this->administrator->box('系统支持语言', $output);

        // 渲染最终页面
        $this->administrator->view($content, false, "modules/admin/js/languages.js");
    }

    /**
     * 设置默认系统语言
     */
    public function set()
    {
        $language = $this->input->post('language');

        // 验证修改权限
        requirePermission("changeDefaultLanguage");

        // 加载配置编辑器
        require_once('application/libraries/ConfigEditor.php');

        if (!$language || !is_dir("application/language/" . $language)) {
            die("无效的语言");
        }

        $fusionConfig = new ConfigEditor("application/config/language.php");
        $fusionConfig->set("language", $language);
        $fusionConfig->save();

        die('成功');
    }
}
