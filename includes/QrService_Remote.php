<?php
namespace QRG;

class QrService_Remote implements QrServiceInterface {
    public function generate_url( string $text, int $size = 256 ): string {
        $text = rawurlencode( $text );
        $size = max(64, min(2048, $size));
        return sprintf( 'https://api.qrserver.com/v1/create-qr-code/?size=%dx%d&data=%s', $size, $size, $text );
    }
}
