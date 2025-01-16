<?php defined('BASEPATH') OR exit('不允许直接脚本访问。');

if(!isset($lang) ||!is_array($lang))
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
    'global_okay'        => '好的',
    'global_accept'      => '接受',
    'global_reject'      => '拒绝',
    'global_cancel'      => '取消',
    'global_online'      => '在线',
    'global_offline'     => '离线',
    'global_loading'     => '正在加载...',
    'global_user_avatar' => '%s 的头像'
]);

/**
 * - Main Template -------------------------------------------
 * -----------------------------------------------------------
 */
$lang = array_merge($lang, [
    # Logo
    'logo' => '%s',

    # Menu
    'nav' => '导航',

    # User buttons
    'account'  => '账号',
    'register' => '注册',

    # Banner 1
    'banner01_text01' => '',
    'banner01_text02' => '',

    # Banner 2
    'banner02_text01' => '',
    'banner02_text02' => '',
    'banner02_text03' => '',
    'banner02_text04' => '',

    # Copyright
    'copyright' => '%s &copy; 版权所有 %s'
]);
