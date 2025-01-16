<?php

/*
| -------------------------------------------------------------------
| 自动加载器
| -------------------------------------------------------------------
| 这个文件指定了默认情况下应该加载哪些系统。
|
| 为了保持框架尽可能轻量级，默认情况下只加载绝对最小的资源。例如，
| 数据库不会自动连接，因为没有假设你是否打算使用它。这个文件允许你
| 全局定义你希望在每个请求中加载哪些系统。
|
*/

/**
 * -------------------------------------------------------------------
 * 自动加载器
 * -------------------------------------------------------------------
 * 这个文件定义了命名空间和类映射，以便自动加载器可以根据需要找到文件。
 */

/**
 * -------------------------------------------------------------------
 * 命名空间
 * -------------------------------------------------------------------
 * 这将你的应用程序中的命名空间位置映射到文件系统中的位置。这些用于自动加载器
 * 在第一次实例化时定位文件。
 *
 * '/application' 和 '/system' 目录已经为你映射好了。你可以更改 'App' 命名空间的名称，
 * 但这应该在创建任何命名空间类之前完成，否则你将需要修改所有这些类才能使其工作。
 *
 * 不要更改 CodeIgniter 命名空间的名称，否则你的应用程序将无法正常工作。
 *
 * 原型：
 *
 *   $config['psr4'] = [
 *       'CodeIgniter' => SYSPATH
 *   ];
 */

$config['psr4'] = [
    APP_NAMESPACE     => APPPATH,
    'App\Config'      => APPPATH.'config',
    'CodeIgniter'     => realpath(SYSTEMPATH)
];

/**
 * -------------------------------------------------------------------
 * 类映射
 * -------------------------------------------------------------------
 * 类映射提供了类名及其在驱动器上的确切位置的映射。以这种方式加载的类将具有
 * 稍微更快的性能，因为它们不必像通过命名空间自动加载的类那样在一个或多个目录中搜索。
 *
 * 原型：
 *
 *   $config['classmap'] = [
 *       'MyClass'   => '/path/to/class/file.php'
 *   ];
 */

$config['classmap'] = [
    'CodeIgniter\Log\Logger'                      => SYSTEMPATH. 'Log/Logger.php',
    'Laminas\Escaper\Escaper'                     => SYSTEMPATH. 'View/Escaper.php'
];

if(!file_exists(WRITEPATH. 'install/.lock'))
    return;

/*
| -------------------------------------------------------------------
| 自动加载包
| -------------------------------------------------------------------
| 原型：
|
|  $autoload['packages'] = [APPPATH.'third_party', '/usr/local/shared'];
|
*/

$autoload['packages'] = [];

/*
| -------------------------------------------------------------------
| 自动加载库
| -------------------------------------------------------------------
| 这些是位于 system/libraries 文件夹或 application/libraries 文件夹中的类。
|
| 原型：
|
|   $autoload['libraries'] = ['database', 'cache', 'security'];
*/

$autoload['libraries'] = ['security', 'cache', 'database', 'smartyengine' => 'smarty', 'template', 'language', 'realms', 'acl', 'user', 'dblogger', 'dbbackup', 'captcha', 'recaptcha', 'items', 'crypto'];

/*
| -------------------------------------------------------------------
| 自动加载驱动程序
| -------------------------------------------------------------------
| 这些类位于 system/libraries/ 或 application/libraries/ 目录中，但也位于它们自己的子目录中，并且扩展了 CI_Driver_Library 类。它们提供了多个可互换的驱动程序选项。
|
| 原型：
|
|   $autoload['drivers'] = ['cache'];
*/

$autoload['drivers'] = [''];

/*
| -------------------------------------------------------------------
| 自动加载助手文件
| -------------------------------------------------------------------
| 原型：
|
|   $autoload['helper'] = ['url', 'file'];
*/

$autoload['helper'] = ['url', 'emulator', 'form', 'text', 'lang', 'breadcumb', 'permission', 'tinymce'];

/*
| -------------------------------------------------------------------
| 自动加载配置文件
| -------------------------------------------------------------------
| 原型：
|
|   $autoload['config'] = ['config1', 'config2'];
|
| 注意：这个项目仅用于你创建了自定义配置文件的情况。否则，请保持为空。
|
*/

$autoload['config'] = ['language', 'version', 'acl_defaults', 'fusion', 'message', 'backups', 'cdn', 'captcha', 'social_media', 'performance', 'wow_db', 'wow_expansions', 'auth'];

/*
| -------------------------------------------------------------------
| 自动加载语言文件
| -------------------------------------------------------------------
| 原型：
|
|   $autoload['language'] = ['lang1', 'lang2'];
|
| 注意：不要包含文件的“_lang”部分。例如，“codeigniter_lang.php”将被引用为 ['codeigniter'];
|
*/

$autoload['language'] = [];

/*
| -------------------------------------------------------------------
| 自动加载模型
| -------------------------------------------------------------------
| 原型：
|
|   $autoload['model'] = ['model1', 'model2'];
|
*/

$autoload['model'] = ['cms_model', 'external_account_model', 'internal_user_model', 'acl_model'];

/* 文件 autoload.php 结束 */
/* 位置：./application/config/autoload.php */
