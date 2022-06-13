<?php


/*
Plugin Name: افزونه روتیک
Plugin URI: https://github.com/roticmedia/RoticWP/releases
Description: Connect your website to the Rotic, because you contacted to the future...
Version: 4.0.0.0
Author: شرکت دانش بنیان روتیک
Author URI: http://rotic.ir
License: GPLv2
*/

$GLOBALS['rotic_api_url'] = 'https://rotic.ir/api/v1/';

add_action('admin_menu', 'rotic_menu_builder');
function rotic_menu_builder()
{
    add_menu_page('افزونه روتیک', 'افزونه روتیک', 'manage_options', 'rotic', '', "/wp-content/plugins/rotic/assets/Rotic-Wp-Logo.png");
}

add_action('admin_menu', 'rotic_option_builder');
function rotic_option_builder()
{
    add_options_page("افزونه روتیک", "افزونه روتیک", 'manage_options', 'rotic', 'rotic_page_builder');
}

add_action('admin_enqueue_scripts', 'rotic_callback_for_setting_up_scripts');

function rotic_callback_for_setting_up_scripts()
{
    wp_register_style('semantic', get_site_url() . '/wp-content/plugins/rotic/css/semantic.min.css');
    wp_register_style('icon', get_site_url() . '/wp-content/plugins/rotic/css/icon.min.css');
    wp_register_style('rotic', get_site_url() . '/wp-content/plugins/rotic/css/rotic.css');
    wp_register_style('toast', get_site_url() . '/wp-content/plugins/rotic/css/jquery.toast.css');
    wp_register_script('jquery', get_site_url() . '/wp-content/plugins/rotic/js/jquery-3.1.1.min.js');
    wp_register_script('autoNumeric', get_site_url() . '/wp-content/plugins/rotic/js/autoNumeric.js');
    wp_register_script('semantic', get_site_url() . '/wp-content/plugins/rotic/js/semantic.min.js');
    wp_register_script('toast', get_site_url() . '/wp-content/plugins/rotic/js/jquery.toast.js');
    wp_register_script('rotic', get_site_url() . '/wp-content/plugins/rotic/js/rotic.js');

    wp_enqueue_style('semantic');
    wp_enqueue_style('icon');
    wp_enqueue_style('toast');
    wp_enqueue_style('rotic');
    wp_enqueue_script('jquery');
    wp_enqueue_script('autoNumeric');
    wp_enqueue_script('semantic');
    wp_enqueue_script('toast');
    wp_enqueue_script('rotic');
}

function rotic_prefix_register() {
    session_start();
    register_rest_route( 'rotic/v1', '/verify', array(
        'methods'  => 'GET',
        'callback' => function($request){
            var_dump($request);
            update_option('rotic_token',$request['token']);
//            wp_redirect( get_site_url()."/wp-admin/admin.php?page=rotic");
            exit;
        },
        'permission_callback' =>'__return_true'
    ) );
}
add_action( 'rest_api_init', 'rotic_prefix_register' );


$token = get_option('rotic_token');
$api = get_option('rotic_api');
$driver = get_option('rotic_driver');
$side = get_option('rotic_side');
$width = get_option('rotic_width');
$bottom = get_option('rotic_bottom');
$distance = get_option('rotic_distance');

function rotic_encrypt_decryt($stringToHandle = "",$encryptDecrypt = 'e'){
    // Set default output value
    $output = null;
    // Set secret keys
    $secret_key = get_site_url(); // Change this!
    $secret_iv = 'd&&9"dh4%:@@@ssdeer##'; // Change this!
    $key = hash('sha256',$secret_key);
    $iv = substr(hash('sha256',$secret_iv),0,16);
    // Check whether encryption or decryption
    if($encryptDecrypt == 'e'){
        // We are encrypting
        $output = base64_encode(openssl_encrypt($stringToHandle,"AES-256-CBC",$key,0,$iv));
    }else if($encryptDecrypt == 'd'){
        // We are decrypting
        $output = openssl_decrypt(base64_decode($stringToHandle),"AES-256-CBC",$key,0,$iv);
    }
    // Return the final value
    return $output;
}

function rotic_page_builder()
{
    if (isset($_POST['save'])) {
        $_POST['token'] = esc_sql($_POST['token']);
        $_POST['api'] = esc_sql($_POST['api']);
        $_POST['driver'] = esc_sql($_POST['driver']);
        $_POST['side'] = esc_sql($_POST['side']);
        $_POST['width'] = esc_sql($_POST['width']);
        $_POST['bottom'] = esc_sql($_POST['bottom']);
        $_POST['distance'] = esc_sql($_POST['distance']);
        update_option('rotic_token', $_POST['token']);
        update_option('rotic_api', $_POST['api']);
        update_option('rotic_driver', $_POST['driver']);
        update_option('rotic_side', $_POST['side']);
        update_option('rotic_width', $_POST['width']);
        update_option('rotic_bottom', $_POST['bottom']);
        update_option('rotic_distance', $_POST['distance']);
        echo '<div id="message" class="updated notice-success is-dismissible persian" ><p>تنظیمات جدید با موفقیت ذخیره گردید!</p></div>';
    }
    elseif(isset($_POST['login'])){
        $headers = array(
        );
        $body = array(
            'email' => $_POST['email'],
            'password' => $_POST['password'],
        );
        $args = array(
            'body' => $body,
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers,
        );

        $response = wp_remote_post($GLOBALS['rotic_api_url'] . 'oauth/login', $args);
        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body);
        if (isset($json->response->token) && $json->response->token!=null){
            update_option('rotic_access_token',rotic_encrypt_decryt($json->response->token));
        }else{
            echo '<div id="message" class=" notice-error error is-dismissible persian" ><p>خطایی در یافتن یا مطابقت اطلاعات حساب کاربری شما به وجود آمده است!</p></div>';
        }
    }
    elseif (isset($_POST['logout'])) {
        update_option('rotic_access_token', null);
        $GLOBALS['rotic_reset_login'] = true;
    }
    if ( get_option('rotic_access_token') != null){
        $token = get_option('rotic_token');
        $api = get_option('rotic_api');
        $driver = get_option('rotic_driver');
        $side = get_option('rotic_side');
        $width = get_option('rotic_width');
        $bottom = get_option('rotic_bottom');
        $distance = get_option('rotic_distance');
        $drivers = ['rotic' => 'هیچ کدام', 'imber' => 'ایمبر', 'raychat' => 'رای چت', 'retain' => 'ریتین', 'goftino' => 'گفتینو', 'crisp' => 'Crisp', 'smartsupp' => 'SmartSupp', 'intercom' => 'Intercom'];
        $headers = array('Authorization'=>'Bearer '.rotic_encrypt_decryt(get_option('rotic_access_token'),'d'));
        $body = array();
        $args = array(
            'body' => $body,
            'timeout' => '5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers,
        );

        $enterprise_response = wp_remote_get($GLOBALS['rotic_api_url'] . 'panel/enterprise', $args);
        $bots_response = wp_remote_get($GLOBALS['rotic_api_url'] . 'panel/bots', $args);
        $enterprise_body = wp_remote_retrieve_body($enterprise_response);
        $bots_body = wp_remote_retrieve_body($bots_response);
        $enterprises = json_decode($enterprise_body)->response;
        $bots = json_decode($bots_body)->response;
    }

    ?>
    <?php if ( get_option('rotic_access_token') != null): ?>
    <div class="wrap">
        <h2 class="persian">تنظیمات افزونه روتیک</h2>
        <form method="post" class="persian" enctype="multipart/form-data">
            <div style="text-align: center">
                <a href="https://rotic.ir/fa-ir?utm_source=<?php echo site_url()?>&utm_medium=wp_plugin" target="_blank">
                    <img src="/wp-content/plugins/rotic/assets/Rotic-Text-Theme-Pure.png" width="20%" alt="Rotic">
                </a>
            </div>
            <table class="widefat">
                <tr>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <label for="webtoken" class="persian" >توکن کسب و کار:</label>
                        <input type="password" class="regular-text persian rotic-input" size="100" id="webtoken" style="width: 100%;text-align: center"
                               name="token"
                               placeholder="لطفا توکن کسب و کار خود را از پنل روتیک وارد کنید"
                               required=""
                               value="<?php echo $enterprises->enterprise->token ?>"/>
                    </td>
                    <td style="width: 50% !important;text-align: center;margin: 10%" colspan="2">
                        <label for="webwidth" class="persian" >عرض صفحه گفتگو ابزارک:</label>
                        <input type="number" class="regular-text persian rotic-input" size="100" id="webwidth" style="width: 100%;text-align: center"
                               name="width"
                               placeholder="عرض صفحه گفتگو ابزارک"
                               required=""
                               value="<?php echo empty($width) ? 350 : $width ?>"/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <label for="webbottom" class="persian" >فاصله از پایین:</label>
                        <input type="number" class="regular-text persian rotic-input" size="100" id="webbottom" style="width: 100%;text-align: center"
                               name="bottom"
                               placeholder="فاصله ابزارک از پایین صفحه"
                               required=""
                               value="<?php echo empty($bottom) ? 30 : $bottom ?>"/>
                    </td>
                    <td style="width: 50% !important;text-align: center;margin: 10%" colspan="2">
                        <label for="webdistance" class="persian" >فاصله از کنار صفحه:</label>
                        <input type="number" class="regular-text persian rotic-input" size="100" id="webdistance" style="width: 100%;text-align: center"
                               name="distance"
                               placeholder="فاصله ابزارک از کنار صفحه"
                               required=""
                               value="<?php echo empty($distance) ? 45 : $distance ?>"/>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;margin: 10%">
                        <label for="api" class="persian" >توکن ربات خودتان را انتخاب کنید:</label>
                        <select name="api" class="regular-text persian rotic-input" id="api" style="width: 100%;text-align: center;min-width: 100%">
                            <?php foreach ($bots->channels as $bot_key => $bot): ?>
                                <?php foreach ($bot as $channel_key => $channel): ?>
                                    <?php if ($channel->channel == 'api'): ?>
                                        <?php if ($channel->token == $api): ?>
                                            <option selected value="<?php echo $channel->token ?>"><?php echo $bot_key ?></option>
                                        <?php else: ?>
                                            <option  value="<?php echo $channel->token ?>"><?php echo $bot_key ?></option>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="text-align: center;margin: 10%">
                        <label for="webdriver" class="persian" >پیام رسان دوم خود را انتخاب کنید:</label>
                        <select name="driver" class="regular-text persian rotic-input" id="webdriver" style="width: 100%;text-align: center">
                            <?php foreach ($drivers as $key => $item): ?>
                                <?php if ($driver == $key): ?>
                                    <option selected value="<?php echo $key ?>"><?php echo $item ?></option>
                                <?php elseif ($driver != $key): ?>
                                    <option value="<?php echo $key ?>"><?php echo $item ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td style="text-align: center;margin: 10%">
                        <label for="side" class="persian">جهت مورد نظر را انتخاب کنید:</label>
                        <select name="side" class="regular-text persian rotic-input" id="side" style="width: 100%;text-align: center">
                            <?php if ($side == 'right'): ?>
                                <option selected value="right">سمت راست</option>
                                <option value="left">سمت چپ</option>

                            <?php else: ?>
                                <option selected value="left">سمت چپ</option>
                                <option value="right">سمت راست</option>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button type="submit" name="save" class="ui button persian rotic-submit" >
                            ذخیره
                        </button>
                    </td>
                    <td>
                        <?php if (get_option('rotic_access_token') != null): ?>
                            <form method="post" class="persian" enctype="multipart/form-data">
                                <button type="submit" name="logout" class="ui button persian rotic-danger" style="float: left" >خروج <i
                                            class="power off icon"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php else: ?>
    <div class="wrap">
        <h2 class="persian">تنظیمات وب سرویس روتیک</h2>
        <form method="post" class="persian" enctype="multipart/form-data">
            <div style="text-align: center">
                <a href="https://rotic.ir/fa-ir?utm_source=<?php echo site_url()?>&utm_medium=wp_plugin" target="_blank">
                    <img src="/wp-content/plugins/rotic/assets/Rotic-Text-Theme-Pure.png" width="20%" alt="Rotic">
                </a>
                <h4 class="persian" style="margin: 10px" >
                    به حساب کاربری خود وارد شوید!
                </h4>
            </div>
            <table class="widefat">
                <tr>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <label for="email">پست الکترونیک:</label>

                        <input type="email" class="regular-text persian rotic-input" size="100" id="email" style="width: 100%;text-align: center"
                               name="email"
                               placeholder="لطفا ایمیل حساب کاربری روتیک خود وارد کنید"
                               required
                        />
                    </td>
                </tr>
                <tr>
                    <td style="width: 50% !important;text-align: center;margin: 10%">
                        <label for="password">گذرواژه روتیک:</label>
                        <input type="password" class="regular-text persian rotic-input" size="100" id="password" style="width: 100%;text-align: center"
                               name="password"
                               placeholder="لطفا گذرواژه حساب کاربری روتیک خود وارد کنید"
                               required
                        />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button type="submit" name="login" class="ui button persian rotic-submit" >
                            ورود
                        </button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php endif; ?>
    <?php
}

function rotic_add_script()
{
    $token = get_option('rotic_token');
    $api = get_option('rotic_api');
    $driver = get_option('rotic_driver');
    $width = get_option('rotic_width');
    $bottom = get_option('rotic_bottom');
    $distance = get_option('rotic_distance');
    $side = get_option('rotic_side') != null ? get_option('rotic_side') : "right";
    echo '<script src="https://api.rotic.ir/v2/enterprise/' . $token . '/widget/' . $api . '"></script>';
    if ($driver != 'rotic') {
        echo '<script>window.addEventListener("rotic-start", function () { Rotic.setScroll(1000); Rotic.setDriver("' . $driver . '"); Rotic.setSide("' . $side . '"); Rotic.setWidth("' . $width . '"); Rotic.setBottomDistance("' . $bottom . '");  Rotic.setDistance("' . $distance . '");})</script>';
    } else {
        echo '<script>window.addEventListener("rotic-start", function () { Rotic.setScroll(1000); Rotic.setSide("' . $side . '"); Rotic.setWidth("' . $width . '"); Rotic.setBottomDistance("' . $bottom . '"); Rotic.setDistance("' . $distance . '");})</script>';
    }
}

add_action('wp_footer', 'rotic_add_script');

?>
