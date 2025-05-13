<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(!isset($lang) || !is_array($lang))
    $lang = [];

/**
 * - RTL -----------------------------------------------------
 * -----------------------------------------------------------
 */
$lang['isRTL'] = 0;

/**
 * - Global --------------------------------------------------
 * -----------------------------------------------------------
 */
$lang = array_merge($lang, [
    'global_or'          => '或',
    'global_icon'        => '图标',
    'global_okay'        => '确定',
    'global_accept'      => '接受',
    'global_reject'      => '拒绝', 
    'global_cancel'      => '取消',
    'global_online'      => '在线',
    'global_offline'     => '离线',
    'global_loading'     => '加载中...',
    'global_user_avatar' => '%s 的头像'
]);

/**
 * - Main Template -------------------------------------------
 * -----------------------------------------------------------
 */
$lang = array_merge($lang, [
    # Logo
    'logo' => '欢迎来到 %s',

    # 导航菜单
    'nav' => '导航',

    # 用户按钮
    'account'  => '账号',
    'register' => '注册',

    # 横幅1
    'banner01_text01' => '欢迎来到 %s',
    'banner01_text02' => '探索我们的世界，体验独特的冒险旅程。',

    # 横幅2
    'banner02_text01' => '学习如何',
    'banner02_text02' => '连接',
    'banner02_text03' => '到我们的服务器',
    'banner02_text04' => '点击阅读指南',

    # 版权声明
    'copyright' => '%s &copy; 版权所有 %s'
]);
