<?php

/*
Plugin Name: Rotic Plugin
Plugin URI: https://github.com/roticmedia/RoticWP/releases
Description: Connect your website to the Rotic, because you contacted to the future..
Version: 2.0.2
Author: Milad Xandi
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
    add_menu_page('افزونه روتیک', 'افزونه روتیک', 'manage_options', 'rotic', '', "https://rotic.ir/images/wordpress-150x.png");
}

add_action('admin_menu', 'option_builder');
function option_builder()
{
    add_options_page("افزونه روتیک", "افزونه روتیک", 'manage_options', 'rotic', 'page_builder');
}

$token = get_option('token')['token'];
$api = get_option('token')['api'];
$driver = get_option('token')['driver'];
function page_builder()
{
    if (isset($_POST['save'])) {
        $_POST['token'] = esc_sql($_POST['token']);
        $_POST['api'] = esc_sql($_POST['api']);
        $_POST['driver'] = esc_sql($_POST['driver']);
        update_option('token', $_POST);
        update_option('api', $_POST);
        update_option('driver', $_POST);
        echo '<div class="error"><p>تنظیمات با موفقیت ذخیره شد</p></div>';
    }
    $token = get_option('token')['token'];
    $api = get_option('token')['api'];
    $driver = get_option('token')['driver'];
    ?>
    <link rel="stylesheet" href="https://rotic.ir/css/custom.css" >
    <div class="wrap">
        <h2 class="persian" >تنظیمات وب سرویس روتیک</h2>

        <form method="post"  class="persian"  enctype="multipart/form-data">
            <table class="widefat">
                <thead>
                <tr>
                    <th colspan="2">تنظیمات</th>
                </tr>
                <tr style="width: 100%;text-align: center">
                    <th colspan="2" style="width: 100%;text-align: center;margin: 10%"><img
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
                        <label for="webtoken">توکن کسب و کار:</label>
                    </td>
                    <td style="width: 100%;text-align: center;margin: 10%">
                        <input type="password" class="persian" size="100" id="webtoken" style="width: 100%;text-align: center"
                               name="token"
                               placeholder="لطفا توکن کسب و کار خود را از پنل روتیک وارد کنید"
                               required=""
                               value="<?php echo empty($token) ? '' : $token ?>"/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100%;text-align: center;margin: 10%">
                        <label for="webapi">توکن ربات:</label>
                    </td>
                    <td style="width: 100%;text-align: center;margin: 10%">
                        <input type="password" size="100" id="webapi" style="width: 100%;text-align: center"
                               name="api"
                               placeholder="لطفا توکن بات خود را از پنل روتیک وارد کنید"
                               required=""
                               value="<?php echo empty($api) ? '' : $api ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="webdriver">پیام رسان خود را انتخاب کنید:</label>
                    </td>
                    <td style="width: 100%;text-align: center;margin: 10%">
                        <select name="driver" id="webdriver" style="width: 100%;text-align: center" >
                            <option value="rotic">هیچ کدام</option>
                            <option value="imber">ایمبر</option>
                            <option value="raychat">رای چت</option>
                            <option value="retain">ریتین</option>
                            <option value="goftino">گفتینو</option>
                            <option value="crisp">Crisp</option>
                            <option value="smartsupp">SmartSupp</option>
                            <option value="intercom">Intercom</option>
                        </select>
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
    $driver = get_option('token')['driver'];
    echo '<script src="https://rotic.ir/api/v1/enterprise/' . $token . '/widget/'.$api.'"></script>';
    if ($driver!='rotic'){echo '<script>window.addEventListener("rotic-start", function () { Rotic.setScroll(1000); Rotic.setDriver("'.$driver.'");})</script>';}
}

add_action('wp_footer', 'add_script');
