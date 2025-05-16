<?php

/**
 * @package FusionCMS
 * @version 6.0
 * @link    https://github.com/FusionWowCMS/FusionCMS
 */

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
  |--------------------------------------------------------------------------
  | Default Language
  |--------------------------------------------------------------------------
  |
  | Also acts as fallback language
  |
 */
$config['language'] = "simplified-chinese";

/*
  |--------------------------------------------------------------------------
  | Detect Browser Language
  |--------------------------------------------------------------------------
  |
  | If enabled detecting browser language and set user language to detected language
  |
 */
$config['detect_language'] = false;

/*
|--------------------------------------------------------------------------
| Supported Languages
|--------------------------------------------------------------------------
*/
$config['supported_languages'] = array(
    'cn' => array('name' => 'simplified-chinese')
);
