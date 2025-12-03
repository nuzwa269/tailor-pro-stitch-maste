<?php
/**
 * Tailor Pro Admin Class
 * 
 * Manages all admin pages and their functionality.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class TailorPro_Admin {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Tailor Pro', 'tailorpro'),
            __('Tailor Pro', 'tailorpro'),
            'tailorpro_view_dashboard',
            'tailorpro-dashboard',
            array($this, 'dashboard_page'),
            'dashicons-admin-generic',
            30
        );
        
        // Submenu pages
        add_submenu_page(
            'tailorpro-dashboard',
            __('Dashboard', 'tailorpro'),
            __('Dashboard', 'tailorpro'),
            'tailorpro_view_dashboard',
            'tailorpro-dashboard',
            array($this, 'dashboard_page')
        );
        
        add_submenu_page(
            'tailorpro-dashboard',
            __('Customers', 'tailorpro'),
            __('Customers', 'tailorpro'),
            'tailorpro_manage_customers',
            'tailorpro-customers',
            array($this, 'customers_page')
        );
        
        add_submenu_page(
            'tailorpro-dashboard',
            __('New Order', 'tailorpro'),
            __('New Order', 'tailorpro'),
            'tailorpro_manage_orders',
            'tailorpro-new-order',
            array($this, 'new_order_page')
        );
        
        add_submenu_page(
            'tailorpro-dashboard',
            __('Orders', 'tailorpro'),
            __('Orders', 'tailorpro'),
            'tailorpro_manage_orders',
            'tailorpro-orders',
            array($this, 'orders_page')
        );
        
        add_submenu_page(
            'tailorpro-dashboard',
            __('Settings', 'tailorpro'),
            __('Settings', 'tailorpro'),
            'tailorpro_manage_settings',
            'tailorpro-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'tailorpro') === false) {
            return;
        }
        
        // Enqueue CSS
        wp_enqueue_style('tailorpro-admin', TAILORPRO_PLUGIN_URL . 'assets/css/admin.css', array('dashicons'), TAILORPRO_VERSION);
        wp_enqueue_style('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19');
        
        // Enqueue scripts
        wp_enqueue_script('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.js', array(), '2.2.19', true);
        wp_enqueue_script('tailorpro-admin', TAILORPRO_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), TAILORPRO_VERSION, true);
        
        // Localize script
        wp_localize_script('tailorpro-admin', 'tailorpro_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tailorpro_ajax_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this item?', 'tailorpro'),
                'error' => __('An error occurred. Please try again.', 'tailorpro'),
                'success' => __('Operation completed successfully.', 'tailorpro'),
                'loading' => __('Loading...', 'tailorpro'),
                'no_data' => __('No data available.', 'tailorpro'),
                'required_field' => __('This field is required.', 'tailorpro'),
                'invalid_email' => __('Please enter a valid email address.', 'tailorpro'),
                'invalid_phone' => __('Please enter a valid phone number.', 'tailorpro'),
                'save' => __('Save', 'tailorpro'),
                'cancel' => __('Cancel', 'tailorpro'),
                'delete' => __('Delete', 'tailorpro'),
                'edit' => __('Edit', 'tailorpro'),
                'add' => __('Add', 'tailorpro'),
                'search' => __('Search...', 'tailorpro'),
                'export' => __('Export', 'tailorpro'),
                'import' => __('Import', 'tailorpro'),
                'print' => __('Print', 'tailorpro'),
                'demo_data' => __('Demo Data', 'tailorpro'),
                'settings' => __('Settings', 'tailorpro'),
                'customer' => __('Customer', 'tailorpro'),
                'order' => __('Order', 'tailorpro'),
                'measurement' => __('Measurement', 'tailorpro'),
                'dashboard' => __('Dashboard', 'tailorpro'),
                'customers' => __('Customers', 'tailorpro'),
                'orders' => __('Orders', 'tailorpro'),
                'new_order' => __('New Order', 'tailorpro')
            )
        ));
        
        // Add theme mode support
        $settings = get_option('tailorpro_settings', array());
        $theme_mode = $settings['theme_mode'] ?? 'light';
        wp_add_inline_script('tailorpro-admin', '
            window.tailorproThemeMode = "' . esc_js($theme_mode) . '";
        ');
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
        include TAILORPRO_PLUGIN_DIR . 'pages/dashboard.php';
    }
    
    /**
     * Customers page
     */
    public function customers_page() {
        include TAILORPRO_PLUGIN_DIR . 'pages/customers.php';
    }
    
    /**
     * New order page
     */
    public function new_order_page() {
        include TAILORPRO_PLUGIN_DIR . 'pages/new-order.php';
    }
    
    /**
     * Orders page
     */
    public function orders_page() {
        include TAILORPRO_PLUGIN_DIR . 'pages/orders.php';
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        include TAILORPRO_PLUGIN_DIR . 'pages/settings.php';
    }
}

// Initialize admin class
new TailorPro_Admin();