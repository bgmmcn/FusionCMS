<?php

use MX\MX_Controller;

/**
 * Cache Management Controller Class
 * @property cache_model $cache_model Cache Model Class
 */
class Cachemanager extends MX_Controller
{
    private array $itemMatches;
    private array $minifyMatches;
    private array $websiteMatches;

    public function __construct()
    {
        // Make sure to load the administrator library!
        $this->load->library('administrator');

        $this->itemMatches = ["spells/*", "items/*", "search/*"];
        $this->minifyMatches = ["minify/*"];
        $this->websiteMatches = ["*.cache"];

        parent::__construct();

        requirePermission("viewCache");
    }

    /**
     * Display Cache Management Interface
     */
    public function index()
    {
        // Set page title
        $this->administrator->setTitle("Cache Management");

        // Prepare my data
        $data = array(
            'url' => $this->template->page_url
        );

        // Load my view template
        $output = $this->template->loadPage("cachemanager/cache.tpl", $data);

        // Output to admin panel
        $content = $this->administrator->box('Cache Management', $output);

        // Output my content
        $this->administrator->view($content, false, "modules/admin/js/cache.js");
    }

    /**
     * Get Cache Information
     */
    public function get()
    {
        $item = $this->countItemCache();
        $website = $this->countWebsiteCache();
        $theme = $this->countThemeMinifyCache();

        $total['files'] = $item['files'] + $website['files'] + $theme['files'];
        $total['size'] = $this->formatSize($item['size'] + $website['size'] + $theme['size']);

        // Prepare my data
        $data = [
            'url' => $this->template->page_url,
            'item' => $item,
            'website' => $website,
            'theme' => $theme,
            'total' => $total
        ];

        // Load my view template
        $output = $this->template->loadPage("cachemanager/cache_data.tpl", $data);

        die($output);
    }

    /**
     * Count Item Cache
     */
    private function countItemCache(): array
    {
        // Define my result
        $result = [
            "files" => 0,
            "size" => 0
        ];

        // Define what to search for
        $matches = $this->itemMatches;

        // Loop through all searches
        return $this->SearchCache($matches, $result);
    }

    /**
     * Count Theme Cache
     */
    private function countThemeMinifyCache(): array
    {
        // Define my result
        $result = [
            "files" => 0,
            "size" => 0
        ];

        // Define what to search for
        $matches = $this->minifyMatches;

        // Loop through all searches
        return $this->SearchCache($matches, $result);
    }

    /**
     * Count Website Cache
     */
    private function countWebsiteCache(): array
    {
        // Define my result
        $result = [
            "files" => 0,
            "size" => 0
        ];

        // Define what to search for
        $matches = $this->websiteMatches;

        // Loop through all searches
        return $this->SearchCache($matches, $result);
    }

    /**
     * Format Byte Size
     */
    private function formatSize($size): string
    {
        if ($size < 1024) {
            return $size . " B";
        } elseif ($size < 1024 * 1024) {
            return round($size / 1024) . " KB";
        } elseif ($size < 1024 * 1024 * 1024) {
            return round($size / (1024 * 1024)) . " MB";
        } else {
            return round($size / (1024 * 1024 * 1024)) . " GB";
        }
    }

    /**
     * Delete Specific Type of Cache
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

            die("success");
        }
    }

    /**
     * Search Cache
     */
    private function SearchCache(array $matches, array $result): array
    {
        foreach ($matches as $search) {
            // Search for matches
            $matches = glob("writable/cache/data/" . $search);

            if ($matches) {
                // Loop through all matches
                foreach ($matches as $file) {
                    if (!preg_match("/index\.html/", $file)) {
                        // Count and add size to result
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
