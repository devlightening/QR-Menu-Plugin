<?php
namespace QRG;

class Shortcodes {
    private $qr;
    public function __construct( $qr_service ){ $this->qr = $qr_service; }
    public function init(){ add_shortcode( 'qr_code', [ $this, 'render' ] ); }
    public function render( $atts = [] ){
        $atts = shortcode_atts( [ 'text' => '', 'size' => 256, 'alt' => 'QR' ], $atts, 'qr_code' );
        $text = sanitize_text_field( $atts['text'] );
        $size = max(64, min(2048, intval( $atts['size'] ) ));
        if ( empty( $text ) ) return '';
        $src = esc_url( $this->qr->generate_url( $text, $size ) );
        return sprintf( '<img src="%s" width="%d" height="%d" alt="%s" />', $src, $size, $size, esc_attr( $atts['alt'] ) );
    }
}
