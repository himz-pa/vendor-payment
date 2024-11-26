=== Vendor Payments ===
Contributors: [Himanshu Panchal]
Tags: woocommerce, vendor payments, payment management, order processing
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WooCommerce addon plugin for managing vendor payments with custom product fields, payment terms, and a vendor payments database.

== Description ==
Vendor Payments is a WooCommerce addon plugin that helps store owners manage vendor payments seamlessly. With this plugin, you can:
- Add vendor-specific details to products.
- Define payment terms such as post-payment, pre-payment, weekly, or monthly.
- Automatically log vendor payment records when an order is placed.
- Manage and update payment statuses for vendor-related transactions.

The plugin supports both High-Performance Order Storage (HPOS) and the Old Order Storage format in WooCommerce, ensuring compatibility with different setups.

== Features ==
1. **Custom Product Fields**:
    - **Vendor Name**: Dropdown field to select the vendor.
    - **Purchase Cost**: Field to input the purchase cost of the product.
    - **Payment Term**: Dropdown to specify payment terms (Post Payment, Pre Payment, Weekly, Monthly).

2. **Vendor Payments Table**:
    - Stores vendor payment records with details like vendor name, product, order ID, payment term, and transaction details.

3. **Order Handling**:
    - Automatically creates a vendor payment record for each item in an order upon successful checkout.

4. **Payment Status Management**:
    - Edit payment statuses based on order status, with options like Pending, Paid, Refunded, and Credit Note.

5. **Security & Extensibility**:
    - Built with WordPress coding standards and security best practices in mind.
    - Extensible to adapt to additional business requirements.

== Installation ==
1. **Prerequisites**:
    - WooCommerce plugin must be installed and activated before using Vendor Payments.

2. **Install via WordPress Admin**:
    - Upload the `vendor-payments.zip` file through the Plugins menu in WordPress.
    - Activate the plugin through the 'Plugins' screen.

3. **Manual Installation**:
    - Upload the plugin folder to the `/wp-content/plugins/` directory.
    - Activate the plugin through the 'Plugins' screen in WordPress.

4. **Configuration**:
    - Go to **WooCommerce > Settings** and configure the vendor payment options.

== Changelog ==
= 1.0.0 =
* Initial release.
* Added support for HPOS and Old Order Storage.
* Introduced custom product fields: Vendor Name, Purchase Cost, and Payment Term.
* Created database table `wp_vendor_payments` for storing vendor payment records.
* Implemented automatic order handling and payment status management.

== Frequently Asked Questions ==
= What is required for this plugin to work? =
WooCommerce must be installed and active on your WordPress site.

= Is the plugin compatible with HPOS? =
Yes, the plugin supports both HPOS and Old Order Storage in WooCommerce.

= Can I add custom payment statuses? =
Currently, the plugin supports predefined statuses (Pending, Paid, Refunded, Credit Note), but it can be extended to add custom statuses.

== Upgrade Notice ==
= 1.0.0 =
Initial release. No upgrade required.
