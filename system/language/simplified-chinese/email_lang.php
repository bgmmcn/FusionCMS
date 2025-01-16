<?php

defined('BASEPATH') OR exit('不允许直接脚本访问。');

$lang['email_must_be_array'] = '电子邮件验证方法必须传递一个数组。';
$lang['email_invalid_address'] = '无效的电子邮件地址：%s';
$lang['email_attachment_missing'] = '无法找到以下电子邮件附件：%s';
$lang['email_attachment_unreadable'] = '无法打开此附件：%s';
$lang['email_no_from'] = '没有“发件人”标题头，无法发送邮件。';
$lang['email_no_recipients'] = '您必须包括收件人：To，Cc 或 Bcc';
$lang['email_send_failure_phpmail'] = '无法使用 PHP mail() 发送电子邮件。您的服务器可能未配置为使用此方法发送邮件。';
$lang['email_send_failure_sendmail'] = '无法使用 PHP Sendmail 发送电子邮件。您的服务器可能未配置为使用此方法发送邮件。';
$lang['email_send_failure_smtp'] = '无法使用 PHP SMTP 发送电子邮件。您的服务器可能未配置为使用此方法发送邮件。';
$lang['email_sent'] = '您的消息已成功使用以下协议发送：%s';
$lang['email_no_socket'] = '无法打开到 Sendmail 的套接字。请检查设置。';
$lang['email_no_hostname'] = '您未指定 SMTP 主机名。';
$lang['email_smtp_error'] = '遇到以下 SMTP 错误：%s';
$lang['email_no_smtp_unpw'] = '错误：您必须分配 SMTP 用户名和密码。';
$lang['email_failed_smtp_login'] = '发送 AUTH LOGIN 命令失败。错误：%s';
$lang['email_smtp_auth_un'] = '用户名验证失败。错误：%s';
$lang['email_smtp_auth_pw'] = '密码验证失败。错误：%s';
$lang['email_smtp_data_failure'] = '无法发送数据：%s';
$lang['email_exit_status'] = '退出状态代码：%s';
