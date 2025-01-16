<?php if (! defined('BASEPATH')) exit('不允许直接脚本访问。');

/**
 * Output TinyMCE script
 */
function TinyMCE()
{
    $CI = &get_instance();

    return $CI->smarty->view($CI->template->view_path . 'tinymce.tpl', ['url' => pageURL], true);
}
