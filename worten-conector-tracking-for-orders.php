<?php
/**
 * Worten Conector - Tracking for orders
 *
 * @package       WORTENCONESHIP
 * @author        TERACONDITION
 * @license       gplv2
 * @version       2.7
 *
 * @wordpress-plugin
 * Plugin Name:   Worten Conector - Tracking for orders
 * Plugin URI:    https://wordpress.teracondition.pt
 * Description:   Add shipping information to orders in WooCommerce, change the order status to "Completed" to send the tracking details to the Worten Marketplace (Mirakl), and mark the order as shipped.
 * Version:       2.7
 * Author:        TERACONDITION
 * Author URI:    https://wordpress.teracondition.pt
 * Text Domain:   worten-conector-tracking-for-orders
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Worten Conector - Tracking for orders. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Adiciona a informação "Encomenda Worten" na lista de encomendas no painel de administração
add_filter('manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
function custom_shop_order_column($columns)
{
    $new_columns = array();
    foreach ($columns as $key => $column) {
        $new_columns[$key] = $column;
        if ($key === 'order_status') {
            $new_columns['worten_info'] = __('Marketplace', 'woocommerce');
        }
    }
    return $new_columns;
}

// Mostra a informação "Encomenda Worten" na lista de encomendas no painel de administração
add_action('manage_shop_order_posts_custom_column', 'custom_shop_order_column_content', 20, 2 );
function custom_shop_order_column_content( $column, $post_id )
{
    if ( 'worten_info' === $column ) {
        $order = wc_get_order( $post_id );
        $customer_note = $order->get_customer_note();
        
        // Verifica se a encomenda é da Worten
        $is_worten_order = strpos( $customer_note, 'Worten Conector' ) !== false;
        
        // Salva os metadados do marketplace (Worten ou outro)
        $marketplace = $is_worten_order ? 'worten' : 'outro';
        update_post_meta( $post_id, 'worten_marketplace', $marketplace );

        // Exibe o valor do marketplace na coluna
        echo '<span class="worten-conector-info">' . ( $is_worten_order ? __('Worten', 'woocommerce') : '' ) . '</span>';
    }
}

// Adiciona campos personalizados aos pedidos do WooCommerce
function wortenconeship_trackingwoofields( $order ) {
    echo '<div class="tracking-fields">';
    ?>
    <strong><h3>Detalhes de Envio para Worten</h3></strong>
    <?php
    woocommerce_form_field( 'carrier_code', array(
        'type'          => 'select',
        'class'         => array( 'form-row-first' ),
        'label'         => __( 'Transportadora' ),
        'options'       => array(
            '' => 'SELECIONE',
            '17track' => '17TRACK',
            'ABC' => 'AB Custom',
            'asendia' => 'Asendia',
            'austrianpost' => 'Austrian Post',
            'Autoradio' => 'Autoradio',
            'bpost' => 'BPOST',
            'BRT' => 'BRT',
            'camposcadilhe' => 'CC Campos e Cadilhe',
            'cbllogistica' => 'CBL Logistica',
            'CEHD' => 'CTT Fulfillment Espanha',
            'CELERITAS' => 'CELERITAS',
            'chronopostfrance' => 'Chronopost France',
            'CHSD' => 'DPD Fulfillment',
            'cne' => 'CNE',
            'correosespana' => 'Correos ES',
            'correosexpress' => 'Correos Express',
            'CTHD' => 'CTT Fulfillment C&C',
            'ctt' => 'CTT',
            'dbschenker' => 'DB SCHENKER',
            'delnext' => 'Delnext',
            'dhl' => 'DHL',
            'dhlespana' => 'DHL España',
            'dhlexpress' => 'DHL Express',
            'dpd' => 'DPD',
            'DPD_DE' => 'DPD.de',
            'envialia' => 'ENVIALIA',
            'fedexespana' => 'FedEx España',
            'fedexportugal' => 'FedEx Portugal',
            'gls' => 'GLS',
            'glsespana' => 'GLS ES',
            'kerrylogistics' => 'Kerry Logistics',
            'landmarkglobal' => 'Landmark Global',
            'laposte' => 'La Poste',
            'LSSD' => 'Luis Simões Fulfillment',
            'marcapt' => 'MarCa',
            'mrwespana' => 'MRW España',
            'mrwportugal' => 'MRW',
            'nacexespana' => 'Nacex España',
            'p2p' => 'P2P',
            'Paack' => 'Paack',
            'postluxembourg' => 'Post Luxembourg',
            'postnl' => 'PostNL',
            'sending' => 'sending',
            'seurespana' => 'SEUR',
            'szendex' => 'Szendex',
            'tamdis' => 'TAMDIS',
            'tfs' => 'TFS',
            'tipsa' => 'TIPSA',
            'tnt' => 'TNT',
            'trackcubyn' => 'Track Cubyn',
            'trackyourparcel' => 'Track Your parcel',
            'TTLF' => 'TTM Fulfillment',
            'ups' => 'UPS',
            'upsespana' => 'UPS España',
            'vaspexpresso' => 'Vasp Expresso',
            'Venta-Unica' => 'Venta-Unica',
            'wanbexpress' => 'WANBEXPRESS',
            'WEEXP' => 'Worten Em 2H',
            'wndirect' => 'WN Direct',
            'wortenentrega' => 'Worten Entrega',
            'xpologistics' => 'XPO Logistics',
            'yunexpress' => 'Yun Express'
            
        )
    ), $order->get_meta( 'carrier_code' ) );

    woocommerce_form_field( 'tracking_number', array(
        'type'          => 'text',
        'class'         => array( 'form-row-last' ),
        'label'         => __( 'Tracking' ),
    ), $order->get_meta( 'tracking_number' ) );
    echo '</div>';
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'wortenconeship_trackingwoofields' );

// Salva os campos personalizados quando o pedido é salvo
function save_wortenconeship_trackingwoofields( $order_id ) {
    if ( ! empty( $_POST['carrier_code'] ) ) {
        update_post_meta( $order_id, 'carrier_code', sanitize_text_field( $_POST['carrier_code'] ) );
        update_post_meta( $order_id, 'carrier_name', sanitize_text_field( $_POST['carrier_name'] ) );
    }
    if ( ! empty( $_POST['tracking_number'] ) ) {
        update_post_meta( $order_id, 'tracking_number', sanitize_text_field( $_POST['tracking_number'] ) );
    }
}
add_action( 'woocommerce_process_shop_order_meta', 'save_wortenconeship_trackingwoofields' );

// Adiciona os campos personalizados à resposta da API do WooCommerce
function wortenconeship_trackingwoofieldsapi( $response, $order ) {
    $response->data['carrier_name'] = $order->get_meta( 'carrier_name' );
    $response->data['carrier_code'] = $order->get_meta( 'carrier_code' );
    $response->data['tracking_number'] = $order->get_meta( 'tracking_number' );
    return $response;
}
add_filter( 'woocommerce_rest_prepare_shop_order_object', 'wortenconeship_trackingwoofieldsapi', 10, 2 );

// Estilos CSS para a formatação da informação "Encomenda Worten" na lista de encomendas
add_action('admin_head', 'worten_conector_admin_styles');
function worten_conector_admin_styles() {
    ?>
    <style>
        .column-worten_info {
            color: #FF0000;
            font-weight: bold;
        }
    </style>
    <?php
}

// Adiciona filtro na lista de encomendas para filtrar por Marketplace (Worten)
add_action( 'restrict_manage_posts', 'wortenconeship_orders_filter' );
function wortenconeship_orders_filter() {
    global $typenow;
    
    // Apenas exibe o filtro para o tipo de post 'shop_order' (encomendas)
    if ( 'shop_order' === $typenow ) {
        $selected = isset( $_GET['worten_marketplace'] ) ? $_GET['worten_marketplace'] : '';
        $options  = array(
            ''       => __( 'By Marketplace', 'woocommerce' ),
            'worten' => __( 'Worten', 'woocommerce' ),
        );

        echo '<select name="worten_marketplace">';
        
        foreach ( $options as $value => $label ) {
            echo '<option value="' . esc_attr( $value ) . '" ' . selected( $selected, $value, false ) . '>' . esc_html( $label ) . '</option>';
        }
        
        echo '</select>';
    }
}

// Aplica o filtro quando o formulário é submetido
add_filter( 'parse_query', 'wortenconeship_orders_filter_query' );
function wortenconeship_orders_filter_query( $query ) {
    global $pagenow, $typenow;
    if ( 'edit.php' === $pagenow && 'shop_order' === $typenow && isset( $_GET['worten_marketplace'] ) && $_GET['worten_marketplace'] != '' ) {
        $query->query_vars['meta_key']   = 'worten_marketplace';
        $query->query_vars['meta_value'] = sanitize_text_field( $_GET['worten_marketplace'] );
    }
}
?>
