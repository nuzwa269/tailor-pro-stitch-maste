<?php
/**
 * Uninstall script for Tailor Pro Stitch Master
 * 
 * This file is called when the plugin is uninstalled
 * to clean up all plugin data including database tables,
 * options, and custom roles.
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Clean up all plugin data on uninstall
 */
function tailorpro_cleanup_on_uninstall() {
    global $wpdb;
    
    // Remove database tables
    $tables = array(
        $wpdb->prefix . 'tailorpro_customers',
        $wpdb->prefix . 'tailorpro_orders',
        $wpdb->prefix . 'tailorpro_measurements'
    );
    
    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }
    
    // Remove plugin options
    delete_option('tailorpro_version');
    delete_option('tailorpro_settings');
    delete_option('tailorpro_demo_data_installed');
    
    // Remove custom roles and capabilities
    $role = get_role('tailor_manager');
    if ($role) {
        $role->remove_cap('tailorpro_view_dashboard');
        $role->remove_cap('tailorpro_manage_customers');
        $role->remove_cap('tailorpro_manage_orders');
        $role->remove_cap('tailorpro_view_reports');
        $role->remove_cap('tailorpro_manage_settings');
        remove_role('tailor_manager');
    }
    
    // Remove capabilities from administrator role
    $admin_role = get_role('administrator');
    if ($admin_role) {
        $admin_role->remove_cap('tailorpro_view_dashboard');
        $admin_role->remove_cap('tailorpro_manage_customers');
        $admin_role->remove_cap('tailorpro_manage_orders');
        $admin_role->remove_cap('tailorpro_view_reports');
        $admin_role->remove_cap('tailorpro_manage_settings');
    }
}

// Execute cleanup
tailorpro_cleanup_on_uninstall();