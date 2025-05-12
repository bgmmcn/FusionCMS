<?php

use MX\MX_Controller;

class Theme extends MX_Controller
{
    public function __construct()
    {
        // 确保加载管理员库！
        $this->load->library('administrator');

        parent::__construct();

        require_once('application/libraries/ConfigEditor.php');

        requirePermission("changeTheme");
    }

    public function index()
    {
        // 更改标题
        $this->administrator->setTitle("选择主题");

        // 准备数据
        $data = array(
            'url' => $this->template->page_url,
            'themes' => $this->getThemes(),
            'current_theme' => $this->config->item('theme')
        );

        // 加载视图
        $output = $this->template->loadPage("theme.tpl", $data);

        // 将视图放入带标题的主框中
        $content = $this->administrator->box('选择主题', $output);

        // 输出内容
        $this->administrator->view($content, "modules/admin/css/theme.css", "modules/admin/js/theme.js");
    }

    private function getThemes()
    {
        $themes = glob("application/themes/*");
        $themesArr = array();

        foreach ($themes as $value) {
            $value = preg_replace("/application\/themes\/([A-Za-z_-]*)/", "$1", $value);

            // 检查是否为目录
            if (!is_dir("application/themes/" . $value)) {
                continue;
            }

            if (file_exists("application/themes/" . $value . "/manifest.json")) {
                $manifest = json_decode(file_get_contents("application/themes/" . $value . "/manifest.json"), true);
                $manifest['folderName'] = $value;

				// 检查模块是否有配置
				if ($this->hasConfigs($value)) {
					$manifest['has_configs'] = true;
				} else {
					$manifest['has_configs'] = false;
				}

                $themesArr[] = $manifest;
            }
        }

        return $themesArr;
    }

    public function hasConfigs($theme)
    {
        if (file_exists("application/themes/" . $theme . "/config")) {
            return true;
        } else {
            return false;
        }
    }

    public function set($theme = false)
    {
        if (!$theme || !file_exists("application/themes/" . $theme)) {
            die('无效的主题');
        }

        $fusionConfig = new ConfigEditor("application/config/fusion.php");
        $fusionConfig->set('theme', $theme);
        $fusionConfig->save();

        $this->cache->delete('*.cache');
        $this->cache->delete('search/*.cache');
        $this->cache->delete('minify/*');

        die('成功');
    }
}
