<?php
namespace QRG;

interface QrServiceInterface {
    public function generate_url( string $text, int $size = 256 ): string;
}
