<?php

/**
 *
 * 启用网站的验证码
 *
 */
$config['use_captcha'] = false;

/**
 *
 * 验证码的类型？
 *
 * 'recaptcha'  = Google Recaptcha v2
 * 'recaptcha3' = Google Recaptcha v3
 * 'inbuilt'    = 内置验证码系统
 *
 */
$config['captcha_type'] = "inbuilt";

/**
 *
 * 经过多少次尝试后应该弹出验证码？
 *
 */
$config['captcha_attemps'] = 3;

/**
 *
 * 经过多少次尝试后我们应该阻止一个IP地址？
 * 一个IP地址应该被阻止多少分钟？
 *
 */
$config['block_attemps'] = 5;
$config['block_duration'] = 5;

/**
 *
 * 网站密钥
 * 在 www.google.com/recaptcha/admin 获取网站密钥
 *
 */
$config['recaptcha_site_key'] = false;

/**
 *
 * 密钥
 * 在 www.google.com/recaptcha/admin 获取密钥
 *
 */
$config['recaptcha_secret_key'] = false;

// 主题
$config['recaptcha_theme'] = "dark"; // dark - light

