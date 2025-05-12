<?php defined('BASEPATH') OR exit('禁止直接访问脚本');

# 导入所需类
use App\Config\Services;
use \VisualAppeal\AutoUpdate;
use MX\MX_Controller;

/**
 * 更新器
 *
 * @package    FusionCMS
 * @subpackage admin/updater
 * @since      1.0.0
 * @version    1.0.0
 * @author     Ehsan Zare (Darksider) <darksider.legend@gmail.com>
 * @author     Keramat Jokar (Nightprince) <https://github.com/Nightprince>
 * @link       https://code-path.com
 * @copyright  (c) 2023 Code-path 网页开发团队
 */

class Updater extends MX_Controller
{
    # 目录分隔符快捷方式
    const DS = DIRECTORY_SEPARATOR;

    # CMS版本
    private $version;

    # 日志路径
    private $log_path;

    # 日志文件
    private $log_file;

    # 缓存路径
    private $cache_path;

    # 缓存文件
    private $cache_file;

    # 更新路径
    private $update_path;

    # 解压路径
    private $extract_path;

    # GitHub发布URL
    private $releases_url;

    # 资产文件扩展名
    private $asset_file_ext;

    public function __construct()
    {
        # 调用`MX_Controller`构造函数
        parent::__construct();

        # 确保用户具有所需权限
        requirePermission('updateCms');

        # 加载库
        $this->load->library('administrator');

        # CMS版本
        $this->version = $this->administrator->getVersion();

        # 日志路径
        $this->log_path = (($this->config->item('log_path') && realpath($this->config->item('log_path'))) ? realpath($this->config->item('log_path')) : realpath(WRITEPATH) . self::DS . 'logs') . self::DS;

        # 日志文件
        $this->log_file = 'update-{DATE}.log';

        # 缓存路径
        $this->cache_path = (($this->config->item('cache_path') && realpath($this->config->item('cache_path'))) ? realpath($this->config->item('cache_path')) : realpath(WRITEPATH) . self::DS . 'cache') . self::DS . 'data' . self::DS . 'update' . self::DS;

        # 缓存文件
        $this->cache_file = 'assets.json';

        # 更新路径
        $this->update_path = realpath(FCPATH) . self::DS;

        # 解压路径
        $this->extract_path = $this->update_path . 'temp';

        # GitHub发布URL
        $this->releases_url = 'https://api.github.com/repos/FusionWowCMS/FusionCMS/releases?page={PAGE}&per_page={PER_PAGE}';

        # 资产文件扩展名
        $this->asset_file_ext = '.zip';

        # 创建所需路径（如果不存在）
        foreach([$this->log_path, $this->cache_path] as $path)
        {
            if(!file_exists($path))
            {
                try
                {
                    mkdir($path);
                    chmod($path, 0755);
                }
                catch(Error | Exception $e)
                {
                    show_error($e->getMessage());
                }
            }
        }
    }

    public function index()
    {
        // 设置页面标题
        $this->administrator->setTitle('更新器');

        // 获取最后更新日志
        ($log = $this->dblogger->getLogs('updater', 0, 1)) ? $log = reset($log) : $log = ['time' => false];

        // 格式化最后更新时间
        if($log['time']) $log['time'] = date('Y-m-d H:i:s', $log['time']);

        // 准备数据
        $data = [
            # 通用
            'url'             => $this->template->page_url,

            # 发布URL
            'releases_url'    => str_replace(['api.github.com', 'github.com/repos/'], ['github.com', 'github.com/'], substr($this->releases_url, 0, strrpos($this->releases_url, '?'))),

            # 最后更新
            'last_updated'    => $log['time'],

            # 日志
            'logs'            => $this->logs(),

            # 响应
            'response'        => $this->check(),

            # 服务器信息
            'server_modules'  => function_exists('apache_get_modules') ? apache_get_modules() : false,
            'server_software' => $_SERVER['SERVER_SOFTWARE'],

            # PHP信息
            'php_version'     => phpversion(),
            'php_extensions'  => get_loaded_extensions(),

            # 框架版本
            'ci_version'      => CI_VERSION,
            'cms_version'     => $this->version,
            'smarty_version'  => $this->smarty::SMARTY_VERSION
        ];

        // 附加最后检查
        $data['last_checked'] = date('Y-m-d H:i:s', filemtime($this->cache_path . $this->cache_file));

        // 渲染页面
        $this->administrator->view($this->administrator->box('更新器', $this->template->loadPage('updater.tpl', $data)), false, 'modules/admin/js/updater.js');
    }

    /**
     * 日志
     * 获取更新日志
     *
     * @return array $logs
     */
    public function logs()
    {
        // 初始化日志以稍后烘焙它们
        $logs = [];

        // 获取日志文件
        $files = glob($this->log_path . str_replace('{DATE}', '*', $this->log_file), GLOB_BRACE);

        // 爆炸日志文件名
        $log_file_name_arr = explode('{DATE}', $this->log_file);

        // 循环文件
        foreach($files as $file)
        {
            // 导出文件名
            $fileName = pathinfo(basename($file), PATHINFO_FILENAME);

            // 从文件名导出文件日期
            $fileDate = str_replace($log_file_name_arr, '', $fileName);

            // 打开日志文件
            $log = fopen($file, 'r');

            // 附加日志文件
            $logs[$fileDate] = fread($log, filesize($file));

            // 关闭日志文件
            fclose($log);
        }

        // 获取GET数据
        $today = $this->input->get('today', TRUE);

        // 丢弃今天的日志
        if($today)
            die(isset($logs[date('Y-m-d')]) ? $logs[date('Y-m-d')] : '');

        // 排序日志
        uksort($logs, function($a, $b) {
            return strtotime($a) <=> strtotime($b);
        });

        return $logs;
    }

    /**
     * 更新
     * 安装更新包
     *
     * @return void
     */
    public function update()
    {
        // 确保这是一个AJAX请求
        if(!$this->input->is_ajax_request())
            exit('禁止直接访问脚本');

        // 检查更新
        $response = $this->check();

        // 跟踪更新标志
        $response['updated'] = '0';

        // 确保更新可用
        if(!$response['available'])
            die(json_encode($response));

        // 格式化更新服务器URL
        $update_url = rtrim(str_replace([self::DS, '\\', '/', realpath(APPPATH)], ['/', '/', '/', base_url() . basename(APPPATH)], $this->cache_path), '/');

        // 格式化包数组
        array_walk($response['packages'], function(&$v) {
            $v = $v['asset']['browser_download_url'];
        });

        // 生成update.json
        $json = fopen($this->cache_path . 'update.json', 'w');

        // 写入update.json
        fwrite($json, json_encode($response['packages'], JSON_PRETTY_PRINT));

        // 关闭update.json
        fclose($json);

        ####################################################################################################
        ########################################## 开始更新 ##########################################
        ####################################################################################################

        // 创建解压路径（如果不存在）
        if(!file_exists($this->extract_path))
        {
            try
            {
                mkdir($this->extract_path);
                chmod($this->extract_path, 0755);
            }
            catch(Error | Exception $e)
            {
                // 设置消息
                $response['message'] = $e->getMessage();

                // 抛出响应
                die(json_encode($response));
            }
        }

        // 删除解压路径（我们不再需要它们）
        register_shutdown_function(function() { $this->removeDir($this->extract_path); });

        // 创建一个新的`AutoUpdate`对象
        $update = new AutoUpdate($this->extract_path, $this->update_path, 60);

        // `AutoUpdate`设置当前版本
        $update->setCurrentVersion($this->version);

        // `AutoUpdate`设置更新URL
        $update->setUpdateUrl($update_url);

        // 创建一个新的`Logger`对象
        $logger = new \Monolog\Logger('default');

        // `Logger`设置推送处理程序
        $logger->pushHandler((new Monolog\Handler\StreamHandler($this->log_path . str_replace('{DATE}', date('Y-m-d'), $this->log_file)))->setFormatter(new Monolog\Formatter\LineFormatter("[%datetime%] %channel%.%level_name%: %message% \n", "Y-m-d|H:i:s")));

        // `AutoUpdate`设置记录器
        $update->setLogger($logger);

        // 检查是否有新更新
        if($update->checkUpdate() === false)
        {
            // 设置消息
            $response['message'] = '无法检查更新！详情请查看日志文件。';

            // 抛出响应
            die(json_encode($response));
        }

        // 应用程序已经是最新的
        if(!$update->newVersionAvailable())
        {
            // 设置消息
            $response['message'] = '当前版本已是最新。';

            // 抛出响应
            die(json_encode($response));
        }

        // 设置错误处理程序
        set_error_handler(function() {});

        try
        {
            // 模拟更新
            $result = $update->update(true);

            // 检查错误
            if($result !== true)
            {
                // 设置消息
                $response['message'] = '更新模拟失败：' . $result . '!';

                // 附加更多数据到消息
                if(AutoUpdate::ERROR_SIMULATE && $update->getSimulationResults())
                {
                    $response['message'] = $response['message'] . '<br />';
                    $response['message'] = $response['message'] . '<pre>';
                    $response['message'] = $response['message'] . var_dump($update->getSimulationResults());
                    $response['message'] = $response['message'] . '</pre>';
                }

                // 抛出响应
                die(json_encode($response));
            }
        }
        catch(Error | Exception $e)
        {
            // 设置消息
            $response['message'] = $e->getMessage();

            // 抛出响应
            die(json_encode($response));
        }

        // 恢复错误处理程序
        restore_error_handler();

        // 回调每个版本更新
        $update->onEachUpdateFinish(function($version) use($logger) { $this->updateCallback($version, $logger); });

        // 回调所有版本更新
        $update->setOnAllUpdateFinishCallbacks(function($versions) use($logger) { $this->updateFinishCallback($versions, $logger); });

        // 最终应用更新
        $result = $update->update(false);

        // 检查可能的错误（网络）
        if($result !== true)
        {
            // 设置消息
            $response['message'] = '更新失败：' . $result . '!';

            // 抛出响应
            die(json_encode($response));
        }

        // 设置更新标志
        $response['updated'] = '1';

        // 设置消息
        $response['message'] = '更新成功。';

        // 抛出响应
        die(json_encode($response));
    }

    /**
     * 更新回调
     * 回调每个版本更新
     *
     * @param  string $version
     * @param  object $logger
     * @return void
     */
    private function updateCallback($version, $logger)
    {
        $this->insertSQL($logger);
        $this->removeFiles($logger);
        $this->saveLastUpdate($version);
    }

    /**
     * 更新完成回调
     * 回调所有版本更新
     *
     * @param  array  $versions
     * @param  object $logger
     * @return void
     */
    private function updateFinishCallback($versions, $logger)
    {
    }

    /**
     * 插入SQL
     * 执行更新SQL查询
     *
     * @param  object $logger
     * @return void
     */
    private function insertSQL($logger)
    {
        // 查找SQL文件
        $files = array_merge(glob($this->extract_path . self::DS . '*.sql', GLOB_BRACE), glob($this->extract_path . self::DS . '*' . self::DS . '*.sql', GLOB_BRACE));

        // 循环文件
        foreach($files as $file)
        {
            // 记录器
            $logger->debug(sprintf('插入 "%s"', $file));

            // 读取SQL文件
            $lines = file($file);

            // 初始化语句
            $statement = '';

            // 循环行
            foreach($lines as $line)
            {
                // 附加行到语句
                $statement .= $line;

                // 分号找到！做魔法...
                if(substr(trim($line), -1) === ';')
                {
                    // 运行查询
                    $res = $this->db->simple_query($statement);

                    // 记录器
                    ($res) ? $logger->notice(sprintf('插入 "%s" 成功', $file)) : $logger->error(sprintf('失败插入： "%s"', $file));

                    // 重置语句
                    $statement = '';
                }
            }

            // 删除文件
            unlink($file);
        }
    }

    /**
     * 删除文件
     * 检查更新是否需要删除文件
     *
     * @param  object $logger
     * @return void
     */
    private function removeFiles($logger)
    {
        // remove.txt文件路径
        $file = $this->extract_path . self::DS . 'remove.txt';

        // 确保remove.txt存在
        if(!file_exists($file))
            return;

        // 读取remove.txt文件
        $lines = file($file);

        // 循环行
        foreach($lines as $line)
        {
            // 附加根目录
            $line = realpath($this->update_path . str_replace(['\\', '/'], self::DS, trim($line)));

            // 确保这是一个有效路径
            if(!$line || strpos($line, $this->update_path) === false)
                continue;

            // 记录器
            $logger->debug(sprintf('尝试删除 "%s"', $line));

            (is_dir($line)) ? $this->removeDir($line) : unlink($line);

            // 记录器
            (file_exists($line)) ? $logger->error(sprintf('失败删除： "%s"', $line)) : $logger->notice(sprintf('删除 "%s" 成功', $line));
        }

        // 删除remove.txt文件
        unlink($file);
    }

    /**
     * 保存最后更新
     * 跟踪最后安装更新时间
     *
     * @param  string $version
     * @return void
     */
    private function saveLastUpdate($version)
    {
        // 准备数据
        $data = [
            'type'    => 'updater',
            'event'   => 'update',
            'message' => '安装更新：' . $version
        ];

        // 创建日志
        $this->dblogger->createLog($data['type'], $data['event'], $data['message']);
    }

    /**
     * 检查
     * 确定是否有可用的更新包
     *
     * @return array $data
     */
    private function check()
    {
        // 初始化数据以稍后烘焙它们
        $data = [
            'message'   => false,
            'packages'  => [],
            'available' => false
        ];

        // 获取更新包
        $packages = $this->packages();

        // 获取更新包时出错
        if(!is_array($packages))
        {
            $data['message'] = $packages;
        }
        else
        {
            // 没有可用的更新
            if(count($packages) == 0 || array_key_last($packages) == $this->version)
            {
                $data['message'] = '没有可用的更新。';
            }
            else
            {
                // 循环包
                foreach($packages as $key => $package)
                {
                    // 我们将需要其余的包
                    if(version_compare($this->version, $key, '>='))
                        continue;

                    // 添加包
                    $data['packages'][$key] = $package;
                }

                // 可用的更新
                if(count($data['packages']))
                    $data['available'] = true;
            }
        }

        return $data;
    }

    /**
     * 包
     * 获取更新包
     *
     * @return array $updates
     */
    private function packages()
    {
        // 检查缓存
        if(file_exists($this->cache_path . $this->cache_file))
        {
            // 缓存文件创建时间
            $cache_creation_time = filemtime($this->cache_path . $this->cache_file);

            // 确保缓存文件仍然有效
            if(time() <= ($cache_creation_time + (60 * 60 * 1))) # 有效1小时
            {
                // 打开缓存文件
                $cache = fopen($this->cache_path . $this->cache_file, 'r');

                // 读取缓存文件
                $updates = fread($cache, filesize($this->cache_path . $this->cache_file));

                // 关闭缓存文件
                fclose($cache);

                return json_decode($updates, true);
            }
        }

        // 限制
        $limit = ['offset' => 1, 'count' => 100];

        // 初始化更新以稍后烘焙它们
        $updates = [];

        // 初始化发布以稍后烘焙它们
        $releases = [];

        // 获取GitHub存储库发布，直到找到当前资产版本
        while(strpos(json_encode($releases), $this->version . $this->asset_file_ext) === false)
        {
            // 发送HTTP请求以获取发布
            $data = $this->call(str_replace(['{PAGE}', '{PER_PAGE}'], [$limit['offset'], $limit['count']], $this->releases_url));

            // 解析我们的JSON
            $data = json_decode($data, true);

            // 停止！API错误发生
            if(is_array($data) && isset($data['message']))
                return $data['message'];

            // 停止！看起来这是最后一页
            if(!$data || $data === null || (is_array($data) && count($data) == 0))
                break;

            // 附加数据
            $releases = array_merge($releases, $data);

            // 增加偏移量
            $limit['offset'] = $limit['offset'] + 1;
        };

        // 循环发布
        foreach($releases as $release)
        {
            // 无效发布
            if(!is_array($release) || !isset($release['assets']) || !is_array($release['assets']))
                continue;

            // 循环资产
            foreach($release['assets'] as $asset)
            {
                // 无效资产
                if(!is_array($asset) || !isset($asset['browser_download_url']) || !is_string($asset['browser_download_url']))
                    continue;

                // 获取资产文件名和扩展名
                $asset['fileName'] = pathinfo(basename($asset['browser_download_url']), PATHINFO_FILENAME);
                $asset['fileExt']  = pathinfo(basename($asset['browser_download_url']), PATHINFO_EXTENSION);

                // 无效资产
                if(!$asset['fileName'] || $asset['fileExt'] != str_replace('.', '', $this->asset_file_ext))
                    continue;

                // 将资产添加到我们的更新数组中
                $updates[$asset['fileName']] = [
                    # 发布信息
                    'release' => [
                        'name'     => isset($release['name'])     ? $release['name']     : '',
                        'html_url' => isset($release['html_url']) ? $release['html_url'] : '',
                    ],

                    # 资产信息
                    'asset' => [
                        'author' => [
                            'name'     => isset($asset['uploader']['login'])      ? $asset['uploader']['login']      : '',
                            'avatar'   => isset($asset['uploader']['avatar_url']) ? $asset['uploader']['avatar_url'] : '',
                            'html_url' => isset($asset['uploader']['html_url'])   ? $asset['uploader']['html_url']   : ''
                        ],

                        'created_at'           => isset($asset['created_at']) ? date('Y-m-d H:i:s', strtotime($asset['created_at'])) : '',
                        'updated_at'           => isset($asset['updated_at']) ? date('Y-m-d H:i:s', strtotime($asset['updated_at'])) : '',
                        'browser_download_url' => $asset['browser_download_url']
                    ]
                ];
            }
        }

        // 排序更新
        $updates = array_reverse($updates);

        // 创建缓存文件
        $cache = fopen($this->cache_path . $this->cache_file, 'w');

        // 写入缓存文件
        fwrite($cache, json_encode($updates, JSON_PRETTY_PRINT));

        // 关闭缓存文件
        fclose($cache);

        return $updates;
    }

    /**
     * 调用
     * 发送HTTP请求
     *
     * @param  string $url
     * @return string $response
     */
    private function call($url)
    {
        if(!$url)
            return false;

        // 忽略客户端中止
        ignore_user_abort();

        // HACK！给它一些时间...
        set_time_limit(0);

        $options = [
            'timeout'         => 300,
            'allow_redirects' => [
                'max' => 10,
            ],
            'user_agent'      => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1',
            'version'         => CURL_HTTP_VERSION_2_0,
            'verify'          => false,
        ];

        return Services::curlrequest()->get($url, $options)->getBody();
    }

    /**
     * 删除目录
     * 销毁目录
     *
     * @param  string $dir
     * @return void
     */
    private function removeDir($dir)
    {
        // 确保这是一个目录
        if(!is_dir($dir))
            return;

        // 获取树
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        // 删除内部的所有内容
        foreach($files as $file)
            ($file->isDir()) ? rmdir($file->getRealPath()) : unlink($file->getRealPath());

        // 删除根目录
        rmdir($dir);
    }
}
