<?php
if ( !defined('ABSPATH') ) {
   exit('No direct script access allowed');
}

require_once plugin_dir_path( __FILE__ ) . 'lib/Vaptcha.class.php';
require_once plugin_dir_path( __FILE__ ) . 'options.php';

class VaptchaPlugin
{
    private $vaptcha;

    private $options;
    
    public function init() {
        register_activation_hook(plugin_dir_path( __FILE__ ). 'vaptcha.php', array($this, 'init_default_options'));
        register_activation_hook(plugin_dir_path( __FILE__ ) . 'vaptcha.php', array($this, 'uninstall'));
        $this->init_add_actions();

        $options = get_option('vaptcha_options');
        $this->vaptcha = new Vaptcha($options['vaptcha_vid'], $options['vaptcha_key']);          
        $this->options = $options;
    }

    public function offline() {
        $offline_action = sanitize_text_field( $_GET['offline_action'] );
        $callback = sanitize_text_field( $_GET['callback'] );
        $v = sanitize_text_field( $_GET['v'] );
        $knock = sanitize_text_field( $_GET['knock'] );
        return $this->vaptcha->downTime($offline_action, $callback, $v, $knock);
    }

    private function get_captcha($form, $btn) {
        $script =  plugins_url( 'js/init-vaptcha.js', __FILE__ );
        $loading =  plugins_url( 'images/vaptcha-loading.gif', __FILE__ );
        $vid = get_option('vaptcha_options')['vaptcha_vid'];
        $lang = get_option('vaptcha_options')['vaptcha_lang'];
        $height = get_option('vaptcha_options')['vaptcha_height'];
        $width = get_option('vaptcha_options')['vaptcha_width'];
        $color = get_option('vaptcha_options')['bg_color'];
        $style = get_option('vaptcha_options')['button_style'];
        $height = $height ? $height : '36px';
        $area = get_option('vaptcha_options')['vaptcha_area'];
        $options = json_encode(Array(
            "vid" => $vid,
            'type' => 'click',
            "lang" => $lang,
            "style" => $style,
            "https" => true,
            "color" => $color,
            "area" => $area,
            "offline_server" => site_url() . '/wp-json/vaptcha/offline',
            // 'mode' => 'offline',
        ));
        return <<<HTML
        <style>
            .vaptcha-container{
                height: $height;
                width: $width;
                margin-bottom: 10px;
            }
            .vaptcha-init-main{
                display: table;
                width: 100%;
                height: 100%;
                background-color: #EEEEEE;
            }
            .vaptcha-init-loading {
                display: table-cell;
                vertical-align: middle;
                text-align: center
            }

            .vaptcha-init-loading>a {
                display: inline-block;
                width: 18px;
                height: 18px;
            }
            .vaptcha-init-loading>a img{
                vertical-align: middle
            }
            .vaptcha-init-loading .vaptcha-text {
                font-family: sans-serif;
                font-size: 12px;
                color: #CCCCCC;
                vertical-align: middle
            }
            .vaptcha-init-loading .vaptcha-text a {
                font-family: sans-serif;
                font-size: 12px;
                color: #CCCCCC;
                text-decoration: none;
            }
        </style>
        <div class="vaptcha-container" data-config='$options'>
            <div class="vaptcha-init-main">
                <div class="vaptcha-init-loading">
                    <a><img src="$loading"/></a>
                    <span class="vaptcha-text"><a href="https://www.vaptcha.com/" title="CAPTCHA" target="_blank">CAPTCHA</a>is initialing...</span>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="$script"></script>
HTML;
    }
    
    /**
     * 评论框
     */
    function captcha_in_comments( $post_id ) {
        if($this->options['vaptcha_comment'] == 0) return ;
        echo $this->get_captcha('commentform', 'submit');
        echo <<<HTML
        <script>
            var btn = document.getElementsByClassName('vaptcha-container')[0]
            var form = document.getElementById('commentform')
            form.insertBefore(btn, document.getElementsByClassName('form-submit')[0])
        </script>
HTML;
    }
    
    function captcha_validate_comment($comment_data) {
        if($this->options['vaptcha_comment'] == 0) return $comment_data;        
        if(!isset($_POST['vaptcha_challenge'])){
             $challenge = '';
        }else{
             $challenge =sanitize_text_field( $_POST['vaptcha_challenge'] );
        }
        $token = sanitize_text_field( $_POST['vaptcha_token'] );
        $server = sanitize_text_field( $_POST['vaptcha_server'] );
        if (!$token || !$this->vaptcha->validate($server,$challenge, $token)) {
            wp_die(__('人机验证未通过'.$token, 'vaptcha'));
        }
        return $comment_data;        
    }

    /**
     * 登录
     */
    function captcha_in_login_form() {
        if($this->options['vaptcha_login'] == 0) return ;
        echo $this->get_captcha('loginform', 'submit');
    }

    function captcha_validate_login($user) {
        if($this->options['vaptcha_login'] == 0) return $user;
        if(!isset($_POST['vaptcha_challenge'])){
             $challenge = '';
        }else{
             $challenge =sanitize_text_field( $_POST['vaptcha_challenge'] );
        }
        $token = sanitize_text_field( $_POST['vaptcha_token'] );
        $server = sanitize_text_field( $_POST['vaptcha_server'] );
        if (!$token || !$this->vaptcha->validate($server,$challenge, $token)) {
            return  new WP_Error('broke', __('人机验证未通过', 'vaptcha'));
        }
        return $user;  
    }

    function captcha_in_register_form() {
        if($this->options['vaptcha_register'] == 0) return ;
        echo $this->get_captcha('registerform', 'submit');        
    }

    function captcha_validate_register($errors) {
        if($this->options['vaptcha_register'] == 0) return $errors;
        if(!isset($_POST['vaptcha_challenge'])){
             $challenge = '';
        }else{
             $challenge =sanitize_text_field( $_POST['vaptcha_challenge'] );
        }
        $token = sanitize_text_field( $_POST['vaptcha_token'] );
        $server = sanitize_text_field( $_POST['vaptcha_server'] );
        if (!$token || !$this->vaptcha->validate($server,$challenge, $token)) {
            $errors->add('captcha_wrong', "<strong>ERROR</strong>：".__('人机验证未通过', 'vaptcha'));   
        }
        return $errors;
    }

    function vaptcha_settings_init() {
        register_setting('vaptcha_options_group', 'vaptcha_options', array($this, 'validate_options'));
    }

    function validate_options($input) {
        $validated['vaptcha_vid'] = sanitize_text_field($input['vaptcha_vid']);
        $validated['vaptcha_key'] = sanitize_text_field($input['vaptcha_key']);
        $validated['vaptcha_comment'] = ($input['vaptcha_comment'] == "1" ? "1" : "0");
        $validated['vaptcha_register'] = ($input['vaptcha_register'] == "1" ? "1" : "0");
        $validated['vaptcha_login'] = ($input['vaptcha_login'] == "1" ? "1" : "0");
        $validated['vaptcha_lang'] = sanitize_text_field($input['vaptcha_lang']);
        $validated['vaptcha_area'] = sanitize_text_field($input['vaptcha_area']);
        $validated['bg_color'] = sanitize_text_field($input['bg_color']);
        $validated['vaptcha_width'] = sanitize_text_field($input['vaptcha_width']);
        $validated['vaptcha_height'] = sanitize_text_field($input['vaptcha_height']);
        $validated['button_style'] = ($input['button_style'] == "light" ? "light" : "dark");
        return $validated;
    }

    function vaptcha_options_page() {
        add_submenu_page('options-general.php',
        'VAPTCHA',
        'VAPTCHA',
        'manage_options',
        'vaptcha',
        'vaptcha_options_page_html');
    }

    function init_default_options() {
        if (!get_option('vaptcha_options')) {
            $options = array(
                'vaptcha_vid' => '',
                'vaptcha_key' => '',
                'vaptcha_comment' => '1',
                'vaptcha_register' => '1',
                'vaptcha_login' => '1',
                'vaptcha_lang' => 'auto',
                'vaptcha_area' => 'auto',
                'bg_color' => '#57ABFF',
                'vaptcha_width' => '200',
                'vaptcha_height' => '36',
                'https' => true,
                'button_style' => 'dark',
                'type' => 'click',
                "offline_server" => site_url() . '/wp-json/vaptcha/offline',
                // 'mode' => 'offline',
            );
            add_option('vaptcha_options', $options);
        }
    }

    function uninstall() {
        unregister_setting("vaptcha_options_group", 'vaptcha_options');        
    }

    function load_textdomain() {
        load_plugin_textdomain( 'vaptcha', false , dirname( plugin_basename( __FILE__ ) ) . '/languages' );        
    }

    function get_vaptcha_api() {
        header('Content-Type: application/javascript');
        return json_decode($this->vaptcha->getChallenge());
    }
    function get_downtime_api() {
        header('Content-Type: application/javascript');
        $offline_action = sanitize_text_field( $_GET['offline_action'] );
        $callback = sanitize_text_field( $_GET['callback'] );
        return $this->vaptcha->downTime($offline_action, $callback);
    }

    function captcha_in_woocommerce() {
        echo $this->get_captcha('woocommerce-form-register', 'submit');
    }

    function captcha_validate_woocommerce($errors) {
        $token = $_POST['vaptcha_token'];
        $server = sanitize_text_field( $_POST['vaptcha_server'] );
        if (!$token || !$this->vaptcha->validate($server,'', $token)) {
            $errors->add('captcha_wrong', __('人机验证未通过', 'vaptcha'));   
        }
        return $errors;
    }

    function captcha_validate_woocommerce_allow($data) {
        $token = $_POST['vaptcha_token'];
        $server = sanitize_text_field( $_POST['vaptcha_server'] );
        if (!$token || !$this->vaptcha->validate($server,'', $token)) {
            return new WP_Error('captcha_wrong', __('人机验证未通过', 'vaptcha'));
        }
        return $data;
    }

    function back_end_styles() {
		// load styles
		wp_register_style( 'vaptcha-setting-style', plugin_dir_url( __FILE__ ) . '/css/back-end-styles.css', false, '1.0' );
		wp_enqueue_style( 'vaptcha-setting-style' );

    }

    //添加常规选项
//    function vaptcha_register_fields() {
//        register_setting( 'general', 'vaptcha_fields_des' );
//        $title = '<label for="customize_fields_des">' . __( 'website description', 'vaptcha' ) . '</label>';
//        add_settings_field( 'customize_fields_des', $title, 'vaptcha_fields_des', 'general' );
//    }
//    function vaptcha_fields_des() {
//        $value = get_option( 'vaptcha_fields_des', '' );
//        $des   = '<p class="description">' . __( 'Displayed in the description tab of the first page', 'vaptcha' ) . '</p>';
//        echo '<textarea name="vaptcha_fields_des" id="vaptcha_fields_des" class="large-text code" rows="3">' . $value . '</textarea>';
//        echo $des;
//    }

    function init_add_actions() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('comment_form', array($this, 'captcha_in_comments'));
        add_action('login_form', array($this, 'captcha_in_login_form'));
        add_action('register_form', array($this, 'captcha_in_register_form'));      
        add_action('admin_init', array($this, 'vaptcha_settings_init'));
        add_action('admin_menu', array($this, 'vaptcha_options_page'));
        // load styles
		add_action( 'admin_enqueue_scripts', array( $this, 'back_end_styles' ) );
        add_action('woocommerce_login_form', array($this, 'captcha_in_woocommerce'));
        add_action('woocommerce_register_form', array($this, 'captcha_in_woocommerce'));
        add_action('woocommerce_lostpassword_form', array($this, 'captcha_in_woocommerce'));

        //api
        add_action('rest_api_init', function () {
            register_rest_route( 'vaptcha', '/getchallenge', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_vaptcha_api'),
            ));
            register_rest_route( 'vaptcha', '/offline', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_downtime_api'),
            ));
        });

        add_filter('preprocess_comment', array($this, 'captcha_validate_comment'), 100,1);
        add_filter('wp_authenticate_user', array($this, 'captcha_validate_login'),100,1);
        add_filter('registration_errors', array($this, 'captcha_validate_register'),100,1);

        add_filter('woocommerce_process_registration_errors', array($this, 'captcha_validate_woocommerce'),100,1);
        // 插件列表加入设置按钮
        add_filter('plugin_action_links', array($this, 'pluginSettingPageLinkButton'), 10, 2);

//        add_filter( 'admin_init',  array($this, 'vaptcha_register_fields'), 10, 2);
    }

    /**
     * 插件列表添加设置按钮
     * @param $links
     * @param $file
     * @return mixed
     */
    public function pluginSettingPageLinkButton($links, $file)
    {
        if ($file === VAPTCHA_BASENAME) {
            $links[] = '<a href="admin.php?page=vaptcha">Setting</a>';
        }
        return $links;
    }



}