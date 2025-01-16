<?php

// 如果没有定义 BASEPATH，则退出脚本
if (! defined('BASEPATH')) {
    exit('不允许直接脚本访问。');
}

// 配置账号加密方式
$config['account_encryption'] = "SRP6"; // SPH, SRP, SRP6

// 启用基于角色的访问控制（RBAC）
$config['rbac'] = true;

// 启用战网（Battle.net）认证
$config['battle_net'] = true;

// 配置战网认证的加密方式
$config['battle_net_encryption'] = "SRP6_V2"; // SRP6_V2, SRP6_V1, SPH

// 启用基于时间的一次性密码（TOTP）密钥
$config['totp_secret'] = true;

// 配置 TOTP 密钥的名称
$config['totp_secret_name'] = "totp_secret"; // token_key, totp_secret

// 配置 TOTP 主密钥
$config['TOTPMasterSecret'] = ""; // for totp_secret

