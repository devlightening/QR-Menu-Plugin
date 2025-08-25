<?php
namespace QRG;

class AdminPage {
    private $qr;
    public function __construct( $qr_service ){ $this->qr = $qr_service; }
    public function init(){
        add_action( 'admin_menu', [ $this, 'menu' ] );
        add_action( 'admin_post_qrg_generate', [ $this, 'handle_form' ] );
    }
    public function menu(){
        add_menu_page( __( 'QR Üret', 'qr-code-generator' ), 'QR Generator', 'manage_options', 'qrg', [ $this, 'render' ], 'dashicons-image-filter', 65 );
    }
    public function render(){
        if ( ! current_user_can( 'manage_options' ) ) return;
        $o = get_option( Settings::OPTION, [ 'size' => 256 ] );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'QR Kod Üret', 'qr-code-generator' ); ?></h1>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'qrg_generate' ); ?>
                <input type="hidden" name="action" value="qrg_generate" />
                <table class="form-table" role="presentation">
                    <tr>
                        <th><label for="qrg_text"><?php esc_html_e( 'Metin / URL', 'qr-code-generator' ); ?></label></th>
                        <td><input type="text" id="qrg_text" name="text" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="qrg_size"><?php esc_html_e( 'Boyut (px)', 'qr-code-generator' ); ?></label></th>
                        <td><input type="number" id="qrg_size" name="size" min="64" max="2048" value="<?php echo intval( $o['size'] ?? 256 ); ?>"></td>
                    </tr>
                </table>
                <?php submit_button( __( 'Üret', 'qr-code-generator' ) ); ?>
            </form>
            <?php if ( isset( $_GET['qrg'] ) ) : ?>
                <h2><?php esc_html_e( 'Önizleme', 'qr-code-generator' ); ?></h2>
                <img src="<?php echo esc_url( $_GET['qrg'] ); ?>" alt="QR" style="max-width:256px;height:auto;" />
            <?php endif; ?>
        </div>
        <?php
    }
    public function handle_form(){
        if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'qrg_generate' )){ wp_die( __( 'İzinsiz işlem.', 'qr-code-generator' ) ); }
        $text = isset($_POST['text']) ? sanitize_text_field( wp_unslash( $_POST['text'] ) ) : '';
        $size = isset($_POST['size']) ? intval($_POST['size']) : 256;
        $size = max(64, min(2048, $size));
        $url = $this->qr->generate_url( $text, $size );
        wp_safe_redirect( add_query_arg( [ 'page' => 'qrg', 'qrg' => rawurlencode( $url ) ], admin_url( 'admin.php' ) ) );
        exit;
    }
}
