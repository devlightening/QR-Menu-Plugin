<?php
namespace QRG;

class Settings {
    const OPTION = 'qrg_settings';
    public function init(){ add_action( 'admin_init', [ $this, 'register' ] ); }
    public function register(){
        register_setting( 'qrg_group', self::OPTION, [ $this, 'sanitize' ] );
        add_settings_section( 'qrg_main', __( 'QR Ayarları', 'qr-code-generator' ), function(){
            echo '<p>' . esc_html__( 'Varsayılan boyut vb.', 'qr-code-generator' ) . '</p>';
        }, 'qrg' );
        add_settings_field( 'size', __( 'Varsayılan Boyut (px)', 'qr-code-generator' ), function(){
            $o = get_option( self::OPTION, [ 'size' => 256 ] );
            printf( '<input type="number" name="%s[size]" value="%d" min="64" max="2048" />', esc_attr( self::OPTION ), intval( $o['size'] ?? 256 ) );
        }, 'qrg', 'qrg_main' );
    }
    public function sanitize( $input ){
        $out = [];
        $out['size'] = max(64, min(2048, intval($input['size'] ?? 256)));
        return $out;
    }
}
