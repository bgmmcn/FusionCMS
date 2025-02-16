<?php namespace MX;

use CI_Lang;

defined('BASEPATH') or exit('不允许直接脚本访问。');

defined('EXT') or define('EXT', '.php');

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 *
 * @link http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Language class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Lang.php
 *
 * @copyright Copyright (c) 2011 Wiredesignz
 * @version   5.5
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
 **/
class MX_Lang extends CI_Lang
{
    /**
     * [load description]
     *
     * @method load
     *
     * @param [type]  $langfile   [description]
     * @param string  $lang       [description]
     * @param boolean $return     [description]
     * @param boolean $add_suffix [description]
     * @param string  $alt_path   [description]
     * @param string  $_module    [description]
     *
     * @return [type]              [description]
     */
    public function load($langFile = [], $lang = '', $return = false, $add_suffix = true, $alt_path = '', string $_module = '')
    {
        if (is_array($langFile)) {
            foreach ($langFile as $_lang) {
                $this->load($_lang);
            }
            return $this->language;
        }

        $deft_lang = CI::$APP->config->item('language');
        $idiom = ($lang === '') ? $deft_lang : $lang;

        if (in_array($langFile . '_lang' . EXT, $this->is_loaded, true)) {
            return $this->language;
        }

        $_module or $_module = CI::$APP->router->fetch_module();

        [$path, $_langfile] = MX_Modules::find($langFile . '_lang', $_module, 'language/' . $idiom . '/');

        if ($path === false) {
            if ($lang = parent::load($langFile, $lang, $return, $add_suffix, $alt_path)) {
                return $lang;
            }
        } else {
            if ($lang = MX_Modules::load_file($_langfile, $path, 'lang')) {
                if ($return) {
                    return $lang;
                }
                $this->language = array_merge($this->language, $lang);
                $this->is_loaded[] = $langFile . '_lang' . EXT;
                unset($lang);
            }
        }

        return $this->language;
    }
}
