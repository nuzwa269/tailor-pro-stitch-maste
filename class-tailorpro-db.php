<?php
/**
 * Tailor Pro Database Helper Class
 * 
 * Provides safe database operations using $wpdb with proper
 * sanitization and prepared statements.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class TailorPro_DB {
    
    /**
     * Get database instance
     */
    private static $wpdb;
    
    /**
     * Initialize
     */
    public static function init() {
        global $wpdb;
        self::$wpdb = $wpdb;
    }
    
    /**
     * Get customers table name
     */
    public static function customers_table() {
        return self::$wpdb->prefix . 'tailorpro_customers';
    }
    
    /**
     * Get orders table name
     */
    public static function orders_table() {
        return self::$wpdb->prefix . 'tailorpro_orders';
    }
    
    /**
     * Get measurements table name
     */
    public static function measurements_table() {
        return self::$wpdb->prefix . 'tailorpro_measurements';
    }
    
    /**
     * Sanitize customer data
     */
    public static function sanitize_customer($data) {
        return array(
            'name' => sanitize_text_field($data['name'] ?? ''),
            'phone' => sanitize_text_field($data['phone'] ?? ''),
            'email' => sanitize_email($data['email'] ?? ''),
            'address' => sanitize_textarea_field($data['address'] ?? ''),
            'city' => sanitize_text_field($data['city'] ?? ''),
            'notes' => sanitize_textarea_field($data['notes'] ?? '')
        );
    }
    
    /**
     * Sanitize order data
     */
    public static function sanitize_order($data) {
        return array(
            'customer_id' => absint($data['customer_id'] ?? 0),
            'order_number' => sanitize_text_field($data['order_number'] ?? ''),
            'order_date' => sanitize_text_field($data['order_date'] ?? ''),
            'due_date' => sanitize_text_field($data['due_date'] ?? ''),
            'delivery_date' => sanitize_text_field($data['delivery_date'] ?? ''),
            'total_amount' => floatval($data['total_amount'] ?? 0),
            'paid_amount' => floatval($data['paid_amount'] ?? 0),
            'status' => sanitize_text_field($data['status'] ?? 'pending'),
            'priority' => sanitize_text_field($data['priority'] ?? 'normal'),
            'notes' => sanitize_textarea_field($data['notes'] ?? '')
        );
    }
    
    /**
     * Sanitize measurement data
     */
    public static function sanitize_measurement($data) {
        return array(
            'order_id' => absint($data['order_id'] ?? 0),
            'item_type' => sanitize_text_field($data['item_type'] ?? ''),
            'item_name' => sanitize_text_field($data['item_name'] ?? ''),
            'measurements_json' => wp_json_encode($data['measurements'] ?? array()),
            'notes' => sanitize_textarea_field($data['notes'] ?? '')
        );
    }
    
    /**
     * Insert customer
     */
    public static function insert_customer($data) {
        $data = self::sanitize_customer($data);
        
        // Check for duplicate phone
        $existing = self::$wpdb->get_var($self::$wpdb->prepare(
            "SELECT id FROM " . self::customers_table() . " WHERE phone = %s",
            $data['phone']
        ));
        
        if ($existing) {
            return new WP_Error('duplicate_phone', __('Phone number already exists.', 'tailorpro'));
        }
        
        $result = self::$wpdb->insert(
            self::customers_table(),
            $data,
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('insert_failed', __('Failed to insert customer.', 'tailorpro'));
        }
        
        return self::$wpdb->insert_id;
    }
    
    /**
     * Update customer
     */
    public static function update_customer($id, $data) {
        $id = absint($id);
        $data = self::sanitize_customer($data);
        
        // Check for duplicate phone (excluding current customer)
        $existing = self::$wpdb->get_var($self::$wpdb->prepare(
            "SELECT id FROM " . self::customers_table() . " WHERE phone = %s AND id != %d",
            $data['phone'],
            $id
        ));
        
        if ($existing) {
            return new WP_Error('duplicate_phone', __('Phone number already exists.', 'tailorpro'));
        }
        
        $result = self::$wpdb->update(
            self::customers_table(),
            $data,
            array('id' => $id),
            array('%s', '%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error('update_failed', __('Failed to update customer.', 'tailorpro'));
        }
        
        return true;
    }
    
    /**
     * Delete customer
     */
    public static function delete_customer($id) {
        $id = absint($id);
        
        // Check if customer has orders
        $orders_count = self::$wpdb->get_var($self::$wpdb->prepare(
            "SELECT COUNT(*) FROM " . self::orders_table() . " WHERE customer_id = %d",
            $id
        ));
        
        if ($orders_count > 0) {
            return new WP_Error('has_orders', __('Cannot delete customer with existing orders.', 'tailorpro'));
        }
        
        $result = self::$wpdb->delete(
            self::customers_table(),
            array('id' => $id),
            array('%d')
        );
        
        return $result !== false;
    }
    
    /**
     * Get customer by ID
     */
    public static function get_customer($id) {
        $id = absint($id);
        
        return self::$wpdb->get_row($self::$wpdb->prepare(
            "SELECT * FROM " . self::customers_table() . " WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Get all customers
     */
    public static function get_customers($limit = 0, $offset = 0, $search = '') {
        $where = '';
        if (!empty($search)) {
            $where = self::$wpdb->prepare(
                " WHERE name LIKE %s OR phone LIKE %s OR email LIKE %s",
                '%' . $search . '%',
                '%' . $search . '%',
                '%' . $search . '%'
            );
        }
        
        $limit_clause = '';
        if ($limit > 0) {
            $limit_clause = self::$wpdb->prepare(' LIMIT %d', $limit);
            if ($offset > 0) {
                $limit_clause = self::$wpdb->prepare(' LIMIT %d, %d', $offset, $limit);
            }
        }
        
        return self::$wpdb->get_results(
            "SELECT * FROM " . self::customers_table() . $where . " ORDER BY name ASC" . $limit_clause
        );
    }
    
    /**
     * Count customers
     */
    public static function count_customers($search = '') {
        $where = '';
        if (!empty($search)) {
            $where = self::$wpdb->prepare(
                " WHERE name LIKE %s OR phone LIKE %s OR email LIKE %s",
                '%' . $search . '%',
                '%' . $search . '%',
                '%' . $search . '%'
            );
        }
        
        return self::$wpdb->get_var("SELECT COUNT(*) FROM " . self::customers_table() . $where);
    }
    
    /**
     * Insert order
     */
    public static function insert_order($data) {
        $data = self::sanitize_order($data);
        
        // Generate order number if not provided
        if (empty($data['order_number'])) {
            $data['order_number'] = self::generate_order_number();
        }
        
        $result = self::$wpdb->insert(
            self::orders_table(),
            $data,
            array('%d', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('insert_failed', __('Failed to insert order.', 'tailorpro'));
        }
        
        return self::$wpdb->insert_id;
    }
    
    /**
     * Update order
     */
    public static function update_order($id, $data) {
        $id = absint($id);
        $data = self::sanitize_order($data);
        
        $result = self::$wpdb->update(
            self::orders_table(),
            $data,
            array('id' => $id),
            array('%d', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error('update_failed', __('Failed to update order.', 'tailorpro'));
        }
        
        return true;
    }
    
    /**
     * Delete order
     */
    public static function delete_order($id) {
        $id = absint($id);
        
        $result = self::$wpdb->delete(
            self::orders_table(),
            array('id' => $id),
            array('%d')
        );
        
        return $result !== false;
    }
    
    /**
     * Get order by ID
     */
    public static function get_order($id) {
        $id = absint($id);
        
        return self::$wpdb->get_row($self::$wpdb->prepare(
            "SELECT o.*, c.name as customer_name, c.phone as customer_phone 
             FROM " . self::orders_table() . " o 
             LEFT JOIN " . self::customers_table() . " c ON o.customer_id = c.id 
             WHERE o.id = %d",
            $id
        ));
    }
    
    /**
     * Get all orders
     */
    public static function get_orders($limit = 0, $offset = 0, $search = '', $status = '') {
        $where = "WHERE 1=1";
        
        if (!empty($search)) {
            $where .= self::$wpdb->prepare(
                " AND (o.order_number LIKE %s OR c.name LIKE %s OR c.phone LIKE %s)",
                '%' . $search . '%',
                '%' . $search . '%',
                '%' . $search . '%'
            );
        }
        
        if (!empty($status)) {
            $where .= self::$wpdb->prepare(" AND o.status = %s", $status);
        }
        
        $limit_clause = '';
        if ($limit > 0) {
            $limit_clause = self::$wpdb->prepare(' LIMIT %d', $limit);
            if ($offset > 0) {
                $limit_clause = self::$wpdb->prepare(' LIMIT %d, %d', $offset, $limit);
            }
        }
        
        return self::$wpdb->get_results(
            "SELECT o.*, c.name as customer_name, c.phone as customer_phone 
             FROM " . self::orders_table() . " o 
             LEFT JOIN " . self::customers_table() . " c ON o.customer_id = c.id 
             $where 
             ORDER BY o.order_date DESC" . $limit_clause
        );
    }
    
    /**
     * Count orders
     */
    public static function count_orders($search = '', $status = '') {
        $where = "WHERE 1=1";
        
        if (!empty($search)) {
            $where .= self::$wpdb->prepare(
                " AND (o.order_number LIKE %s OR c.name LIKE %s OR c.phone LIKE %s)",
                '%' . $search . '%',
                '%' . $search . '%',
                '%' . $search . '%'
            );
        }
        
        if (!empty($status)) {
            $where .= self::$wpdb->prepare(" AND o.status = %s", $status);
        }
        
        return self::$wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::orders_table() . " o 
             LEFT JOIN " . self::customers_table() . " c ON o.customer_id = c.id 
             $where"
        );
    }
    
    /**
     * Insert measurement
     */
    public static function insert_measurement($data) {
        $data = self::sanitize_measurement($data);
        
        $result = self::$wpdb->insert(
            self::measurements_table(),
            $data,
            array('%d', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('insert_failed', __('Failed to insert measurement.', 'tailorpro'));
        }
        
        return self::$wpdb->insert_id;
    }
    
    /**
     * Get measurements for order
     */
    public static function get_order_measurements($order_id) {
        $order_id = absint($order_id);
        
        return self::$wpdb->get_results($self::$wpdb->prepare(
            "SELECT * FROM " . self::measurements_table() . " WHERE order_id = %d ORDER BY id ASC",
            $order_id
        ));
    }
    
    /**
     * Generate unique order number
     */
    public static function generate_order_number() {
        $year = date('Y');
        $prefix = 'TP-' . $year . '-';
        
        // Get the highest order number for current year
        $last_order = self::$wpdb->get_var(self::$wpdb->prepare(
            "SELECT order_number FROM " . self::orders_table() . " 
             WHERE order_number LIKE %s 
             ORDER BY order_number DESC 
             LIMIT 1",
            $prefix . '%'
        ));
        
        if ($last_order) {
            // Extract the number part and increment
            $number = (int) substr($last_order, strlen($prefix)) + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get dashboard statistics
     */
    public static function get_dashboard_stats() {
        $stats = array();
        
        // Total customers
        $stats['total_customers'] = self::$wpdb->get_var("SELECT COUNT(*) FROM " . self::customers_table());
        
        // Total orders
        $stats['total_orders'] = self::$wpdb->get_var("SELECT COUNT(*) FROM " . self::orders_table());
        
        // Pending orders
        $stats['pending_orders'] = self::$wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::orders_table() . " WHERE status = 'pending'"
        );
        
        // In progress orders
        $stats['in_progress_orders'] = self::$wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::orders_table() . " WHERE status = 'in_progress'"
        );
        
        // Completed orders
        $stats['completed_orders'] = self::$wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::orders_table() . " WHERE status = 'completed'"
        );
        
        // Total revenue
        $stats['total_revenue'] = self::$wpdb->get_var(
            "SELECT SUM(total_amount) FROM " . self::orders_table()
        );
        
        // Paid amount
        $stats['paid_amount'] = self::$wpdb->get_var(
            "SELECT SUM(paid_amount) FROM " . self::orders_table()
        );
        
        // Outstanding amount
        $stats['outstanding_amount'] = $stats['total_revenue'] - $stats['paid_amount'];
        
        // Overdue orders
        $stats['overdue_orders'] = self::$wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::orders_table() . " WHERE due_date < CURDATE() AND status NOT IN ('completed', 'cancelled')"
        );
        
        // This month orders
        $stats['month_orders'] = self::$wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::orders_table() . " WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())"
        );
        
        // This month revenue
        $stats['month_revenue'] = self::$wpdb->get_var(
            "SELECT SUM(total_amount) FROM " . self::orders_table() . " WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())"
        );
        
        return $stats;
    }
}

// Initialize the DB class
TailorPro_DB::init();