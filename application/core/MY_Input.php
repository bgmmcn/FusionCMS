<?php

if (! defined('BASEPATH')) {
    exit('不允许直接脚本访问。');
}

class MY_Input extends CI_Input
{
    public function _sanitize_globals()
    {
        $ignore_csrf = config_item('csrf_ignore');

        if (is_array($ignore_csrf) && count($ignore_csrf)) {
            global $URI;
            $haystack = $URI->uri_string();

            foreach ($ignore_csrf as $needle) {
                if (strlen($haystack) >= strlen($needle) && str_starts_with($haystack, $needle)) {
                    $this->_enable_csrf = false;
                    break;
                }
            }
        }

        parent::_sanitize_globals();
    }
}
/* EOF: MY_Input */
