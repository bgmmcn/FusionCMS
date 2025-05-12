<?php

use MX\MX_Controller;
use CodeIgniter\HTTP\UserAgent;

/**
 * 会话管理控制器类
 * @property session_model $session_model 会话模型类
 */
class Sessions extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('administrator');
        $this->load->model('session_model');

        requirePermission("viewSessions");
    }

    /**
     * 显示活跃会话列表
     */
    public function index()
    {
        // 获取所有会话数据
        $sessions = $this->session_model->get();
        $user_agent = new UserAgent();

        if ($sessions)
        {
            foreach ($sessions as $key => $value) {
                // 解析会话数据
                $session = $this->session_model->getSessId($value['id']);
                if (!empty($session['data']))
                {
                    $session = $this->parseSession($session['data']);
                    $session = unserialize($session);
                }

                // 获取用户信息
                if ($this->getUserId($session))
                {
                    $sessions[$key]['uid'] = $this->getUserId($session);
                    $sessions[$key]['nickname'] = $this->getNickname($session);
                }

                // 解析用户代理信息
                $user_agent->parse($value['user_agent'] ?? '');
                $sessions[$key]['os'] = $this->getPlatform($value['user_agent']);
                $sessions[$key]['osName'] = $user_agent->getPlatform();
                $sessions[$key]['browser'] = $this->getBrowser($value['user_agent']);
                $sessions[$key]['browserName'] = $user_agent->getBrowser();
                $sessions[$key]['date'] = $value["timestamp"];
            }
        }

        // 准备视图数据
        $data = array(
            'sessions' => $sessions,
        );

        // 加载视图模板
        $output = $this->template->loadPage("sessions/sessions.tpl", $data);

        // 输出到管理面板
        $content = $this->administrator->box('活跃会话管理', $output);
        $this->administrator->view($content, false, "modules/admin/js/session.js");
    }

    /**
     * 删除指定会话
     */
    public function deleteSessions()
    {
        $ip_address = $this->input->ip_address();

        $this->session_model->deleteSessions($ip_address);

        $this->dblogger->createLog("admin", "purge", "清除会话");

        die('1');
    }

    /**
     * 解析会话数据
     */
    private function parseSession($sess_data): string
    {
        $sess_data = rtrim($sess_data, ";");
        $sess_info = array();
        $parts = explode(";", $sess_data);

        unset($parts[3], $parts[4]);

        foreach ($parts as $part) {
            $part = explode("|", $part);
            $key = preg_replace('/:.*/', '', $part[0]);
            $value = preg_replace('/.*:/', '', $part[1]);
            $value = str_replace('"', '', $value);
            $sess_info[$key] = $value;
        }
        unset($sess_info["__ci_last_regenerate"], $sess_info["captcha"], $sess_info[""], $sess_info["admin_access"], $sess_info["language"], $sess_info["expansion"], $sess_info["email"], $sess_info["last_ip"], $sess_info["register_date"]);

        return serialize($sess_info);
    }

    /**
     * 获取用户ID
     */
    private function getUserId($data)
    {
        if (array_key_exists("uid", $data)) {
            return $data['uid'];
        }
        else
        {
            return false;
        }
    }

    /**
     * 获取用户昵称
     */
    private function getNickname($data)
    {
        if (array_key_exists("nickname", $data)) {
            return $data['nickname'];
        }
    }

    /**
     * 获取浏览器类型
     */
    private function getBrowser($user_agent): string
    {
        if (preg_match('/trident/i', $user_agent) && !preg_match('/opera/i', $user_agent)) {
            return "ie";
        } elseif (preg_match('/opera/i', $user_agent)) {
            return "opera";
        } elseif (preg_match('/firefox/i', $user_agent)) {
            return "firefox";
        } elseif (preg_match('/edg/i', $user_agent)) {
            return "edge";
        } elseif (preg_match('/chrome/i', $user_agent)) {
            return "chrome";
        } elseif (preg_match('/android/i', $user_agent)) {
            return "android";
        } elseif (preg_match('/safari/i', $user_agent)) {
            return "safari";
        } else {
            // Default to most common one to prevent errors
            return "chrome";
        }
    }

    /**
     * 获取操作系统类型
     */
    private function getPlatform($user_agent): string
    {
        if (preg_match('/android/i', $user_agent)) {
            return "android";
        } elseif (preg_match('/linux/i', $user_agent)) {
            return "linux";
        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            return "windows";
        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            return "mac";
        } else {
            // Default to most common one to prevent errors
            return "windows";
        }
    }
}
