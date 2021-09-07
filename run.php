<?php

/*
Plugin Name: Rotic Plugin
Plugin URI: https://github.com/roticmedia/RoticWP/releases
Description: Connect your website to the Rotic, because you contacted to the future..
Version: 2.0.6
Author: Milad Xandi
Author URI: http://rotic.ir
License: GPLv2
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
    $drivers = ['rotic'=>'هیچ کدام','imber'=>'ایمبر','raychat'=>'رای چت','retain'=>'ریتین','goftino'=>'گفتینو','crisp'=>'Crisp','smartsupp'=>'SmartSupp','intercom'=>'Intercom'];
    ?>
    <div class="wrap">
        <h2 class="persian" >تنظیمات وب سرویس روتیک</h2>
        <form method="post"  class="persian"  enctype="multipart/form-data">
            <div style="text-align: center" >
                <img src="https://rotic.ir/images/rotic-full-cyan.png" width="10%" alt="Rotic">
                <h4>
                    تنظیم توکن روتیک (
                    <a href="https://rotic.ir/panel" target="_blank">دریافت توکن کسب و کار</a>
                    و
                    <a href="https://rotic.ir/panel/bots" target="_blank">دریافت توکن بات</a>
                    )
                </h4>
            </div>
            <table class="widefat">
                <tr>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <label for="webtoken">توکن کسب و کار:</label>
                    </td>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <input type="password" size="100" id="webtoken" style="width: 100%;text-align: center"
                               name="token"
                               placeholder="لطفا توکن کسب و کار خود را از پنل روتیک وارد کنید"
                               required=""
                               value="<?php echo empty($token) ? '' : $token ?>"/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <label for="webapi">توکن ربات:</label>
                    </td>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <input type="password" size="100" id="webapi" style="width: 100%;text-align: center"
                               name="api"
                               placeholder="لطفا توکن بات خود را از پنل روتیک وارد کنید"
                               required=""
                               value="<?php echo empty($api) ? '' : $api ?>"/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <label for="webdriver">پیام رسان خود را انتخاب کنید:</label>
                    </td>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <select name="driver" id="webdriver" style="width: 100%;text-align: center" >
                            <?php foreach ($drivers as $key => $item): ?>
                                <?php if ($driver == $key): ?>
                                    <option selected value="<?php echo $key ?>"><?php echo $item ?></option>
                                <?php elseif($driver!=$key): ?>
                                    <option value="<?php echo $key ?>"><?php echo $item ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
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
    echo '<script src="https://api.rotic.ir/v1/enterprise/' . $token . '/widget/'.$api.'"></script>';
    if ($driver!='rotic'){echo '<script>window.addEventListener("rotic-start", function () { Rotic.setScroll(1000); Rotic.setDriver("'.$driver.'");})</script>';}
}

add_action('wp_footer', 'add_script');
