<?php

if (! defined('BASEPATH')) {
    exit('不允许直接脚本访问。');
}

/*
|--------------------------------------------------------------------------
| 基础站点 URL
|--------------------------------------------------------------------------
|
| URL 到您的 CodeIgniter 根目录。通常这将是您的基础 URL，
| 带有一个尾随斜杠：
|
|   http://example.com/
|
| 警告：您必须设置这个值！
|
| 如果没有设置，那么 CodeIgniter 将尝试猜测协议和路径
| 您的安装，但由于安全考虑，主机名将被设置
| 为 $_SERVER['SERVER_ADDR']，如果可用，否则为 localhost。
| 自动检测机制仅存在于开发期间的便利，
| 不得在生产环境中使用！
|
| 如果您需要允许多个域，请记住，这个文件仍然
| 是一个 PHP 脚本，您可以轻松地自己完成。
|
*/

$config['base_url'] = (is_https()? 'https://' : 'http://').($_SERVER['HTTP_HOST']?? '').substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME'])));

/*
|--------------------------------------------------------------------------
| 索引文件
|--------------------------------------------------------------------------
|
| 通常这将是您的 index.php 文件，除非您已经将其重命名为
| 其他名称。如果您使用 mod_rewrite 来删除页面，请设置此
| 变量，使其为空。
|
*/

$config['index_page'] = '';

/*
|--------------------------------------------------------------------------
| URI 协议
|--------------------------------------------------------------------------
|
| 此项目确定应使用哪个服务器全局变量来检索
| URI 字符串。默认设置 'REQUEST_URI' 适用于大多数服务器。
| 如果您的链接似乎不起作用，请尝试其他美味的口味：
|
| 'REQUEST_URI'    使用 $_SERVER['REQUEST_URI']
| 'QUERY_STRING'   使用 $_SERVER['QUERY_STRING']
| 'PATH_INFO'      使用 $_SERVER['PATH_INFO']
|
| 警告：如果您将此设置为 'PATH_INFO'，URI 将始终被 URL 解码！
*/

$config['uri_protocol'] = 'AUTO';

/*
|--------------------------------------------------------------------------
| URL 后缀
|--------------------------------------------------------------------------
|
| 此选项允许您在 CodeIgniter 生成的所有 URL 后添加后缀。
| 有关更多信息，请参阅用户指南：
|
| https://codeigniter.com/user_guide/general/urls.html
*/

$config['url_suffix'] = '';

/*
|--------------------------------------------------------------------------
| 默认字符集
|--------------------------------------------------------------------------
|
| 这确定了在各种需要提供字符集的方法中默认使用的字符集。
|
*/

$config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| 类扩展前缀
|--------------------------------------------------------------------------
|
| 此项目允许您在扩展本地库时设置文件名/类名前缀。
| 有关更多信息，请参阅用户指南：
|
| https://codeigniter.com/user_guide/general/core_classes.html
| https://codeigniter.com/user_guide/general/creating_libraries.html
|
*/

$config['subclass_prefix'] = 'MY_';

/*
|--------------------------------------------------------------------------
| 允许的 URL 字符
|--------------------------------------------------------------------------
|
| 这允许您指定在 URL 中允许使用哪些字符。
| 当有人尝试提交包含不允许字符的 URL 时，他们将
| 收到警告消息。
|
| 作为安全措施，强烈建议您将 URL 限制为
| 尽可能少的字符。默认情况下，只允许这些字符：a-z 0-9~%.:_-
|
| 留空以允许所有字符 - 但只有在您疯了的情况下。
|
| 配置的值实际上是一个正则表达式字符组
| 它将被执行如下：! preg_match('/^[<permitted_uri_chars>]+$/i
|
| 不要更改此设置，除非您完全理解其影响！
|
*/

$config['permitted_uri_chars'] = 'a-z 0-9~%.:\_\=+%\&-';
/*
|--------------------------------------------------------------------------
| 启用查询字符串
|--------------------------------------------------------------------------
|
| 默认情况下，CodeIgniter 使用搜索引擎友好的基于段的 URL：
| example.com/who/what/where/
|
| 您可以选择启用标准查询字符串基于的 URL：
| example.com?who=me&what=something&where=here
|
| 选项是：TRUE 或 FALSE（布尔值）
|
| 其他项目允许您设置查询字符串“单词”，这些单词将
| 调用您的控制器及其函数：
| example.com/index.php?c=controller&m=function
|
| 请注意，当启用此功能时，一些助手可能无法按预期工作，因为
| CodeIgniter 主要设计为使用基于段的 URL。
|
*/

$config['enable_query_strings'] = false;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

/*
|--------------------------------------------------------------------------
| 允许 $_GET 数组
|--------------------------------------------------------------------------
|
| 默认情况下，CodeIgniter 允许访问 $_GET 数组。如果出于某种原因
| 您希望禁用它，请将 'allow_get_array' 设置为 FALSE。
|
| 警告：此功能已被弃用，目前仅出于向后兼容的目的提供！
|
*/

$config['allow_get_array'] = true;

/*
|--------------------------------------------------------------------------
| 错误视图目录路径
|--------------------------------------------------------------------------
|
| 除非您想设置默认的 application/views/errors/ 目录以外的其他目录，否则请留空。
| 使用完整的服务器路径，并带有斜杠。
|
*/

$config['error_views_path'] = '';

/*
|--------------------------------------------------------------------------
| 缓存目录路径
|--------------------------------------------------------------------------
|
| 除非您想设置默认的 application/cache/ 目录以外的其他目录，否则请留空。
| 使用完整的服务器路径，并带有斜杠。
|
*/

$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| 缓存包含查询字符串
|--------------------------------------------------------------------------
|
| 是否在生成输出缓存文件时考虑 URL 查询字符串。有效选项是：
|
|   FALSE      = 禁用
|   TRUE       = 启用，考虑所有查询参数。
|                请注意，这可能会导致同一页面生成大量缓存文件。
|   array('q') = 启用，但仅考虑指定的查询参数列表。
|
*/

$config['cache_query_string'] = false;

/*
|--------------------------------------------------------------------------
| 加密密钥
|--------------------------------------------------------------------------
|
| 如果您使用加密类，您必须设置一个加密密钥。
| 更多信息请参见用户指南。
|
| https://codeigniter.com/userguide3/libraries/encryption.html
|
*/

$config['encryption_key'] = '';

/*
|--------------------------------------------------------------------------
| Cookie 相关变量
|--------------------------------------------------------------------------
|
| 'cookie_prefix'   = 如果您需要避免冲突，可以设置 cookie 名称前缀
| 'cookie_domain'   = 设置为.your-domain.com 以实现全站 cookie
| 'cookie_path'     = 通常是一个斜杠
| 'cookie_secure'   = 仅在存在安全 HTTPS 连接时设置 cookie
| 'cookie_httponly' = cookie 只能通过 HTTP(S) 访问（无 JavaScript）
| 'cookie_samesite' = cookie 的 samesite 属性（Lax、Strict 或 None）
|
| 注意：这些设置（除了 'cookie_prefix' 和 'cookie_httponly'）也会影响会话。
|
*/

$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = false;
$config['cookie_httponly'] = false;
$config['cookie_samesite'] = 'Lax';




/*

|--------------------------------------------------------------------------

| Standardize newlines

|--------------------------------------------------------------------------

|

| Determines whether to standardize newline characters in input data,

| meaning to replace \r\n, \r, \n occurrences with the PHP_EOL value.

|

| WARNING: This feature is DEPRECATED and currently available only

|          for backwards compatibility purposes!

|

*/

$config['standardize_newlines'] = false;



/*

|--------------------------------------------------------------------------

| Global XSS Filtering

|--------------------------------------------------------------------------

|

| Determines whether the XSS filter is always active when GET, POST or

| COOKIE data is encountered

|

*/

$config['global_xss_filtering'] = true;



/*

|--------------------------------------------------------------------------

| Cross Site Request Forgery

|--------------------------------------------------------------------------

| Enables a CSRF cookie token to be set. When set to TRUE, token will be

| checked on a submitted form. If you are accepting user data, it is strongly

| recommended CSRF protection be enabled.

|

| 'csrf_token_name' = The token name

| 'csrf_cookie_name' = The cookie name

| 'csrf_expire' = The number in seconds the token should expire.

| 'csrf_regenerate' = Regenerate token on every submission

| 'csrf_exclude_uris' = Array of URIs which ignore CSRF checks

*/

$config['csrf_protection'] = true;

$config['csrf_token_name'] = 'csrf_token_name';

$config['csrf_cookie_name'] = 'csrf_cookie_name';

$config['csrf_expire'] = 7200;

$config['csrf_regenerate'] = true;

$config['csrf_exclude_uris'] = ['donate', 'vote/callback', '.*callback.*+', '.*install.*+'];



/*

|--------------------------------------------------------------------------

| Output Compression

|--------------------------------------------------------------------------

|

| Enables Gzip output compression for faster page loads.  When enabled,

| the output class will test whether your server supports Gzip.

| Even if it does, however, not all browsers support compression

| so enable only if you are reasonably sure your visitors can handle it.

|

| Only used if zlib.output_compression is turned off in your php.ini.

| Please do not use it together with httpd-level output compression.

|

| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it

| means you are prematurely outputting something to your browser. It could

| even be a line of whitespace at the end of one of your scripts.  For

| compression to work, nothing can be sent before the output buffer is called

| by the output class.  Do not 'echo' any values with compression enabled.

|

*/

$config['compress_output'] = true;



/*

|--------------------------------------------------------------------------

| Master Time Reference

|--------------------------------------------------------------------------

|

| Option is 'gmt'.  This pref tells the system whether to use

| your server's local time as the master 'now' reference, or convert it to

| GMT.  See the 'date helper' page of the user guide for information

| regarding date handling.

|

*/

//$config['time_reference'] = 'gmt';



/*

|--------------------------------------------------------------------------

| Rewrite PHP Short Tags

|--------------------------------------------------------------------------

|

| If your PHP installation does not have short tag support enabled CI

| can rewrite the tags on-the-fly, enabling you to utilize that syntax

| in your view files.  Options are TRUE or FALSE (boolean)

|

| Note: You need to have eval() enabled for this to work.

|

*/

$config['rewrite_short_tags'] = false;



/*

|--------------------------------------------------------------------------

| Reverse Proxy IPs

|--------------------------------------------------------------------------

|

| If your server is behind a reverse proxy, you must whitelist the proxy

| IP addresses from which CodeIgniter should trust headers such as

| HTTP_X_FORWARDED_FOR and HTTP_CLIENT_IP in order to properly identify

| the visitor's IP address.

|

| You can use both an array or a comma-separated list of proxy addresses,

| as well as specifying whole subnets. Here are a few examples:

|

| Comma-separated:  '10.0.1.200,192.168.5.0/24'

| Array:        array('10.0.1.200', '192.168.5.0/24')

*/

$config['proxy_ips'] = '';

