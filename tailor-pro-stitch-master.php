<?php
/**
 * Plugin Name: Tailor Pro Stitch Master
 * Plugin URI: https://tailorpro.com
 * Description: A comprehensive digital ledger system for tailors to manage customers, orders, measurements, payments, and delivery schedules.
 * Version: 1.0.0
 * Author: MiniMax Agent
 * Author URI: https://minimax.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tailorpro
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TAILORPRO_VERSION', '1.0.0');
define('TAILORPRO_PLUGIN_FILE', __FILE__);
define('TAILORPRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TAILORPRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TAILORPRO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Tailor Pro Stitch Master Class
 */
class TailorPro_Stitch_Master {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init'));
        
        // Plugin activation/deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Include required files
        add_action('plugins_loaded', array($this, 'includes'));
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('tailorpro', false, dirname(TAILORPRO_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize admin
        if (is_admin()) {
            new TailorPro_Admin();
        }
    }
    
    /**
     * Include required files
     */
    public function includes() {
        require_once TAILORPRO_PLUGIN_DIR . 'includes/class-tailorpro-activator.php';
        require_once TAILORPRO_PLUGIN_DIR . 'includes/class-tailorpro-db.php';
        require_once TAILORPRO_PLUGIN_DIR . 'includes/class-tailorpro-ajax.php';
        require_once TAILORPRO_PLUGIN_DIR . 'includes/class-tailorpro-assets.php';
        
        if (is_admin()) {
            require_once TAILORPRO_PLUGIN_DIR . 'pages/class-tailorpro-admin.php';
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        require_once TAILORPRO_PLUGIN_DIR . 'includes/class-tailorpro-activator.php';
        TailorPro_Activator::activate();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up if needed
    }
}

/**
 * Admin Class
 */
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
        
        // Enqueue styles
        wp_enqueue_style('tailorpro-admin', TAILORPRO_PLUGIN_URL . 'assets/css/admin.css', array(), TAILORPRO_VERSION);
        wp_enqueue_style('dashicons');
        
        // Enqueue scripts
        wp_enqueue_script('tailorpro-admin', TAILORPRO_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), TAILORPRO_VERSION, true);
        
        // Localize script
        wp_localize_script('tailorpro-admin', 'tailorpro_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tailorpro_ajax_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this item?', 'tailorpro'),
                'error' => __('An error occurred. Please try again.', 'tailorpro'),
                'success' => __('Operation completed successfully.', 'tailorpro')
            )
        ));
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

// Initialize the plugin
TailorPro_Stitch_Master::get_instance();