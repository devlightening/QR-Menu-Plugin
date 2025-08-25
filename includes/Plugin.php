<?php
namespace QRG;

class Plugin {
    private $qr_service;
    public function __construct( array $args = [] ){
        $class = $args['qr_service'] ?? '\\QRG\\QrService_Remote';
        $this->qr_service = new $class();
    }
    public function init(){
        require_once QRG_DIR . 'includes/helpers.php';
        ( new Settings() )->init();
        ( new AdminPage( $this->qr_service ) )->init();
        ( new Shortcodes( $this->qr_service ) )->init();
        ( new RestController( $this->qr_service ) )->init();
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_public' ] );
    }
    public function enqueue_admin(){
        wp_enqueue_style( 'qrg-admin', QRG_URL . 'assets/admin.css', [], '1.0.0' );
        wp_enqueue_script( 'qrg-admin', QRG_URL . 'assets/admin.js', [ 'jquery' ], '1.0.0', true );
    }
    public function enqueue_public(){
        wp_enqueue_style( 'qrg-public', QRG_URL . 'assets/public.css', [], '1.0.0' );
        wp_enqueue_script( 'qrg-public', QRG_URL . 'assets/public.js', [ 'jquery' ], '1.0.0', true );
    }
}
