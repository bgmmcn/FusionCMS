<?php

use MX\MX_Controller;

class Edit extends MX_Controller
{
    private $module;
    private $manifest;
    private $configs;

    public function __construct()
    {
        // 确保加载管理员库！
        $this->load->library('administrator');

        parent::__construct();

        requirePermission("editModuleConfigs");

        require_once('application/libraries/ConfigEditor.php');
    }

    /**
     * 输出配置
     *
     * @param String $module 模块名称
     */
    public function index($module = false)
    {
        // 确保模块存在且有配置
        if (
            !$module
            || !file_exists("application/modules/" . $module . "/")
            || !$this->administrator->hasConfigs($module)
        ) {
            die();
        }

        $this->module = $module;

        $this->loadModule();
        $this->loadConfigs();

        // 修改标题
        $this->administrator->setTitle($this->manifest['name']);

        $data = array(
            "configs" => $this->configs,
            "moduleName" => $module,
            "url" => $this->template->page_url
        );

        // 加载视图模板
        $output = $this->template->loadPage("config.tpl", $data);

        // 将视图放入带标题的主框中
        $content = $this->administrator->box('<a href="' . $this->template->page_url . 'admin/modules">模块管理</a> &rarr; 编辑 &rarr; ' . $this->manifest['name'], $output);

        // 输出内容（参数与template->view相同）
        $this->administrator->view($content, false, "modules/admin/js/settings.js");
    }

    /**
     * 加载模块清单
     */
    private function loadModule()
    {
        $this->manifest = @file_get_contents("application/modules/" . $this->module . "/manifest.json");

        if (!$this->manifest) {
            die("模块 <b>" . $this->module . "</b> 缺少manifest.json文件");
        } else {
            $this->manifest = json_decode($this->manifest, true);

            // 如果未指定名称，使用模块文件夹名
            if (!array_key_exists("name", $this->manifest)) {
                $this->manifest['name'] = ucfirst($this->module);
            }
        }
    }

    /**
     * 加载模块配置
     */
    private function loadConfigs()
    {
        $configPath = "application/modules/{$this->module}/config/";

        foreach (glob("{$configPath}*") as $file) {
            if (in_array(basename($file), ['routes.php', 'Event.php'])) {
                continue;
            }

            $this->getConfig($file);
        }
    }

    /**
     * 将配置加载到函数作用域并分配到配置数组
     */
    private function getConfig($file)
    {
        include($file);

        // 跳过强制隐藏的配置
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
     * 从路径获取配置名称
     *
     * @param  String $path 文件路径
     * @return String 配置名称
     */
    private function getConfigName($path = "")
    {
        return preg_replace("/application\/modules\/" . $this->module . "\/config\/([A-Za-z0-9_-]*)\.php/", "$1", $path);
    }

    public function save($module = false, $name = false)
    {
        if (!$name || !$module || !$this->configExists($module, $name)) {
            die("无效的模块或配置名称");
        } else {
            if ($this->input->post()) {
                $fusionConfig = new ConfigEditor("application/modules/" . $module . "/config/" . $name . ".php");

                foreach ($this->input->post() as $key => $value) {
                    $fusionConfig->set($key, $value);
                }

                $fusionConfig->save();

                die("yes");
            } else {
                die("没有要设置的数据");
            }
        }
    }

    public function saveSource($module = false, $name = false)
    {
        if (!$name || !$module || !$this->configExists($module, $name)) {
            die("无效的模块或配置名称");
        } else {
            if ($this->input->post("source")) {
                $file = fopen("application/modules/" . $module . "/config/" . $name . ".php", "w");
                fwrite($file, $this->input->post("source"));
                fclose($file);

                $file = file("application/modules/" . $module . "/config/" . $name . ".php");
                $file[0] = str_replace("&lt;", "<", $file[0]);
                file_put_contents("application/modules/" . $module . "/config/" . $name . ".php", $file);

                die("yes");
            } else {
                die("没有要设置的数据");
            }
        }
    }

    /**
     * 检查配置是否存在
     */
    private function configExists($module, $file)
    {
        if (file_exists("application/modules/" . $module . "/config/" . $file . ".php")) {
            return true;
        } else {
            return false;
        }
    }
}
