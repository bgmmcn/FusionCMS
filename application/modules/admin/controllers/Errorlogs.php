<?php

use MX\MX_Controller;

/**
 * 错误日志控制器类
 * @property errorlogs_model $errorlogs_model 错误日志模型类
 */
class Errorlogs extends MX_Controller
{
    private static $levelsIcon = array(
        'INFO'     => 'fa-duotone fa-circle-info',
        'ERROR'    => 'fa-duotone fa-triangle-exclamation',
        'CRITICAL' => 'fa-duotone fa-triangle-exclamation',
        'DEBUG'    => 'fa-duotone fa-triangle-exclamation',
        'WARNING'  => 'fa-duotone fa-warning',
        'ALL'      => 'fa-duotone fa-minus',
    );

    private static $levelClasses = [
        'INFO'     => 'info',
        'ERROR'    => 'danger',
        'CRITICAL' => 'danger',
        'WARNING'  => 'warning',
        'DEBUG'    => 'debug',
        'ALL'      => 'muted',
    ];

    private $LOG_LINE_START_PATTERN = "/((INFO)|(ERROR)|(DEBUG)|(CRITICAL)|(WARNING)|(ALL))[\s\-\d:\.\/]+(-->)/";
    private $LOG_DATE_PATTERN = ["/^((ERROR)|(INFO)|(DEBUG)|(CRITICAL)|(WARNING)|(ALL))\s\-\s/", "/\s(-->)/"];
    private $LOG_LEVEL_PATTERN = "/^((ERROR)|(INFO)|(DEBUG)|(CRITICAL)|(WARNING)|(ALL))/";

    // 这是系统中存储日志文件的目录路径
    private $logFolderPath;

    // 这是日志文件的匹配模式
    private $logFilePattern;

    // 这是日志文件的完整路径
    private $fullLogFilePath = "";

    private $MAX_STRING_LENGTH = 300; // 300 个字符

    public function __construct()
    {
        parent::__construct();

        // 加载必要依赖
        $this->load->library("administrator");

        // 设置日志文件目录路径
        $this->logFolderPath =  WRITEPATH . "/logs";
        $this->logFilePattern = "log-*.php";

        // 构造日志文件的完整路径
        $this->fullLogFilePath = $this->logFolderPath . "/" . $this->logFilePattern;
    }

    /**
     * 显示错误日志列表
     */
    public function index()
    {
        // 获取日志文件名
        $fileName =  $this->input->get("f");

        // 获取日志文件列表
        $files = $this->getFiles();

        // 确定当前日志文件
        if (!is_null($fileName)) {
            $currentFile = $this->logFolderPath . "/" . basename(base64_decode($fileName));
        } elseif (is_null($fileName) && !empty($files)) {
            $currentFile = $this->logFolderPath . "/" . $files[0];
        } else {
            $currentFile = null;
        }

        // 处理日志数据
        if (!is_null($currentFile) && file_exists($currentFile)) {
            $logs =  $this->processLogs($this->getLogs($currentFile));
        } else {
            $logs = [];
        }

        // 准备视图数据
        $data = array(
            'logs' => $logs,
            'files' => $files,
            'currentFile' => !is_null($currentFile) ? basename($currentFile) : "",
        );

        // 加载视图模板
        $output = $this->template->loadPage("errorlogs.tpl", $data);

        // 将内容放入管理面板容器
        $content = $this->administrator->box('错误日志', $output);

        // 渲染最终页面
        $this->administrator->view($content);
    }

    /**
     * 处理日志数据
     * @param array $logs 日志数据
     * @return array 处理后的日志数据
     */
    private function processLogs($logs)
    {
        if (is_null($logs)) {
            return null;
        }

        $superLog = [];

        foreach ($logs as $log) {
            // 获取日志行的开始部分
            $logLineStart = $this->getLogLineStart($log);

            if (!empty($logLineStart)) {
                // 这是新的日志行的开始
                $level = $this->getLogLevel($logLineStart);
                $data = [
                    "level" => $level,
                    "date" => $this->getLogDate($logLineStart),
                    "icon" => self::$levelsIcon[$level],
                    "class" => self::$levelClasses[$level],
                ];

                // 获取日志消息
                $logMessage = preg_replace($this->LOG_LINE_START_PATTERN, '', $log);

                // 处理日志消息长度
                if (strlen($logMessage) > $this->MAX_STRING_LENGTH) {
                    $data['content'] = substr($logMessage, 0, $this->MAX_STRING_LENGTH);
                    $data["extra"] = substr($logMessage, ($this->MAX_STRING_LENGTH + 1));
                } else {
                    $data["content"] = $logMessage;
                }

                $superLog[] = $data;
            } elseif (!empty($superLog)) {
                // 这是上一条日志的继续
                // 将其添加到上一条日志的额外信息中
                $prevLog = $superLog[count($superLog) - 1];
                $extra = (array_key_exists("extra", $prevLog)) ? $prevLog["extra"] : "";
                $prevLog["extra"] = $extra . "<br>" . $log;
                $superLog[count($superLog) - 1] = $prevLog;
            }
        }

        return $superLog;
    }

    /**
     * 获取日志级别
     * @param string $logLineStart 日志行的开始部分
     * @return string 日志级别
     */
    private function getLogLevel($logLineStart)
    {
        preg_match($this->LOG_LEVEL_PATTERN, $logLineStart, $matches);
        return $matches[0];
    }

    /**
     * 获取日志日期
     * @param string $logLineStart 日志行的开始部分
     * @return string 日志日期
     */
    private function getLogDate($logLineStart)
    {
        return preg_replace($this->LOG_DATE_PATTERN, '', $logLineStart);
    }

    /**
     * 获取日志行的开始部分
     * @param string $logLine 日志行
     * @return string 日志行的开始部分
     */
    private function getLogLineStart($logLine)
    {
        preg_match($this->LOG_LINE_START_PATTERN, $logLine, $matches);
        if (!empty($matches)) {
            return $matches[0];
        }
        return "";
    }

    /**
     * 获取日志文件内容
     * @param string $fileName 日志文件名
     * @return array 日志文件内容
     */
    private function getLogs($fileName)
    {
        $size = filesize($fileName);
        if (!$size) {
            return null;
        }
        return file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    /**
     * 获取日志文件列表
     * @param bool $basename 是否返回文件名
     * @return array 日志文件列表
     */
    private function getFiles($basename = true)
    {
        $files = glob($this->fullLogFilePath);

        $files = array_reverse($files);
        $files = array_filter($files, 'is_file');
        if ($basename && is_array($files)) {
            foreach ($files as $k => $file) {
                $files[$k] = basename($file);
            }
        }
        return array_values($files);
    }
}
