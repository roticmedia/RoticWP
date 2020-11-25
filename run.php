<?php

/*
Plugin Name: Rotic Plugin
Plugin URI: http://rotic.ir
Description: Connect your website to the Rotic, because you contacted to the future.
Version: 1.0
Author: Rotic Team
Author URI: http://rotic.ir
License: MIT
*/

function pluginprefix_install()
{
//ایجاد جدول
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $create_table = "
CREATE TABLE {$wpdb->prefix}rotic (
token varchar(512) COLLATE utf8_persian_ci NOT NULL
api varchar(512) COLLATE utf8_persian_ci NOT NULL
) CHARSET=utf8 COLLATE=utf8_persian_ci;
";
    dbDelta($create_table);
}

register_activation_hook(__FILE__, 'pluginprefix_install');
add_action('admin_menu', 'menu_builder');
function menu_builder()
{
    add_menu_page('پشتیبانی روتیک', 'پشتیبانی روتیک', 'manage_options', 'rotic', '', "https://rotic.ir/images/wordpress-150x.png");
}

add_action('admin_menu', 'option_builder');
function option_builder()
{
    add_options_page("پشتیبانی روتیک", "پشتیبانی روتیک", 'manage_options', 'rotic', 'page_builder');
}

$token = get_option('token')['token'];
$api = get_option('token')['api'];
function page_builder()
{
    if (isset($_POST['save'])) {
        $_POST['token'] = esc_sql($_POST['token']);
        $_POST['api'] = esc_sql($_POST['api']);
        update_option('token', $_POST);
        update_option('api', $_POST);
        echo '<div class="error"><p>تنظیمات با موفقیت ذخیره شد</p></div>';
    }
    $token = get_option('token')['token'];
    $api = get_option('token')['api'];
    ?>
    <div class="wrap">
        <h2>تنظیمات وب سرویس روتیک</h2>

        <form method="post" enctype="multipart/form-data">
            <table class="widefat">
                <thead>
                <tr>
                    <th colspan="۲">تنظیمات</th>
                </tr>
                <tr style="width: 100%;text-align: center">
                    <th colspan="۲" style="width: 100%;text-align: center;margin: 10%"><img
                                src="https://rotic.ir/images/rotic-full-cyan.png" width="10%" alt="Rotic"></th>
                </tr>
                </thead>
                <tr>
                    <td style="width: 100%;text-align: center;margin: 10%">
                        <label for="webtoken">
                            تنظیم توکن روتیک (
                            <a href="https://rotic.ir/panel" target="_blank">دریافت توکن کسب و کار</a>
                             و
                            <a href="https://rotic.ir/panel/bots" target="_blank">دریافت توکن بات</a>
                            )
                        </label>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;text-align: center;margin: 10%">
                        <input type="password" size="۱۰۰" id="webtoken" style="width: 100%;text-align: center"
                               name="token"
                               placeholder="لطفا توکن کسب و کار خود را از پنل روتیک وارد کنید"
                               required=""
                               value="<?php echo empty($token) ? '' : $token ?>"/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;text-align: center;margin: 10%">
                        <input type="password" size="۱۰۰" id="webapi" style="width: 100%;text-align: center"
                               name="api"
                               placeholder="لطفا توکن بات خود را از پنل روتیک وارد کنید"
                               required=""
                               value="<?php echo empty($api) ? '' : $api ?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="۲">
                        <input type="submit" name="save" value="ذخیره" class="button-primary"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php
}

function add_script()
{
    $token = get_option('token')['token'];
    $api = get_option('token')['api'];
    echo '<script src="https://rotic.ir/api/v1/enterprise/' . $token . '/widget/'.$api.'"></script>';
}

add_action('wp_footer', 'add_script');