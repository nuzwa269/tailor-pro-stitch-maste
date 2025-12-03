<?php
/**
 * Tailor Pro Activator Class
 * 
 * Handles plugin activation including database table creation,
 * custom role setup, and default data insertion.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class TailorPro_Activator {
    
    /**
     * Plugin activation
     */
    public static function activate() {
        self::create_tables();
        self::create_roles();
        self::create_capabilities();
        self::insert_default_settings();
        
        // Set activation flag
        update_option('tailorpro_version', TAILORPRO_VERSION);
        update_option('tailorpro_activation_date', current_time('mysql'));
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Customers table
        $customers_table = $wpdb->prefix . 'tailorpro_customers';
        $customers_sql = "CREATE TABLE $customers_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            phone varchar(50) NOT NULL,
            email varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY phone (phone)
        ) $charset_collate;";
        
        // Orders table
        $orders_table = $wpdb->prefix . 'tailorpro_orders';
        $orders_sql = "CREATE TABLE $orders_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            customer_id int(11) NOT NULL,
            order_number varchar(50) NOT NULL UNIQUE,
            order_date date NOT NULL,
            due_date date NOT NULL,
            delivery_date date DEFAULT NULL,
            total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            paid_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(50) NOT NULL DEFAULT 'pending',
            priority varchar(20) NOT NULL DEFAULT 'normal',
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY customer_id (customer_id),
            KEY status (status),
            KEY due_date (due_date),
            FOREIGN KEY (customer_id) REFERENCES $customers_table(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        // Measurements table
        $measurements_table = $wpdb->prefix . 'tailorpro_measurements';
        $measurements_sql = "CREATE TABLE $measurements_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            order_id int(11) NOT NULL,
            item_type varchar(100) NOT NULL,
            item_name varchar(255) DEFAULT NULL,
            measurements_json longtext NOT NULL,
            notes text DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY order_id (order_id),
            FOREIGN KEY (order_id) REFERENCES $orders_table(id) ON DELETE CASCADE
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($customers_sql);
        dbDelta($orders_sql);
        dbDelta($measurements_sql);
    }
    
    /**
     * Create custom roles
     */
    private static function create_roles() {
        // Add Tailor Manager role
        add_role('tailor_manager', __('Tailor Manager', 'tailorpro'), array());
    }
    
    /**
     * Create capabilities
     */
    private static function create_capabilities() {
        // Define capabilities
        $capabilities = array(
            'tailorpro_view_dashboard',
            'tailorpro_manage_customers',
            'tailorpro_manage_orders',
            'tailorpro_view_reports',
            'tailorpro_manage_settings'
        );
        
        // Add capabilities to Tailor Manager role
        $tailor_manager = get_role('tailor_manager');
        if ($tailor_manager) {
            foreach ($capabilities as $cap) {
                $tailor_manager->add_cap($cap);
            }
        }
        
        // Add capabilities to Administrator role
        $admin = get_role('administrator');
        if ($admin) {
            foreach ($capabilities as $cap) {
                $admin->add_cap($cap);
            }
        }
    }
    
    /**
     * Insert default settings
     */
    private static function insert_default_settings() {
        $default_settings = array(
            'logo_url' => '',
            'theme_mode' => 'light',
            'currency' => '$',
            'date_format' => 'Y-m-d',
            'auto_generate_order_numbers' => true,
            'enable_notifications' => true,
            'low_stock_threshold' => 10
        );
        
        $existing_settings = get_option('tailorpro_settings', array());
        $settings = array_merge($default_settings, $existing_settings);
        update_option('tailorpro_settings', $settings);
    }
    
    /**
     * Install demo data
     */
    public static function install_demo_data() {
        global $wpdb;
        
        // Check if demo data already exists
        if (get_option('tailorpro_demo_data_installed')) {
            return;
        }
        
        $customers_table = $wpdb->prefix . 'tailorpro_customers';
        $orders_table = $wpdb->prefix . 'tailorpro_orders';
        $measurements_table = $wpdb->prefix . 'tailorpro_measurements';
        
        // Insert demo customers
        $demo_customers = array(
            array(
                'name' => 'John Smith',
                'phone' => '555-0101',
                'email' => 'john.smith@email.com',
                'address' => '123 Main Street',
                'city' => 'New York'
            ),
            array(
                'name' => 'Jane Doe',
                'phone' => '555-0102',
                'email' => 'jane.doe@email.com',
                'address' => '456 Oak Avenue',
                'city' => 'Los Angeles'
            ),
            array(
                'name' => 'Mike Johnson',
                'phone' => '555-0103',
                'email' => 'mike.johnson@email.com',
                'address' => '789 Pine Road',
                'city' => 'Chicago'
            )
        );
        
        foreach ($demo_customers as $customer) {
            $wpdb->insert($customers_table, $customer);
        }
        
        // Insert demo orders
        $customer_ids = $wpdb->get_col("SELECT id FROM $customers_table");
        
        $demo_orders = array(
            array(
                'customer_id' => $customer_ids[0],
                'order_number' => 'TP-' . date('Y') . '-001',
                'order_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+7 days')),
                'total_amount' => 150.00,
                'paid_amount' => 50.00,
                'status' => 'in_progress'
            ),
            array(
                'customer_id' => $customer_ids[1],
                'order_number' => 'TP-' . date('Y') . '-002',
                'order_date' => date('Y-m-d', strtotime('-2 days')),
                'due_date' => date('Y-m-d', strtotime('+5 days')),
                'total_amount' => 200.00,
                'paid_amount' => 200.00,
                'status' => 'completed'
            ),
            array(
                'customer_id' => $customer_ids[2],
                'order_number' => 'TP-' . date('Y') . '-003',
                'order_date' => date('Y-m-d', strtotime('-1 day')),
                'due_date' => date('Y-m-d', strtotime('+10 days')),
                'total_amount' => 300.00,
                'paid_amount' => 100.00,
                'status' => 'pending'
            )
        );
        
        foreach ($demo_orders as $order) {
            $wpdb->insert($orders_table, $order);
        }
        
        // Insert demo measurements
        $order_ids = $wpdb->get_col("SELECT id FROM $orders_table");
        
        $demo_measurements = array(
            array(
                'order_id' => $order_ids[0],
                'item_type' => 'Shirt',
                'item_name' => 'Formal Shirt',
                'measurements_json' => json_encode(array(
                    'chest' => '40"',
                    'waist' => '32"',
                    'length' => '28"',
                    'shoulder' => '18"',
                    'sleeve' => '24"'
                ))
            ),
            array(
                'order_id' => $order_ids[1],
                'item_type' => 'Suit',
                'item_name' => 'Business Suit',
                'measurements_json' => json_encode(array(
                    'chest' => '42"',
                    'waist' => '34"',
                    'jacket_length' => '30"',
                    'trouser_waist' => '34"',
                    'trouser_length' => '42"'
                ))
            ),
            array(
                'order_id' => $order_ids[2],
                'item_type' => 'Dress',
                'item_name' => 'Evening Dress',
                'measurements_json' => json_encode(array(
                    'bust' => '36"',
                    'waist' => '28"',
                    'hips' => '40"',
                    'length' => '52"',
                    'sleeve' => 'short'
                ))
            )
        );
        
        foreach ($demo_measurements as $measurement) {
            $wpdb->insert($measurements_table, $measurement);
        }
        
        // Mark demo data as installed
        update_option('tailorpro_demo_data_installed', true);
    }
}
