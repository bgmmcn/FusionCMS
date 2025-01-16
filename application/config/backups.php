<?php

// 如果没有定义 BASEPATH，则退出脚本
if (! defined('BASEPATH')) {
    exit('不允许直接脚本访问。');
}

// 配置自动备份参数
$config['auto_backups'] = false; // 是否启用自动备份
$config['backups_interval'] = 12; // 备份间隔时间，单位为小时
$config['backups_time'] = "hour"; // 备份时间单位，可以是 'hour'（小时）、'day'（天）、'week'（周）等
$config['backups_max_keep'] = 6; // 最大保留备份数量

