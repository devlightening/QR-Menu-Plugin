<?php
spl_autoload_register( function( $class ){
    if ( strpos( $class, 'QRG\\' ) !== 0 ) return;
    $rel = str_replace( 'QRG\\', '', $class );
    $rel = str_replace( '\\', DIRECTORY_SEPARATOR, $rel );
    $file = QRG_DIR . 'includes/' . $rel . '.php';
    if ( file_exists( $file ) ) require_once $file;
});
