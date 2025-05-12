<?php

use MX\MX_Controller;

/**
 * 缓存管理控制器类
 * @property cache_model $cache_model 缓存模型类
 */
class Cachemanager extends MX_Controller
{
    private array $itemMatches;
    private array $minifyMatches;
    private array $websiteMatches;

    public function __construct()
    {
        // 确保加载管理员库！
        $this->load->library('administrator');

        $this->itemMatches = ["spells/*", "items/*", "search/*"];
        $this->minifyMatches = ["minify/*"];
        $this->websiteMatches = ["*.cache"];

        parent::__construct();

        requirePermission("viewCache");
    }

    /**
     * 显示缓存管理界面
     */
    public function index()
    {
        // 设置页面标题
        $this->administrator->setTitle("缓存管理");

        // 准备数据
        $data = array(
            'url' => $this->template->page_url
        );

        // 加载视图模板
        $output = $this->template->loadPage("cachemanager/cache.tpl", $data);

        // 输出到管理面板
        $content = $this->administrator->box('缓存管理', $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/cache.js");
    }

    /**
     * 获取缓存信息
     */
    public function get()
    {
        $item = $this->countItemCache();
        $website = $this->countWebsiteCache();
        $theme = $this->countThemeMinifyCache();

        $total['files'] = $item['files'] + $website['files'] + $theme['files'];
        $total['size'] = $this->formatSize($item['size'] + $website['size'] + $theme['size']);

        // 准备数据
        $data = [
            'url' => $this->template->page_url,
            'item' => $item,
            'website' => $website,
            'theme' => $theme,
            'total' => $total
        ];

        // 加载视图模板
        $output = $this->template->loadPage("cachemanager/cache_data.tpl", $data);

        die($output);
    }

    /**
     * 计算物品缓存
     */
    private function countItemCache(): array
    {
        // 定义结果
        $result = [
            "files" => 0,
            "size" => 0
        ];

        // 定义搜索条件
        $matches = $this->itemMatches;

        // 循环搜索
        return $this->SearchCache($matches, $result);
    }

    /**
     * 计算主题缓存
     */
    private function countThemeMinifyCache(): array
    {
        // 定义结果
        $result = [
            "files" => 0,
            "size" => 0
        ];

        // 定义搜索条件
        $matches = $this->minifyMatches;

        // 循环搜索
        return $this->SearchCache($matches, $result);
    }

    /**
     * 计算网站缓存
     */
    private function countWebsiteCache(): array
    {
        // 定义结果
        $result = [
            "files" => 0,
            "size" => 0
        ];

        // 定义搜索条件
        $matches = $this->websiteMatches;

        // 循环搜索
        return $this->SearchCache($matches, $result);
    }

    /**
     * 格式化字节大小
     */
    private function formatSize($size): string
    {
        if ($size < 1024) {
            return $size . " 字节";
        } elseif ($size < 1024 * 1024) {
            return round($size / 1024) . " KB";
        } elseif ($size < 1024 * 1024 * 1024) {
            return round($size / (1024 * 1024)) . " MB";
        } else {
            return round($size / (1024 * 1024 * 1024)) . " GB";
        }
    }

    /**
     * 删除特定类型缓存
     */
    public function delete($type = false)
    {
        requirePermission("emptyCache");

        if (!in_array($type, ['item', 'website', 'theme', 'all'])) {
            die();
        } else {
            switch ($type) {
                case "item":
                    foreach ($this->itemMatches as $match) {
                        $this->cache->delete($match);
                    }
                    break;

                case "website":
                    foreach ($this->websiteMatches as $match) {
                        $this->cache->delete($match);
                    }
                    break;

                case "theme":
                    foreach ($this->minifyMatches as $match) {
                        $this->cache->delete($match);
                    }
                    break;

                case "all":
                    foreach ($this->itemMatches as $match) {
                        $this->cache->delete($match);
                    }
                    foreach ($this->websiteMatches as $match) {
                        $this->cache->delete($match);
                    }
                    foreach ($this->minifyMatches as $match) {
                        $this->cache->delete($match);
                    }
                    break;
            }

            die("删除成功");
        }
    }

    /**
     * 搜索缓存
     */
    private function SearchCache(array $matches, array $result): array
    {
        foreach ($matches as $search) {
            // 搜索匹配
            $matches = glob("writable/cache/data/" . $search);

            if ($matches) {
                // 循环匹配
                foreach ($matches as $file) {
                    if (!preg_match("/index\.html/", $file)) {
                        // 计数和添加大小到结果
                        $result['files']++;
                        $result['size'] += filesize($file);
                    }
                }
            }
        }

        $result['sizeString'] = $this->formatSize($result['size']);

        return $result;
    }
}
