<?php

/**
 * Note to module developers:
 *  Keeping a module specific language file like this
 *  in this external folder is not a good practise for
 *  portability - I do not advice you to do this for
 *  your own modules since they are non-default.
 *  Instead, simply put your language files in
 *  application/modules/yourModule/language/
 *  You do not need to change any code, the system
 *  will automatically look in that folder too.
 */

// UCP
$lang['user_panel'] = "用户控制面板";
$lang['account_overview'] = "账号概览";
$lang['account_characters'] = "账号角色";

$lang['nickname'] = "昵称";
$lang['change_nickname'] = "更改昵称";

$lang['username'] = "用户名";

$lang['location'] = "位置";
$lang['change_location'] = "更改位置";

$lang['email'] = "邮箱";
$lang['change_email'] = "更改邮箱";

$lang['password'] = "密码";
$lang['change_password'] = "修改密码";

$lang['account_rank'] = "账号等级";
$lang['voting_points'] = "人口";
$lang['donation_points'] = "本位";
$lang['account_status'] = "账号状态";
$lang['member_since'] = "注册时间";
$lang['data_tip_vote'] = "通过为服务器投票获得人口";
$lang['data_tip_donate'] = "通过捐赠服务器获得本位";

$lang['edit'] = "编辑";

// Avatar
$lang['change_avatar'] = "更换头像";
$lang['avatar_invalid'] = "所选头像无效。";
$lang['avatar_invalid_rank'] = "所选头像需要更高的用户等级。";

// Settings
$lang['settings'] = "账号设置";

$lang['old_password'] = "旧密码";
$lang['new_password'] = "新密码";
$lang['new_password_confirm'] = "确认密码";
$lang['new_password_submit'] = "修改密码";

$lang['nickname_error'] = "昵称必须在4到14个字符之间，且只能包含字母和数字";
$lang['location_error'] = "位置只能包含字母，且长度不能超过32个字符";
$lang['pw_doesnt_match'] = "密码不匹配!";
$lang['changes_saved'] = "修改已保存!";
$lang['invalid_pw'] = "密码错误!";
$lang['nickname_taken'] = "昵称已经被占用";
$lang['invalid_language'] = "语言无效";

$lang['change_information'] = "修改信息";

// Security
$lang['account_security'] = "账号安全";
$lang['save_changes'] = "保存修改";
$lang['two_factor'] = "双因素认证";
$lang['two_factor_description'] = "双因素认证可以通过添加额外的安全层来保护您的账号免受未经授权的访问。";
$lang['two_factor_help'] = "从Google Play或App Store下载Google Authenticator应用程序。启动应用程序并使用您的手机摄像头扫描下面的条形码。输入Authenticator应用程序生成的6位验证码。";
$lang['qr_code'] = "二维码";
$lang['qr_code_help_1'] = "无法扫描二维码？您也可以手动输入";
$lang['qr_code_help_2'] = "密钥。";
$lang['select_authentication'] = "选择认证方法";
$lang['disabled'] = "禁用";
$lang['google_authenticator'] = "Google Authenticator";
$lang['six_digit_auth_code'] = "6位认证码";
$lang['six_digit_not_empty'] = "6位认证码不能为空";
$lang['six_digit_not_true'] = "认证码不正确";
