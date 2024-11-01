<?php
/*
Plugin Name: VAPTCHA 手势验证码
Plugin URI: https://en.vaptcha.com
Description: VAPTCHA是基于人工智能和大数据的创新人机验证解决方案。 通过综合分析用户的行为特征、生物特征、网络环境等，VAPTCHA 高效、不断进化的智能风控引擎能准确的识别并拦截包括人工打码在 内的攻击请求。与传统验证码相比，无论在安全级别还是用户体验， VAPTCHA都有显著的优势.
Version: 3.0.4
Author: vaptcha
Text Domain: vaptcha
Domain Path: /languages
Author URI: https://github.com/vaptcha
*/

/*  Copyright 2017  vaptcha  (email : vaptcha@wlinno.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined('VAPTCHA_BASENAME') || define('VAPTCHA_BASENAME', plugin_basename(__FILE__));
defined('VAPTCHA_URL') || define('VAPTCHA_URL', plugins_url('vaptcha'));//update -----------------------------------------------------------------
defined('VAPTCHA_DIR') || define('VAPTCHA_DIR', VAPTCHA_BASENAME . '/js/');//update -----------------------------------------------------------------

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

require_once plugin_dir_path(__FILE__) . 'VaptchaPlugin.php';
require __DIR__ . '/setting-vaptcha.php';

function load_vaptcha_domain() {
    load_plugin_textdomain( 'vaptcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'load_vaptcha_domain' );

if (class_exists("VaptchaPlugin")) {
    $vaptcha = new VaptchaPlugin();
    $vaptcha->init();
}
?>