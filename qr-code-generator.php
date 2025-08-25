<?php
/**
 * Plugin Name: QR Code Generator
 * Description: Metinden QR kod üretimi (Admin, Shortcode, REST).
 * Version: 1.0.0
 * Author: Siz
 * Text Domain: qr-code-generator
 * Domain Path: /languages
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'QRG_FILE', __FILE__ );
define( 'QRG_DIR', plugin_dir_path( __FILE__ ) );
define( 'QRG_URL', plugin_dir_url( __FILE__ ) );

require_once QRG_DIR . 'includes/autoloader.php';

register_activation_hook( __FILE__, function(){} );
register_deactivation_hook( __FILE__, function(){} );

add_action( 'plugins_loaded', function(){
    load_plugin_textdomain( 'qr-code-generator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    $plugin = new QRG\Plugin([ 'qr_service' => '\\QRG\\QrService_Remote' ]);
    $plugin->init();
});
