<?php

use MX\MX_Controller;

/**
 * 备份管理控制器类
 * @property cms_model $cms_model cms_model 类
 */
class Backups extends MX_Controller
{
    public function __construct()
    {
        // 确保加载管理员库
        $this->load->library('administrator');
        $this->load->helper('download');
        $this->load->model('cms_model');

        parent::__construct();

        $this->load->config('backups');

        require_once('application/libraries/ConfigEditor.php');

        requirePermission("viewBackups");
    }

    public function index()
    {
        // 设置页面标题
        $this->administrator->setTitle("备份管理");

        $backups = $this->cms_model->getBackups();

        // 加载备份配置
        $config['auto_backups'] = $this->config->item('auto_backups');
        $config['backups_interval'] = $this->config->item('backups_interval');
        $config['backups_time'] = $this->config->item('backups_time');
        $config['backups_max_keep'] = $this->config->item('backups_max_keep');

        // 准备视图数据
        $data = [
            'backups' => $backups,
            'config' => $config,
            'url' => $this->template->page_url
        ];

        // 加载视图模板
        $output = $this->template->loadPage("backups.tpl", $data);

        // 将内容放入管理面板
        $content = $this->administrator->box('备份管理系统', $output);

        // 输出页面内容
        $this->administrator->view($content, false, "modules/admin/js/backups.js");
    }

    public function do_backup()
    {
        requirePermission("generateBackup");
        $this->dbbackup->backup(true);
    }

    public function saveSettings()
    {
        requirePermission("editBackupSettings");

        if (!is_numeric($this->input->post('backups_interval')) || !is_numeric($this->input->post('backups_max_keep'))) {
            die('只允许输入数字');
        }

        if ($this->input->post('backups_time') == 'hour' && $this->input->post('backups_interval') > 24) {
            die('一天只有24小时');
        }

        $fusionConfig = new ConfigEditor("application/config/backups.php");

        $fusionConfig->set('auto_backups', $this->input->post('auto_backups'));
        $fusionConfig->set('backups_interval', $this->input->post('backups_interval'));
        $fusionConfig->set('backups_time', $this->input->post('backups_time'));
        $fusionConfig->set('backups_max_keep', $this->input->post('backups_max_keep'));

        $fusionConfig->save();

        die('配置已保存');
    }

    public function download($id)
    {
        requirePermission("executeBackupActions");

        $name = $this->cms_model->getBackups($id);
        $file = 'writable/backups/' . $name . '.zip';
        if (file_exists($file)) {
            force_download($file, null);
        } else {
            die("文件不存在");
        }
    }

    public function delete($id)
    {
        requirePermission("executeBackupActions");

        $name = $this->cms_model->getBackups($id);
        $file = 'writable/backups/' . $name . '.zip';
        $this->cms_model->deleteBackups($id);
        if (file_exists($file)) {
            unlink($file);
            die('删除成功');
        } else {
            die("文件不存在");
        }
    }

    public function restore($id)
    {
		set_time_limit(0);
        requirePermission("executeBackupActions");

        $name = $this->cms_model->getBackups($id);
        $zip = new ZipArchive();
        $zipfile = $zip->open('writable/backups/' . $name . '.zip');

        if ($zipfile === true) {
            // 解压路径
            $extractpath = "writable/backups/";

            // 解压文件
            $zip->extractTo($extractpath);
            $zip->close();
        } else {
            die('解压失败，请检查文件权限');
        }

        $sqlfile = 'writable/backups/' . $name . '.sql';

        if (file_exists($sqlfile)) {
            $lines = file($sqlfile);
            $statement = '';
            foreach ($lines as $line) {
                $statement .= $line;
                if (substr(trim($line), -1) === ';') {
                    $this->db->simpleQuery($statement);
                    $statement = '';
                }
            }
        } else {
            die("SQL文件不存在！");
        }

        unlink('writable/backups/' . $name . '.sql');
        die('恢复成功');
    }
}
