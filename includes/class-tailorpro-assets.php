<?php
/**
 * Tailor Pro Assets Manager Class
 * 
 * Handles enqueuing of all scripts and styles with proper
 * dependencies and localization.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class TailorPro_Assets {
    
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_footer', array($this, 'add_theme_styles'));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'tailorpro') === false) {
            return;
        }
        
        // CSS Dependencies
        $css_deps = array('dashicons');
        
        // Enqueue CSS
        wp_enqueue_style(
            'tailorpro-admin',
            TAILORPRO_PLUGIN_URL . 'assets/css/admin.css',
            $css_deps,
            TAILORPRO_VERSION
        );
        
        // Enqueue Chart.js for dashboard
        if (strpos($hook, 'tailorpro-dashboard') !== false) {
            wp_enqueue_script(
                'chart-js',
                'https://cdn.jsdelivr.net/npm/chart.js',
                array(),
                '3.9.1',
                true
            );
        }
        
        // JavaScript Dependencies
        $js_deps = array('jquery');
        
        // Enqueue JavaScript
        wp_enqueue_script(
            'tailorpro-admin',
            TAILORPRO_PLUGIN_URL . 'assets/js/admin.js',
            $js_deps,
            TAILORPRO_VERSION,
            true
        );
        
        // Localize script for AJAX
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
     * Add theme styles to head
     */
    public function add_theme_styles() {
        // Only add to our plugin pages
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'tailorpro') === false) {
            return;
        }
        
        $settings = get_option('tailorpro_settings', array());
        $theme_mode = $settings['theme_mode'] ?? 'light';
        
        $custom_css = "
        <style id='tailorpro-theme-styles'>
        :root {
            --tailorpro-primary: #007cba;
            --tailorpro-primary-hover: #005a87;
            --tailorpro-success: #28a745;
            --tailorpro-warning: #ffc107;
            --tailorpro-danger: #dc3545;
            --tailorpro-info: #17a2b8;
            --tailorpro-light: #f8f9fa;
            --tailorpro-dark: #343a40;
            
            /* Light theme variables */
            --tailorpro-bg-primary: #ffffff;
            --tailorpro-bg-secondary: #f8f9fa;
            --tailorpro-text-primary: #212529;
            --tailorpro-text-secondary: #6c757d;
            --tailorpro-border: #dee2e6;
            --tailorpro-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        [data-theme='dark'] {
            /* Dark theme variables */
            --tailorpro-bg-primary: #1a1a1a;
            --tailorpro-bg-secondary: #2d2d2d;
            --tailorpro-text-primary: #ffffff;
            --tailorpro-text-secondary: #b0b0b0;
            --tailorpro-border: #404040;
            --tailorpro-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        /* Theme-aware styles */
        #wpbody-content .tailorpro-container {
            background-color: var(--tailorpro-bg-primary);
            color: var(--tailorpro-text-primary);
            border: 1px solid var(--tailorpro-border);
            border-radius: 8px;
            box-shadow: var(--tailorpro-shadow);
        }
        
        .tailorpro-card {
            background-color: var(--tailorpro-bg-primary);
            border: 1px solid var(--tailorpro-border);
            border-radius: 8px;
            box-shadow: var(--tailorpro-shadow);
        }
        
        .tailorpro-card-header {
            background-color: var(--tailorpro-bg-secondary);
            border-bottom: 1px solid var(--tailorpro-border);
            color: var(--tailorpro-text-primary);
        }
        
        .tailorpro-card-body {
            background-color: var(--tailorpro-bg-primary);
            color: var(--tailorpro-text-primary);
        }
        
        .tailorpro-btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .tailorpro-btn-primary {
            background-color: var(--tailorpro-primary);
            border-color: var(--tailorpro-primary);
            color: white;
        }
        
        .tailorpro-btn-primary:hover {
            background-color: var(--tailorpro-primary-hover);
            border-color: var(--tailorpro-primary-hover);
        }
        
        .tailorpro-form-control {
            background-color: var(--tailorpro-bg-primary);
            border: 1px solid var(--tailorpro-border);
            color: var(--tailorpro-text-primary);
        }
        
        .tailorpro-form-control:focus {
            background-color: var(--tailorpro-bg-primary);
            border-color: var(--tailorpro-primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 124, 186, 0.25);
            color: var(--tailorpro-text-primary);
        }
        
        .tailorpro-table {
            background-color: var(--tailorpro-bg-primary);
            color: var(--tailorpro-text-primary);
        }
        
        .tailorpro-table th {
            background-color: var(--tailorpro-bg-secondary);
            border-color: var(--tailorpro-border);
            color: var(--tailorpro-text-primary);
        }
        
        .tailorpro-table td {
            border-color: var(--tailorpro-border);
        }
        
        .tailorpro-modal-content {
            background-color: var(--tailorpro-bg-primary);
            border: 1px solid var(--tailorpro-border);
            color: var(--tailorpro-text-primary);
        }
        
        .tailorpro-modal-header {
            background-color: var(--tailorpro-bg-secondary);
            border-bottom: 1px solid var(--tailorpro-border);
            color: var(--tailorpro-text-primary);
        }
        
        .tailorpro-alert {
            border-radius: 6px;
        }
        
        .tailorpro-alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: var(--tailorpro-success);
        }
        
        .tailorpro-alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #856404;
        }
        
        .tailorpro-alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: var(--tailorpro-danger);
        }
        
        .tailorpro-alert-info {
            background-color: rgba(23, 162, 184, 0.1);
            border: 1px solid rgba(23, 162, 184, 0.3);
            color: var(--tailorpro-info);
        }
        
        /* Status badges */
        .tailorpro-badge-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .tailorpro-badge-in-progress {
            background-color: #17a2b8;
            color: white;
        }
        
        .tailorpro-badge-completed {
            background-color: #28a745;
            color: white;
        }
        
        .tailorpro-badge-cancelled {
            background-color: #dc3545;
            color: white;
        }
        
        /* Loading spinner */
        .tailorpro-loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--tailorpro-border);
            border-radius: 50%;
            border-top-color: var(--tailorpro-primary);
            animation: tailorpro-spin 1s ease-in-out infinite;
        }
        
        @keyframes tailorpro-spin {
            to { transform: rotate(360deg); }
        }
        
        /* Print styles */
        @media print {
            .tailorpro-no-print {
                display: none !important;
            }
            
            .tailorpro-container {
                border: none;
                box-shadow: none;
            }
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .tailorpro-responsive-table {
                font-size: 14px;
            }
            
            .tailorpro-responsive-table th,
            .tailorpro-responsive-table td {
                padding: 8px 4px;
            }
        }
        
        /* Focus styles for accessibility */
        .tailorpro-btn:focus,
        .tailorpro-form-control:focus,
        .tailorpro-modal-close:focus {
            outline: 2px solid var(--tailorpro-primary);
            outline-offset: 2px;
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            :root {
                --tailorpro-border: #000000;
                --tailorpro-text-secondary: #000000;
            }
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .tailorpro-btn,
            .tailorpro-loading {
                animation: none;
                transition: none;
            }
        }
        </style>
        ";
        
        echo $custom_css;
        
        // Add theme mode data attribute
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.body.setAttribute('data-theme', '" . esc_js($theme_mode) . "');
            });
        </script>";
    }
    
    /**
     * Get asset URL
     */
    public static function get_asset_url($path) {
        return TAILORPRO_PLUGIN_URL . 'assets/' . ltrim($path, '/');
    }
    
    /**
     * Enqueue media uploader
     */
    public static function enqueue_media_uploader() {
        if (isset($_GET['page']) && strpos($_GET['page'], 'tailorpro') !== false) {
            wp_enqueue_media();
        }
    }
    
    /**
     * Add print styles
     */
    public static function add_print_styles() {
        echo "
        <style media='print'>
        .tailorpro-no-print {
            display: none !important;
        }
        
        .tailorpro-container {
            border: none !important;
            box-shadow: none !important;
            background: white !important;
        }
        
        .tailorpro-card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
        
        .tailorpro-table {
            border-collapse: collapse !important;
        }
        
        .tailorpro-table th,
        .tailorpro-table td {
            border: 1px solid #000 !important;
            padding: 8px !important;
        }
        
        .tailorpro-btn {
            display: none !important;
        }
        </style>
        ";
    }
}

// Initialize assets manager
new TailorPro_Assets();
