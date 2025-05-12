<?php

use MX\MX_Controller;

/**
 * 游戏服务器管理控制器
 * @property realm_model $realm_model 服务器模型类
 */
class Realmmanager extends MX_Controller
{
    public function __construct()
    {
        // 加载管理后台库
        $this->load->library('administrator');
        $this->load->model('realm_model');

        parent::__construct();

        // 验证服务器管理权限
        requirePermission("editSystemSettings");
    }

    public function edit($id = false)
    {
        if (!$id || !is_numeric($id)) {
            die();
        }

        $realm = $this->realms->getRealm($id);

        // 设置页面标题
        $this->administrator->setTitle($realm->getName());

        // 准备视图数据
        $data = array(
            'url' => $this->template->page_url,
            'realm' => $realm,
            'hostname_char' => ($realm->getConfig('override_hostname_char')) ? $realm->getConfig('override_hostname_char') : $realm->getConfig('hostname'),
            'username_char' => ($realm->getConfig('override_username_char')) ? $realm->getConfig('override_username_char') : $realm->getConfig('username'),
            'password_char' => ($realm->getConfig('override_password_char')) ? $realm->getConfig('override_password_char') : $realm->getConfig('password'),
            'port_char' => ($realm->getConfig('override_port_char')) ? $realm->getConfig('override_port_char') : 3306,
            'hostname_world' => ($realm->getConfig('override_hostname_world')) ? $realm->getConfig('override_hostname_world') : $realm->getConfig('hostname'),
            'username_world' => ($realm->getConfig('override_username_world')) ? $realm->getConfig('override_username_world') : $realm->getConfig('username'),
            'password_world' => ($realm->getConfig('override_password_world')) ? $realm->getConfig('override_password_world') : $realm->getConfig('password'),
            'port_world' => ($realm->getConfig('override_port_world')) ? $realm->getConfig('override_port_world') : 3306,
            'emulators' => $this->getEmulators(),
            'expansions' => $this->realms->getExpansions()
        );

        // 加载编辑模板
        $output = $this->template->loadPage("edit_realm.tpl", $data);

        // 构建面包屑导航
        $content = $this->administrator->box(
            '<a href="' . $this->template->page_url . 'admin/settings">设置</a> &rarr; ' . $realm->getName(),
            $output
        );

        // 渲染页面
        $this->administrator->view($content, false, "modules/admin/js/settings.js");
    }

    private function getEmulators()
    {
        require("application/config/emulator_names.php");

        return $emulators;
    }

    public function delete($id)
    {
        $this->cache->delete('*.cache');
        $this->cache->delete('items/item_' . $id . '_*.cache');
        $this->realm_model->delete($id);
    }

    public function create()
    {
        $data = array();

        $data['realmName'] = $this->input->post('name');
        $data['hostname'] = $this->input->post('hostname');
        $data['username'] = $this->input->post('username');
        $data['password'] = $this->input->post('password');
        $data['char_database'] = $this->input->post('characters');
        $data['world_database'] = $this->input->post('world');
        $data['cap'] = $this->input->post('cap');
        $data['expansion'] = $this->input->post('expansion');
        $data['realm_port'] = $this->input->post('port');
        $data['emulator'] = $this->input->post('emulator');
        $data['console_username'] = $this->input->post('console_username');
        $data['console_password'] = $this->input->post('console_password');
        $data['console_port'] = $this->input->post('console_port');

        $data['override_hostname_char'] = $this->input->post('override_hostname_char');
        $data['override_username_char'] = $this->input->post('override_username_char');
        $data['override_password_char'] = $this->input->post('override_password_char');
        $data['override_port_char'] = $this->input->post('override_port_char');

        $data['override_hostname_world'] = $this->input->post('override_hostname_world');
        $data['override_username_world'] = $this->input->post('override_username_world');
        $data['override_password_world'] = $this->input->post('override_password_world');
        $data['override_port_world'] = $this->input->post('override_port_world');

        if (!is_numeric($data['cap'])) {
            die('容量必须是数字');
        }

        if (!is_numeric($data['expansion'])) {
            die('扩展必须是数字');
        }

        if (!is_numeric($data['realm_port'])) {
            die('端口必须是数字');
        }

        if (!file_exists("application/emulators/" . $data['emulator'] . ".php")) {
            die('无效的模拟器');
        }

        $id = $this->realm_model->create($data);

        die((string)$id);
    }

    public function save($id = false)
    {
        if (!$id || !is_numeric($id)) {
            die();
        }

        $data['realmName'] = $this->input->post('realmName');
        $data['hostname'] = $this->input->post('hostname');
        $data['username'] = $this->input->post('username');

        if ($this->input->post('password')) {
            $data['password'] = $this->input->post('password');
        }

        $data['char_database'] = $this->input->post('characters');
        $data['world_database'] = $this->input->post('world');
        $data['cap'] = $this->input->post('cap');
        $data['expansion'] = $this->input->post('expansion');
        $data['realm_port'] = $this->input->post('port');
        $data['emulator'] = $this->input->post('emulator');
        $data['console_username'] = $this->input->post('console_username');

        $data['override_hostname_char'] = $this->input->post('override_hostname_char');
        $data['override_username_char'] = $this->input->post('override_username_char');
        $data['override_password_char'] = $this->input->post('override_password_char');
        $data['override_port_char'] = $this->input->post('override_port_char');

        $data['override_hostname_world'] = $this->input->post('override_hostname_world');
        $data['override_username_world'] = $this->input->post('override_username_world');
        $data['override_password_world'] = $this->input->post('override_password_world');
        $data['override_port_world'] = $this->input->post('override_port_world');

        if ($this->input->post('console_password')) {
            $data['console_password'] = $this->input->post('console_password');
        }

        $data['console_port'] = $this->input->post('console_port');

        if (!is_numeric($data['cap'])) {
            die('容量必须是数字');
        }

        if (!is_numeric($data['expansion'])) {
            die('扩展必须是数字');
        }

        if (!is_numeric($data['realm_port'])) {
            die('端口必须是数字');
        }

        if (!is_numeric($data['console_port'])) {
            die('控制台端口必须是数字');
        }

        if (!file_exists("application/emulators/" . $data['emulator'] . ".php")) {
            die('无效的模拟器');
        }

        $this->realm_model->save($id, $data);

        die('保存成功！');
    }
}
