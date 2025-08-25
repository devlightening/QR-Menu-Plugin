<?php
namespace QRG;
use WP_REST_Request; use WP_REST_Response;

class RestController {
    private $qr;
    public function __construct( $qr_service ){ $this->qr = $qr_service; }
    public function init(){
        add_action( 'rest_api_init', function(){
            register_rest_route( 'qrg/v1', '/qrcode', [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_qr' ],
                'args'     => [
                    'text' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
                    'size' => [ 'required' => false, 'validate_callback' => 'is_numeric' ],
                ],
                'permission_callback' => '__return_true',
            ] );
        } );
    }
    public function get_qr( WP_REST_Request $req ){
        $text = $req->get_param('text');
        $size = intval( $req->get_param('size') ?? 256 );
        $size = max(64, min(2048, $size));
        $url  = $this->qr->generate_url( $text, $size );
        return new WP_REST_Response( [ 'url' => $url ], 200 );
    }
}
