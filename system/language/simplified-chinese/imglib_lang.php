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

$lang['imglib_source_image_required'] = '请指定源图像';
$lang['imglib_gd_required'] = '此功能需要 GD 图形库';
$lang['imglib_gd_required_for_props'] = '服务器必须支持 GD 图形库才能获取图像属性';
$lang['imglib_unsupported_imagecreate'] = '服务器不支持处理此类型图像所需的 GD 函数';
$lang['imglib_gif_not_supported'] = '由于许可限制，GIF 图像通常不被支持，请使用 JPG 或 PNG 图像';
$lang['imglib_jpg_not_supported'] = '不支持 JPG 图像';
$lang['imglib_png_not_supported'] = '不支持 PNG 图像';
$lang['imglib_jpg_or_png_required'] = '您设置的图像调整协议仅适用于 JPEG 或 PNG 图像类型';
$lang['imglib_copy_error'] = '替换文件时发生错误，请确保文件目录可写';
$lang['imglib_rotate_unsupported'] = '服务器不支持图像旋转';
$lang['imglib_libpath_invalid'] = '图像库路径不正确，请在图像首选项中设置正确路径';
$lang['imglib_image_process_failed'] = '图像处理失败，请确认服务器支持所选协议且图像库路径正确';
$lang['imglib_rotation_angle_required'] = '旋转图像需要指定旋转角度';
$lang['imglib_invalid_path'] = '图像路径不正确';
$lang['imglib_invalid_image'] = '提供的图像无效';
$lang['imglib_copy_failed'] = '图像复制失败';
$lang['imglib_missing_font'] = '找不到可用字体';
$lang['imglib_save_failed'] = '无法保存图像，请确保图像和文件目录可写';
