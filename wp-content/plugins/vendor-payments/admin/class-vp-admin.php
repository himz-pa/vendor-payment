<?php 
/**
 * Admin Functionality
 *
 * This file contains the `VP_Admin` class, which handles administrative functionality
 * for the Vendor Payments plugin. The class integrates with WooCommerce to:
 * 
 * - Add custom fields to the WooCommerce product edit page for vendor information, purchase cost, 
 *   and payment terms.
 * - Save the custom field values when a product is updated.
 * - Automatically insert vendor payment details into the custom database table 
 *   when an order is placed.
 * 
 * Note: Ensure the main Vendor Payments plugin is active and WooCommerce is installed 
 * and active to utilize these features.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class VP_Admin {

    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', [$this, 'vp_add_custom_product_fields']);
        add_action('woocommerce_process_product_meta', [$this, 'vp_save_custom_product_fields']);
        add_action('woocommerce_thankyou', [$this, 'vp_handle_order']);

        add_action( 'woocommerce_admin_order_data_after_order_details', [$this, 'vp_add_payment_status_metabox'] );
        add_action( 'woocommerce_process_shop_order_meta', [$this, 'vp_save_payment_status'], 10, 2 );


    }

    /**
     * Add custom fields to the WooCommerce product edit page.
     */
    public function vp_add_custom_product_fields() {
        global $post;

        // Vendor Name (Dropdown)
        echo '<div class="options_group">';
        woocommerce_wp_select([
            'id' => '_vendor_name',
            'label' => __('Vendor Name', 'vendor-payments'),
            'options' => [
                'vendor1' => __('Vendor 1', 'vendor-payments'),
                'vendor2' => __('Vendor 2', 'vendor-payments'),
                'vendor3' => __('Vendor 3', 'vendor-payments'),
            ],
        ]);

        // Purchase Cost
        woocommerce_wp_text_input([
            'id' => '_purchase_cost',
            'label' => __('Purchase Cost', 'vendor-payments'),
            'type' => 'number',
            'custom_attributes' => [
                'step' => '0.01',
                'min' => '0',
            ],
        ]);

        // Payment Term (Dropdown)
        woocommerce_wp_select([
            'id' => '_payment_term',
            'label' => __('Payment Term', 'vendor-payments'),
            'options' => [
                'post_payment' => __('Post Payment', 'vendor-payments'),
                'pre_payment' => __('Pre Payment', 'vendor-payments'),
                'weekly' => __('Weekly', 'vendor-payments'),
                'monthly' => __('Monthly', 'vendor-payments'),
            ],
        ]);
        echo '</div>';
    }

    /**
     * Save the custom fields when the product is saved.
     *
     * @param int $post_id Product ID.
     */
    public function vp_save_custom_product_fields($post_id) {
        $vendor_name = $_POST['_vendor_name'];
        $purchase_cost = $_POST['_purchase_cost'];
        $payment_term = $_POST['_payment_term'];

        if (!empty($vendor_name)) {
            update_post_meta($post_id, '_vendor_name', sanitize_text_field($vendor_name));
        }
        if (!empty($purchase_cost)) {
            update_post_meta($post_id, '_purchase_cost', sanitize_text_field($purchase_cost));
        }
        if (!empty($payment_term)) {
            update_post_meta($post_id, '_payment_term', sanitize_text_field($payment_term));
        }
    }

    /**
     * Handle WooCommerce order completion.
     * Insert vendor payment details into the custom database table.
     *
     * @param int $order_id Order ID.
     */
    public function vp_handle_order($order_id) {
        $order = wc_get_order($order_id);

        if ($order) {
            foreach ($order->get_items() as $item_id => $item) {
                $product_id = $item->get_product_id();
                $vendor_name = get_post_meta($product_id, '_vendor_name', true);
                $payment_term = get_post_meta($product_id, '_payment_term', true);

                global $wpdb;
                $wpdb->insert("{$wpdb->prefix}vendor_payments", [
                    'vendor_name' => $vendor_name,
                    'product_id' => $product_id,
                    'order_id' => $order_id,
                    'order_status' => $order->get_status(),
                    'payment_term' => $payment_term,
                    'payment_status' => 'Pending',
                ]);
            }
        }
    }

    public function vp_add_payment_status_metabox($order){
        // Get the current payment status
        $payment_status = get_post_meta( $order->get_id(), 'payment_status', true );

        // Allowed order statuses to enable editing payment status
        $allowed_statuses = array( 'processing', 'completed', 'refunded' );

        // Check if order status is eligible
        if ( in_array( $order->get_status(), $allowed_statuses ) ) {
            ?>
            <div class="form-field form-field-wide">
                <label for="payment_status"><?php esc_html_e( 'Payment Status', 'vendor-payments' ); ?></label>
                <select name="payment_status" id="payment_status" class="wc-enhanced-select">
                    <option value=""><?php esc_html_e( 'Select a status', 'vendor-payments' ); ?></option>
                    <option value="pending" <?php selected( $payment_status, 'pending' ); ?>><?php esc_html_e( 'Pending', 'vendor-payments' ); ?></option>
                    <option value="paid" <?php selected( $payment_status, 'paid' ); ?>><?php esc_html_e( 'Paid', 'vendor-payments' ); ?></option>
                    <option value="refunded" <?php selected( $payment_status, 'refunded' ); ?>><?php esc_html_e( 'Refunded', 'vendor-payments' ); ?></option>
                    <option value="credit_note" <?php selected( $payment_status, 'credit_note' ); ?>><?php esc_html_e( 'Credit Note', 'vendor-payments' ); ?></option>
                </select>
            </div>
            <?php
        } else {
            // Display the payment status as read-only if editing is not allowed
            ?>
            <div class="form-field form-field-wide">
                <label for="payment_status"><?php esc_html_e( 'Payment Status', 'vendor-payments' ); ?></label>
                <input type="text" readonly value="<?php echo esc_attr( $payment_status ); ?>" />
                <p><?php esc_html_e( 'Payment status can only be updated for orders with Processing, Completed, or Refunded status.', 'vendor-payments' ); ?></p>
            </div>
            <?php
        }

    }
    /**
     * Save Payment Status Meta Field
     */

     public function vp_save_payment_status($post_id, $post ){

        // Get the current order
        $order = wc_get_order( $post_id );

        // Allowed order statuses to enable editing payment status
        $allowed_statuses = array( 'processing', 'completed', 'refunded' );

        // Check if order status is eligible
        if ( in_array( $order->get_status(), $allowed_statuses ) ) {
            if ( isset( $_POST['payment_status'] ) && ! empty( $_POST['payment_status'] ) ) {
                // Sanitize the input value
                $payment_status = sanitize_text_field( $_POST['payment_status'] );

                // Allowed payment statuses
                $allowed_payment_statuses = array( 'pending', 'paid', 'refunded', 'credit_note' );

                if ( in_array( $payment_status, $allowed_payment_statuses ) ) {
                    // Update the payment status
                    update_post_meta( $post_id, 'payment_status', $payment_status );
                }
            }
        }
     }

}
