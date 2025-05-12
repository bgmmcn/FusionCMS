<?php

use MX\MX_Controller;

class EditTheme extends MX_Controller
{
    private $theme;
    private $manifest;
    private $configs;

    public function __construct()
    {
        // 确保加载管理员库
        $this->load->library('administrator');

        parent::__construct();

        requirePermission("editModuleConfigs");

        require_once('application/libraries/ConfigEditor.php');
    }

    /**
     * 输出配置界面
     *
     * @param String $theme
     */
    public function index($theme = false)
    {
        // 验证主题是否存在且包含配置
        if (!$theme) {
            $theme = $this->config->item('theme');
        }
        // 再次验证主题有效性
        if (!file_exists("application/themes/" . $theme . "/")
            || !$this->hasConfigs($theme)
        ) {
            die('无效的主题配置');
        }

        $this->theme = $theme;

        $this->loadTheme();
        $this->loadConfigs();

        // 修改标题
        $this->administrator->setTitle($this->manifest['name']);

        $data = array(
            "configs" => $this->configs,
            "themeName" => $theme,
            "url" => $this->template->page_url
        );

        // 加载视图
        $output = $this->template->loadPage("config_theme.tpl", $data);

        // 将视图放入主框架中
        $content = $this->administrator->box('<a href="' . $this->template->page_url . 'admin/theme">主题</a> &rarr; 编辑配置 ' . $this->manifest['name'], $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/settings.js");
    }

    /**
     * 加载主题清单
     */
    private function loadTheme()
    {
        $this->manifest = @file_get_contents("application/themes/" . $this->theme . "/manifest.json");

        if (!$this->manifest) {
            die("主题 <b>" . $this->theme . "</b> 缺少 manifest.json 文件");
        } else {
            $this->manifest = json_decode($this->manifest, true);

            // 如果主题名称未指定，则使用主题文件夹名称作为名称
            if (!array_key_exists("name", $this->manifest)) {
                $this->manifest['name'] = ucfirst($this->theme);
            }
        }
    }

    /**
     * 加载主题配置
     */
    private function loadConfigs()
    {
        foreach (glob("application/themes/" . $this->theme . "/config/*") as $file) {
            if ($file == 'application/themes/' . $this->theme . '/config/template_vars.php' || $file == 'application/themes/' . $this->theme . '/config/base.php' ||
			    $file == 'application/themes/' . $this->theme . '/config/template_assets.php' || $file == 'application/themes/' . $this->theme . '/config/template_functions.php')
                continue;

            $this->getConfig($file);
        }
    }

    /**
     * 加载配置文件并将其赋值给 configs 数组
     */
    private function getConfig($file)
    {
        include($file);

        // 跳过不需要显示的配置文件
        if(isset($config) && isset($config['force_hidden']) && $config['force_hidden'])
            return;

        $this->configs[$this->getConfigName($file)] = $config;
        $this->configs[$this->getConfigName($file)]['source'] = $this->getConfigSource($file);
    }

    private function getConfigSource($file)
    {
        $handle = fopen($file, "r");
        $data = fread($handle, filesize($file));
        fclose($handle);

        return $data;
    }

    /**
     * 获取配置名称
     *
     * @param  String $path
     * @return String
     */
    private function getConfigName($path = "")
    {
        return preg_replace("/application\/themes\/" . $this->theme . "\/config\/([A-Za-z0-9_-]*)\.php/", "$1", $path);
    }

    public function save($theme = false, $name = false)
    {
        if (!$name || !$theme || !$this->configExists($theme, $name)) {
            die("无效的主题或配置名称");
        } else {
            if ($this->input->post()) {
                $fusionConfig = new ConfigEditor("application/themes/" . $theme . "/config/" . $name . ".php");

                foreach ($this->input->post() as $key => $value) {
                    $fusionConfig->set($key, $value);
                }

                $fusionConfig->save();

                die("yes");
            } else {
                die("没有数据需要保存");
            }
        }
    }

    public function saveSource($theme = false, $name = false)
    {
        if (!$name || !$theme || !$this->configExists($theme, $name)) {
            die("无效的主题或配置名称");
        } else {
            if ($this->input->post("source")) {
                $file = fopen("application/themes/" . $theme . "/config/" . $name . ".php", "w");
                fwrite($file, $this->input->post("source"));
                fclose($file);

                $file = file("application/themes/" . $theme . "/config/" . $name . ".php");
                $file[0] = str_replace("&lt;", "<", $file[0]);
                file_put_contents("application/themes/" . $theme . "/config/" . $name . ".php", $file);

                die("yes");
            } else {
                die("没有数据需要保存");
            }
        }
    }

    private function configExists($theme, $file)
    {
        if (file_exists("application/themes/" . $theme . "/config/" . $file . ".php")) {
            return true;
        } else {
            return false;
        }
    }

    public function hasConfigs($theme)
    {
        if (file_exists("application/themes/" . $theme . "/config")) {
            return true;
        } else {
            return false;
        }
    }
}
