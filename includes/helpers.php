<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
function qrg_is_url( $s ){ return (bool) filter_var( $s, FILTER_VALIDATE_URL ); }
