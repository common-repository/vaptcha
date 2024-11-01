<?php
//添加常规选项
function vaptcha_register_fields() {
    register_setting( 'general', 'vaptcha_fields_des' );
    $title = '<label for="vaptcha_fields_des">' . __( 'website description', 'vaptcha' ) . '</label>';
    add_settings_field( 'vaptcha_fields_des', $title, 'vaptcha_fields_des', 'general' );
}
function vaptcha_fields_des() {
    $value = get_option( 'vaptcha_fields_des', '' );
    $des   = '<p class="description">' . __( 'Displayed in the description tab of the first page', 'vaptcha' ) . '</p>';
    echo '<textarea name="vaptcha_fields_des" id="vaptcha_fields_des" class="large-text code" rows="3">' . $value . '</textarea>';
    echo $des;
}
add_filter( 'admin_init', 'vaptcha_register_fields' );