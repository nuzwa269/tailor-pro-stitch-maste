<?php
/**
 * Tailor Pro AJAX Handler Class
 * 
 * Handles all AJAX requests with proper security measures
 * including nonces verification and capability checks.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class TailorPro_Ajax {
    
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize AJAX hooks
     */
    private function init_hooks() {
        // Customer management
        add_action('wp_ajax_tailorpro_add_customer', array($this, 'add_customer'));
        add_action('wp_ajax_tailorpro_edit_customer', array($this, 'edit_customer'));
        add_action('wp_ajax_tailorpro_delete_customer', array($this, 'delete_customer'));
        add_action('wp_ajax_tailorpro_get_customer', array($this, 'get_customer'));
        add_action('wp_ajax_tailorpro_get_customers', array($this, 'get_customers'));
        
        // Order management
        add_action('wp_ajax_tailorpro_add_order', array($this, 'add_order'));
        add_action('wp_ajax_tailorpro_edit_order', array($this, 'edit_order'));
        add_action('wp_ajax_tailorpro_delete_order', array($this, 'delete_order'));
        add_action('wp_ajax_tailorpro_get_order', array($this, 'get_order'));
        add_action('wp_ajax_tailorpro_get_orders', array($this, 'get_orders'));
        add_action('wp_ajax_tailorpro_update_order_status', array($this, 'update_order_status'));
        
        // Measurement management
        add_action('wp_ajax_tailorpro_add_measurement', array($this, 'add_measurement'));
        add_action('wp_ajax_tailorpro_edit_measurement', array($this, 'edit_measurement'));
        add_action('wp_ajax_tailorpro_delete_measurement', array($this, 'delete_measurement'));
        add_action('wp_ajax_tailorpro_get_measurements', array($this, 'get_measurements'));
        
        // Dashboard
        add_action('wp_ajax_tailorpro_get_dashboard_stats', array($this, 'get_dashboard_stats'));
        
        // Settings
        add_action('wp_ajax_tailorpro_save_settings', array($this, 'save_settings'));
        add_action('wp_ajax_tailorpro_get_settings', array($this, 'get_settings'));
        
        // Demo data
        add_action('wp_ajax_tailorpro_install_demo_data', array($this, 'install_demo_data'));
        
        // Import/Export
        add_action('wp_ajax_tailorpro_export_data', array($this, 'export_data'));
        add_action('wp_ajax_tailorpro_import_data', array($this, 'import_data'));
    }
    
    /**
     * Send JSON response
     */
    private function send_response($success = true, $data = array(), $message = '') {
        wp_send_json(array(
            'success' => $success,
            'data' => $data,
            'message' => $message
        ));
    }
    
    /**
     * Verify AJAX request
     */
    private function verify_request($capability) {
        // Check nonce
        check_ajax_referer('tailorpro_ajax_nonce', 'nonce');
        
        // Check capability
        if (!current_user_can($capability)) {
            wp_die(__('Insufficient permissions.', 'tailorpro'));
        }
    }
    
    // Customer Management
    
    /**
     * Add customer
     */
    public function add_customer() {
        $this->verify_request('tailorpro_manage_customers');
        
        $data = array(
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'phone' => sanitize_text_field($_POST['phone'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'address' => sanitize_textarea_field($_POST['address'] ?? ''),
            'city' => sanitize_text_field($_POST['city'] ?? ''),
            'notes' => sanitize_textarea_field($_POST['notes'] ?? '')
        );
        
        if (empty($data['name']) || empty($data['phone'])) {
            $this->send_response(false, array(), __('Name and phone are required.', 'tailorpro'));
        }
        
        $customer_id = TailorPro_DB::insert_customer($data);
        
        if (is_wp_error($customer_id)) {
            $this->send_response(false, array(), $customer_id->get_error_message());
        }
        
        $customer = TailorPro_DB::get_customer($customer_id);
        $this->send_response(true, $customer, __('Customer added successfully.', 'tailorpro'));
    }
    
    /**
     * Edit customer
     */
    public function edit_customer() {
        $this->verify_request('tailorpro_manage_customers');
        
        $customer_id = absint($_POST['customer_id'] ?? 0);
        if (!$customer_id) {
            $this->send_response(false, array(), __('Invalid customer ID.', 'tailorpro'));
        }
        
        $data = array(
            'name' => sanitize_text_field($_POST['name'] ?? ''),
            'phone' => sanitize_text_field($_POST['phone'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'address' => sanitize_textarea_field($_POST['address'] ?? ''),
            'city' => sanitize_text_field($_POST['city'] ?? ''),
            'notes' => sanitize_textarea_field($_POST['notes'] ?? '')
        );
        
        if (empty($data['name']) || empty($data['phone'])) {
            $this->send_response(false, array(), __('Name and phone are required.', 'tailorpro'));
        }
        
        $result = TailorPro_DB::update_customer($customer_id, $data);
        
        if (is_wp_error($result)) {
            $this->send_response(false, array(), $result->get_error_message());
        }
        
        $customer = TailorPro_DB::get_customer($customer_id);
        $this->send_response(true, $customer, __('Customer updated successfully.', 'tailorpro'));
    }
    
    /**
     * Delete customer
     */
    public function delete_customer() {
        $this->verify_request('tailorpro_manage_customers');
        
        $customer_id = absint($_POST['customer_id'] ?? 0);
        if (!$customer_id) {
            $this->send_response(false, array(), __('Invalid customer ID.', 'tailorpro'));
        }
        
        $result = TailorPro_DB::delete_customer($customer_id);
        
        if (!$result) {
            $this->send_response(false, array(), __('Failed to delete customer.', 'tailorpro'));
        }
        
        $this->send_response(true, array(), __('Customer deleted successfully.', 'tailorpro'));
    }
    
    /**
     * Get customer
     */
    public function get_customer() {
        $this->verify_request('tailorpro_manage_customers');
        
        $customer_id = absint($_POST['customer_id'] ?? 0);
        if (!$customer_id) {
            $this->send_response(false, array(), __('Invalid customer ID.', 'tailorpro'));
        }
        
        $customer = TailorPro_DB::get_customer($customer_id);
        if (!$customer) {
            $this->send_response(false, array(), __('Customer not found.', 'tailorpro'));
        }
        
        $this->send_response(true, $customer);
    }
    
    /**
     * Get customers list
     */
    public function get_customers() {
        $this->verify_request('tailorpro_view_dashboard');
        
        $search = sanitize_text_field($_POST['search'] ?? '');
        $limit = absint($_POST['limit'] ?? 20);
        $offset = absint($_POST['offset'] ?? 0);
        
        $customers = TailorPro_DB::get_customers($limit, $offset, $search);
        $total = TailorPro_DB::count_customers($search);
        
        $this->send_response(true, array(
            'customers' => $customers,
            'total' => $total
        ));
    }
    
    // Order Management
    
    /**
     * Add order
     */
    public function add_order() {
        $this->verify_request('tailorpro_manage_orders');
        
        $data = array(
            'customer_id' => absint($_POST['customer_id'] ?? 0),
            'order_number' => sanitize_text_field($_POST['order_number'] ?? ''),
            'order_date' => sanitize_text_field($_POST['order_date'] ?? ''),
            'due_date' => sanitize_text_field($_POST['due_date'] ?? ''),
            'delivery_date' => sanitize_text_field($_POST['delivery_date'] ?? ''),
            'total_amount' => floatval($_POST['total_amount'] ?? 0),
            'paid_amount' => floatval($_POST['paid_amount'] ?? 0),
            'status' => sanitize_text_field($_POST['status'] ?? 'pending'),
            'priority' => sanitize_text_field($_POST['priority'] ?? 'normal'),
            'notes' => sanitize_textarea_field($_POST['notes'] ?? '')
        );
        
        if (!$data['customer_id'] || empty($data['order_date']) || empty($data['due_date'])) {
            $this->send_response(false, array(), __('Customer, order date, and due date are required.', 'tailorpro'));
        }
        
        $order_id = TailorPro_DB::insert_order($data);
        
        if (is_wp_error($order_id)) {
            $this->send_response(false, array(), $order_id->get_error_message());
        }
        
        $order = TailorPro_DB::get_order($order_id);
        $this->send_response(true, $order, __('Order added successfully.', 'tailorpro'));
    }
    
    /**
     * Edit order
     */
    public function edit_order() {
        $this->verify_request('tailorpro_manage_orders');
        
        $order_id = absint($_POST['order_id'] ?? 0);
        if (!$order_id) {
            $this->send_response(false, array(), __('Invalid order ID.', 'tailorpro'));
        }
        
        $data = array(
            'customer_id' => absint($_POST['customer_id'] ?? 0),
            'order_number' => sanitize_text_field($_POST['order_number'] ?? ''),
            'order_date' => sanitize_text_field($_POST['order_date'] ?? ''),
            'due_date' => sanitize_text_field($_POST['due_date'] ?? ''),
            'delivery_date' => sanitize_text_field($_POST['delivery_date'] ?? ''),
            'total_amount' => floatval($_POST['total_amount'] ?? 0),
            'paid_amount' => floatval($_POST['paid_amount'] ?? 0),
            'status' => sanitize_text_field($_POST['status'] ?? 'pending'),
            'priority' => sanitize_text_field($_POST['priority'] ?? 'normal'),
            'notes' => sanitize_textarea_field($_POST['notes'] ?? '')
        );
        
        if (!$data['customer_id'] || empty($data['order_date']) || empty($data['due_date'])) {
            $this->send_response(false, array(), __('Customer, order date, and due date are required.', 'tailorpro'));
        }
        
        $result = TailorPro_DB::update_order($order_id, $data);
        
        if (is_wp_error($result)) {
            $this->send_response(false, array(), $result->get_error_message());
        }
        
        $order = TailorPro_DB::get_order($order_id);
        $this->send_response(true, $order, __('Order updated successfully.', 'tailorpro'));
    }
    
    /**
     * Delete order
     */
    public function delete_order() {
        $this->verify_request('tailorpro_manage_orders');
        
        $order_id = absint($_POST['order_id'] ?? 0);
        if (!$order_id) {
            $this->send_response(false, array(), __('Invalid order ID.', 'tailorpro'));
        }
        
        $result = TailorPro_DB::delete_order($order_id);
        
        if (!$result) {
            $this->send_response(false, array(), __('Failed to delete order.', 'tailorpro'));
        }
        
        $this->send_response(true, array(), __('Order deleted successfully.', 'tailorpro'));
    }
    
    /**
     * Get order
     */
    public function get_order() {
        $this->verify_request('tailorpro_manage_orders');
        
        $order_id = absint($_POST['order_id'] ?? 0);
        if (!$order_id) {
            $this->send_response(false, array(), __('Invalid order ID.', 'tailorpro'));
        }
        
        $order = TailorPro_DB::get_order($order_id);
        if (!$order) {
            $this->send_response(false, array(), __('Order not found.', 'tailorpro'));
        }
        
        $this->send_response(true, $order);
    }
    
    /**
     * Get orders list
     */
    public function get_orders() {
        $this->verify_request('tailorpro_view_dashboard');
        
        $search = sanitize_text_field($_POST['search'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? '');
        $limit = absint($_POST['limit'] ?? 20);
        $offset = absint($_POST['offset'] ?? 0);
        
        $orders = TailorPro_DB::get_orders($limit, $offset, $search, $status);
        $total = TailorPro_DB::count_orders($search, $status);
        
        $this->send_response(true, array(
            'orders' => $orders,
            'total' => $total
        ));
    }
    
    /**
     * Update order status
     */
    public function update_order_status() {
        $this->verify_request('tailorpro_manage_orders');
        
        $order_id = absint($_POST['order_id'] ?? 0);
        $status = sanitize_text_field($_POST['status'] ?? '');
        
        if (!$order_id || empty($status)) {
            $this->send_response(false, array(), __('Invalid order ID or status.', 'tailorpro'));
        }
        
        $result = TailorPro_DB::update_order($order_id, array('status' => $status));
        
        if (is_wp_error($result)) {
            $this->send_response(false, array(), $result->get_error_message());
        }
        
        $order = TailorPro_DB::get_order($order_id);
        $this->send_response(true, $order, __('Order status updated successfully.', 'tailorpro'));
    }
    
    // Measurement Management
    
    /**
     * Add measurement
     */
    public function add_measurement() {
        $this->verify_request('tailorpro_manage_orders');
        
        $order_id = absint($_POST['order_id'] ?? 0);
        if (!$order_id) {
            $this->send_response(false, array(), __('Invalid order ID.', 'tailorpro'));
        }
        
        $measurements = array();
        if (isset($_POST['measurements']) && is_array($_POST['measurements'])) {
            foreach ($_POST['measurements'] as $key => $value) {
                $measurements[sanitize_text_field($key)] = sanitize_text_field($value);
            }
        }
        
        $data = array(
            'order_id' => $order_id,
            'item_type' => sanitize_text_field($_POST['item_type'] ?? ''),
            'item_name' => sanitize_text_field($_POST['item_name'] ?? ''),
            'measurements' => $measurements,
            'notes' => sanitize_textarea_field($_POST['notes'] ?? '')
        );
        
        $measurement_id = TailorPro_DB::insert_measurement($data);
        
        if (is_wp_error($measurement_id)) {
            $this->send_response(false, array(), $measurement_id->get_error_message());
        }
        
        $this->send_response(true, array('measurement_id' => $measurement_id), __('Measurement added successfully.', 'tailorpro'));
    }
    
    /**
     * Get measurements for order
     */
    public function get_measurements() {
        $this->verify_request('tailorpro_view_dashboard');
        
        $order_id = absint($_POST['order_id'] ?? 0);
        if (!$order_id) {
            $this->send_response(false, array(), __('Invalid order ID.', 'tailorpro'));
        }
        
        $measurements = TailorPro_DB::get_order_measurements($order_id);
        
        // Decode JSON measurements
        foreach ($measurements as &$measurement) {
            $measurement->measurements = json_decode($measurement->measurements_json, true);
            unset($measurement->measurements_json);
        }
        
        $this->send_response(true, $measurements);
    }
    
    // Dashboard
    
    /**
     * Get dashboard statistics
     */
    public function get_dashboard_stats() {
        $this->verify_request('tailorpro_view_dashboard');
        
        $stats = TailorPro_DB::get_dashboard_stats();
        $this->send_response(true, $stats);
    }
    
    // Settings
    
    /**
     * Save settings
     */
    public function save_settings() {
        $this->verify_request('tailorpro_manage_settings');
        
        $settings = get_option('tailorpro_settings', array());
        
        $new_settings = array(
            'logo_url' => esc_url_raw($_POST['logo_url'] ?? ''),
            'theme_mode' => sanitize_text_field($_POST['theme_mode'] ?? 'light'),
            'currency' => sanitize_text_field($_POST['currency'] ?? '$'),
            'date_format' => sanitize_text_field($_POST['date_format'] ?? 'Y-m-d'),
            'auto_generate_order_numbers' => !empty($_POST['auto_generate_order_numbers']),
            'enable_notifications' => !empty($_POST['enable_notifications']),
            'low_stock_threshold' => absint($_POST['low_stock_threshold'] ?? 10)
        );
        
        $settings = array_merge($settings, $new_settings);
        update_option('tailorpro_settings', $settings);
        
        $this->send_response(true, $settings, __('Settings saved successfully.', 'tailorpro'));
    }
    
    /**
     * Get settings
     */
    public function get_settings() {
        $this->verify_request('tailorpro_view_dashboard');
        
        $settings = get_option('tailorpro_settings', array());
        $this->send_response(true, $settings);
    }
    
    // Demo Data
    
    /**
     * Install demo data
     */
    public function install_demo_data() {
        $this->verify_request('tailorpro_manage_settings');
        
        TailorPro_Activator::install_demo_data();
        $this->send_response(true, array(), __('Demo data installed successfully.', 'tailorpro'));
    }
    
    // Import/Export
    
    /**
     * Export data
     */
    public function export_data() {
        $this->verify_request('tailorpro_view_reports');
        
        $type = sanitize_text_field($_POST['export_type'] ?? 'customers');
        
        switch ($type) {
            case 'customers':
                $data = TailorPro_DB::get_customers();
                break;
            case 'orders':
                $data = TailorPro_DB::get_orders();
                break;
            case 'measurements':
                global $wpdb;
                $data = $wpdb->get_results("SELECT * FROM " . TailorPro_DB::measurements_table());
                break;
            default:
                $this->send_response(false, array(), __('Invalid export type.', 'tailorpro'));
        }
        
        $filename = 'tailorpro_' . $type . '_' . date('Y-m-d_H-i-s') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Length: ' . strlen(json_encode($data)));
        
        echo json_encode($data);
        exit;
    }
    
    /**
     * Import data
     */
    public function import_data() {
        $this->verify_request('tailorpro_manage_settings');
        
        if (empty($_FILES['import_file'])) {
            $this->send_response(false, array(), __('No file uploaded.', 'tailorpro'));
        }
        
        $file = $_FILES['import_file'];
        $type = sanitize_text_field($_POST['import_type'] ?? 'customers');
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->send_response(false, array(), __('File upload error.', 'tailorpro'));
        }
        
        $content = file_get_contents($file['tmp_name']);
        $data = json_decode($content, true);
        
        if (!$data) {
            $this->send_response(false, array(), __('Invalid JSON file.', 'tailorpro'));
        }
        
        $imported = 0;
        $errors = array();
        
        switch ($type) {
            case 'customers':
                foreach ($data as $item) {
                    $result = TailorPro_DB::insert_customer($item);
                    if (is_wp_error($result)) {
                        $errors[] = $result->get_error_message();
                    } else {
                        $imported++;
                    }
                }
                break;
            case 'orders':
                foreach ($data as $item) {
                    $result = TailorPro_DB::insert_order($item);
                    if (is_wp_error($result)) {
                        $errors[] = $result->get_error_message();
                    } else {
                        $imported++;
                    }
                }
                break;
        }
        
        $message = sprintf(__('%d items imported successfully.', 'tailorpro'), $imported);
        if (!empty($errors)) {
            $message .= ' ' . __('Some errors occurred:', 'tailorpro') . ' ' . implode(', ', $errors);
        }
        
        $this->send_response(true, array('imported' => $imported), $message);
    }
}

// Initialize AJAX handler
new TailorPro_Ajax();