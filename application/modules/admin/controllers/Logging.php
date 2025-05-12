<?php

use MX\MX_Controller;

/**
 * 日志控制器类
 * @property logging_model $logging_model 日志模型类
 */
class Logging extends MX_Controller
{
    private $logsToLoad = 10; // 每次加载10条日志

    public function __construct()
    {
        parent::__construct();

        $this->load->library("administrator");
        $this->load->model('logging_model');

        requirePermission("viewLogs");
    }

    /**
     * 加载日志页面
     */
    public function index()
    {
        // 设置页面标题
        $this->administrator->setTitle("日志");

        $logs = $this->dblogger->getLogs("", 0, 10);

        if ($logs)
        {
            foreach ($logs as $key => $value) {
                $logs[$key]['custom'] = json_decode($value['custom']);
            }
        }

        // 准备数据
        $data = array(
            'logs' => $logs, // 获取前10条日志
            'modules' => $this->administrator->getEnabledModules(),
            'show_more' => $this->dblogger->getLogCount() - count((array)$logs)
        );

        // 加载日志模板
        $output = $this->template->loadPage("logging/logging.tpl", $data);

        // 将日志模板放入主框架中
        $content = $this->administrator->box('网站日志', $output);

        // 输出内容
        $this->administrator->view($content, false, "modules/admin/js/logging.js");
    }

    public function loadMoreLogs()
    {
        $offset = $this->input->post('offset');
        $count = $this->input->post('count');
        $extraLogCount = $this->input->post('show_more');

        $extraLogCount -= $this->logsToLoad;

        // 数据验证已在模型中完成
        $logs = $this->dblogger->getLogs("", $offset, $count);

        if ($logs)
        {
            foreach ($logs as $key => $value) {
                $logs[$key]['custom'] = json_decode($value['custom']);
            }
        }

        if ($logs) {
            // 准备分页数据
            $data = array(
                'logs' => $logs,
                'show_more' => $extraLogCount
            );

            // 加载日志条目模板
            $output = $this->template->loadPage("logging/logging_found.tpl", $data);

            die($output);
        } else {
            die("<span>无更多结果</span>");
        }
    }

    /**
     * 根据给定参数执行日志搜索
     * POST参数 module: 模块名称
     * POST参数 search: 搜索内容（用户名、IP、用户ID等）
     */
    public function search()
    {
        $module = $this->input->post('module');
        $search = $this->input->post('search');

        // 数据验证已在模型中完成
        $logs = $this->logging_model->findLogs($search, $module);

        if ($logs)
        {
            foreach ($logs as $key => $value) {
                $logs[$key]['custom'] = json_decode($logs[$key]['custom']);
            }
        }

        if ($logs) {
            // 准备数据
            $data = array(
                'logs' => $logs,
                'show_more' => ''
            );

            // 加载日志条目模板
            $output = $this->template->loadPage("logging/logging_found.tpl", $data);

            die($output);
        } else {
            die("<span>无结果</span>");
        }
    }
}
