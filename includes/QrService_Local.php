<?php
namespace QRG;

class QrService_Local implements QrServiceInterface {
    public function generate_url( string $text, int $size = 256 ): string {
        // Burada yerel kütüphane ile üretim yapıp yüklemeye kaydedin.
        return home_url( '/wp-content/uploads/qrg/dummy.png' );
    }
}
