<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['db_invalid_connection_str'] = '无法根据您提交的连接字符串确定数据库设置';
$lang['db_unable_to_connect'] = '无法使用提供的设置连接到数据库服务器';
$lang['db_unable_to_select'] = '无法选择指定的数据库：%s';
$lang['db_unable_to_create'] = '无法创建指定的数据库：%s';
$lang['db_invalid_query'] = '您提交的查询无效。';
$lang['db_must_set_table'] = '您必须设置要用于查询的数据库表。';
$lang['db_must_use_set'] = '您必须使用“set”方法更新条目。';
$lang['db_must_use_index'] = '您必须指定一个索引来匹配批量更新。';
$lang['db_batch_missing_index'] = '提交批量更新的行中缺少指定的索引。';
$lang['db_must_use_where'] = '更新不允许，除非包含“where”子句。';
$lang['db_del_must_use_where'] = '删除不允许，除非包含“where”或“like”子句。';
$lang['db_field_param_missing'] = '获取字段需要将表名作为参数。';
$lang['db_unsupported_function'] = '您使用的数据库不支持此功能。';
$lang['db_transaction_failure'] = '事务失败：已执行回滚。';
$lang['db_unable_to_drop'] = '无法删除指定的数据库。';
$lang['db_unsupported_feature'] = '您使用的数据库平台不支持此功能。';
$lang['db_unsupported_compression'] = '您选择的文件压缩格式不受服务器支持。';
$lang['db_filepath_error'] = '无法将数据写入您提交的文件路径。';
$lang['db_invalid_cache_path'] = '您提交的缓存路径无效或不可写。';
$lang['db_table_name_required'] = '该操作需要表名。';
$lang['db_column_name_required'] = '该操作需要列名。';
$lang['db_column_definition_required'] = '该操作需要列定义。';
$lang['db_unable_to_set_charset'] = '无法设置客户端连接字符集：%s';
$lang['db_error_heading'] = '数据库错误';
