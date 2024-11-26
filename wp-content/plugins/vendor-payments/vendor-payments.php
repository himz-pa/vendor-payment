<?php
/**
 * Plugin Name: Vendor Payments
 * Description: Automates vendor payment management for WooCommerce stores.
 * Version: 1.0
 * Author: Himanshu Panchal
 * Text Domain: vendor-payments
 */

/*
 * This plugin is designed to integrate with WooCommerce and manage payments to vendors.
 * Features:
 * - Automates the creation of a database table to store vendor payment information.
 * - Displays an admin notice if WooCommerce is not active.
 * - Defines constants for plugin file paths and includes necessary files.
 * - Implements plugin activation logic to create required database structures.
 * Note: Ensure WooCommerce is installed and active before using this plugin.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if WooCommerce is active
function vp_check_woocommerce_active() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'vp_woocommerce_inactive_notice' );
        return false;
    }
    return true;
}

function vp_woocommerce_inactive_notice() {
    echo '<div class="notice notice-error"><p>';
    esc_html_e( 'Vendor Payments requires WooCommerce to be installed and active.', 'vendor-payments' );
    echo '</p></div>';
}

// Defined Directory
if(!defined('VP_PLUGIN_DIR_URI')){
    define( 'VP_PLUGIN_DIR_URI', plugin_dir_path( __FILE__ ) . 'admin/' );
}

require_once VP_PLUGIN_DIR_URI . 'class-vp-admin.php';
$VP_Admin = new VP_Admin;

register_activation_hook(__FILE__, 'vp_activate_plugin');

function vp_activate_plugin(){

    global $wpdb;
    $table_name = $wpdb->prefix . 'vendor_payments';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        vendor_name varchar(255) NOT NULL,
        product_id bigint(20) NOT NULL,
        order_id bigint(20) NOT NULL,
        order_status varchar(50) NOT NULL,
        payment_term varchar(50) NOT NULL,
        transaction_detail varchar(255) DEFAULT '',
        payment_status varchar(50) DEFAULT 'Pending',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql); 
}
