<?php
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

function vaptcha_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class='wrap vaptcha-wrap'>
        <h2></h2>
        <?php
        $options = get_option("vaptcha_options");
        ?>
        <div class="vaptcha-header">
            <div class='vaptcha-badge'></div>
            <div>
                <h2><?php _e('VAPTCHA智能人机验证', 'vaptcha') ?></h2>
                <p class="vaptcha-about-text">
                <?php _e('VAPTCHA手势验证码是基于人工智能和大数据的次世代人机验证解决方案，安全强度极高，是目前唯一未被自动化程序破解的验证码。快速接入，完全免费，请放心使用.', 'vaptcha'); ?>
            </div>
        </div>
        <form name="form" action="options.php" method="post">
            <?php
            settings_fields('vaptcha_options_group');
            ?>
            <p class="get-vaptcha-key"><?php _e('请登录VAPTCHA官网免费获取VID及Key，填写保存后即可生效。官网地址:',  'vaptcha'); ?>  <a href="https://en.vaptcha.com" target="_blank" title="vaptcha"><?php _e('https://en.vaptcha.com', 'uncr_translate'); ?></a></p>
            <table class="form-table">
                <tbody>
                <tr>
                    <th class="form-title"><?php _e('人机验证设置', 'vaptcha'); ?></th>
                    <td>
                        <?php _e('（登录VAPTCHA - 创建验证单元  -  免费获取）', 'vaptcha'); ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="public_key_text">VID</label></th>
                    <td>
                        <fieldset>
                            <input placeholder="" type="text" id="public_key_text" name="vaptcha_options[vaptcha_vid]"
                                   value="<?php echo $options['vaptcha_vid'] ?>">
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><label for="public_key_text">KEY</label></th>
                    <td>
                        <fieldset>
                            <input placeholder="" type="text" id="public_key_text" name="vaptcha_options[vaptcha_key]"
                                   value="<?php echo $options['vaptcha_key'] ?>">
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="button_style"><?php _e('按钮风格', 'vaptcha') ?></label></th>
                    <td>
                        <fieldset class="vaptcha-radio-field-wrapper">
                            <input id="invisible"
                                   type="radio" <?php if ('dark' == $options['button_style']) echo 'checked="checked"'; ?>
                                   name="vaptcha_options[button_style]" value="dark">
                            <label for="invisible"><?php _e('深色', 'vaptcha') ?></label>
                            <input id="normal"
                                   type="radio" <?php if ('light' == $options['button_style']) echo 'checked="checked"'; ?>
                                   name="vaptcha_options[button_style]" value="light">
                            <label for="normal"><?php _e('浅色', 'vaptcha') ?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><label for="public_key_text"><?php _e('按钮颜色', 'vaptcha') ?></label></th>
                    <td>
                        <fieldset>
                            <input type="text" id="public_key_text" name="vaptcha_options[bg_color]"
                                   value="<?php echo $options['bg_color'] ?>">
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><label for="public_key_text"><?php _e('按钮高度(PX)', 'vaptcha') ?></label></th>
                    <td>
                        <fieldset>
                            <input type="text" id="public_key_text" name="vaptcha_options[vaptcha_height]"
                                   value="<?php echo $options['vaptcha_height'] ?>">
                        </fieldset>
                    </td>
                    <td class='descript'> <?php _e('不得低于36px', 'vaptcha') ?> </td>
                </tr>
                <tr>
                    <th><label for="public_key_text"><?php _e('按钮宽度(PX)', 'vaptcha') ?></label></th>
                    <td>
                        <fieldset>
                            <input type="text" id="public_key_text" name="vaptcha_options[vaptcha_width]"
                                   value="<?php echo $options['vaptcha_width'] ?>">
                        </fieldset>
                    </td>
                    <td class='descript'> <?php _e('不得低于200px', 'vaptcha') ?> </td>
                </tr>
                <tr>
                    <th class="form-title"><b style="font-size: 16px;"><?php _e('其他设置', 'vaptcha') ?></b></th>
                </tr>
                <tr>
                    <th><label for="public_key_text"><?php _e('启用模块', 'vaptcha') ?></label></th>
                    <td>
                        <fieldset>
                            <div class="vaptcha-checkbox-wrapper">
                                <input id="vaptcha_login_form" type="checkbox" name="vaptcha_options[vaptcha_login]"
                                       value="1"
                                    <?php if (1 == $options['vaptcha_login']) echo 'checked="checked"'; ?>>
                                <label for="vaptcha_login_form"><?php _e('登录', 'vaptcha') ?></label>
                            </div>
                            <div class="vaptcha-checkbox-wrapper">
                                <input id="register_open" type="checkbox" name="vaptcha_options[vaptcha_register]"
                                       value="1"
                                    <?php checked($options['vaptcha_register'], 1); ?>>
                                <label for="register_open"><?php _e('注册', 'vaptcha') ?></label>
                            </div>
                            <div class="vaptcha-checkbox-wrapper">
                                <input id="comment_open" type="checkbox" name="vaptcha_options[vaptcha_comment]"
                                       value="1"
                                    <?php if (1 == $options['vaptcha_comment']) echo 'checked="checked"'; ?>>
                                <label for="comment_open"><?php _e('评论', 'vaptcha') ?></label>
                            </div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><label for="public_key_text"><?php _e('语言设定', 'vaptcha') ?></label></th>
                    <td>
                        <fieldset>
                            <select name="vaptcha_options[vaptcha_lang]">
                                <option value=" " <?php selected($options['vaptcha_lang'], ' '); ?>>Auto Detect</option>
                                <option value="en" <?php selected($options['vaptcha_lang'], 'en'); ?>>English</option>
                                <option value="zh-CN" <?php selected($options['vaptcha_lang'], 'zh-CN'); ?>>中文简体
                                </option>
                                <option value="zh-TW" <?php selected($options['vaptcha_lang'], 'zh-TW'); ?>>中文繁體
                                </option>
                                <option value="jp" <?php selected($options['vaptcha_lang'], 'jp'); ?>>にほんご</option>
                            </select>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><label for="public_key_text"><?php _e('验证节点', 'vaptcha') ?></label></th>
                    <td>
                        <fieldset>
                            <select name="vaptcha_options[vaptcha_area]">
                                <option value=" " <?php selected($options['vaptcha_area'], ' '); ?>><?php _e('中国大陆', 'vaptcha') ?></option>
                                <option value="en" <?php selected($options['vaptcha_area'], 'sea'); ?>><?php _e('东南亚', 'vaptcha') ?></option>
                                <option value="zh-CN" <?php selected($options['vaptcha_area'], 'na'); ?>><?php _e('北美', 'vaptcha') ?></option>
                                <option value="zh-TW" <?php selected($options['vaptcha_area'], 'cn'); ?>><?php _e('欧洲', 'vaptcha') ?></option>
                            </select>
                        </fieldset>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="deadline"></p>
            <?php submit_button(__('保存设置', 'vaptcha')); ?>
        </form>
    </div>
    <?php
}