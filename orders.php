<?php
/**
 * Orders List Page Template
 * 
 * View, filter, and manage all orders with status updates
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle filters
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$overdue_filter = isset($_GET['overdue']) ? absint($_GET['overdue']) : 0;

// Get statistics
$stats = TailorPro_DB::get_dashboard_stats();
?>

<div class="wrap">
    <div class="tailorpro-container">
        <!-- Page Header -->
        <div class="tailorpro-card-header">
            <div class="tailorpro-row">
                <div class="tailorpro-col tailorpro-col-8">
                    <h1 class="tailorpro-heading tailorpro-heading-1">
                        <span class="dashicons dashicons-list-view"></span>
                        <?php _e('Orders', 'tailorpro'); ?>
                        <span class="tailorpro-text-muted">(<?php echo TailorPro_DB::count_orders(); ?> <?php _e('total', 'tailorpro'); ?>)</span>
                    </h1>
                    <p class="tailorpro-text-muted">
                        <?php _e('Manage all tailoring orders, update status, and track progress', 'tailorpro'); ?>
                    </p>
                </div>
                <div class="tailorpro-col tailorpro-col-4 text-right">
                    <a href="<?php echo admin_url('admin.php?page=tailorpro-new-order'); ?>" 
                       class="tailorpro-btn tailorpro-btn-primary">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('New Order', 'tailorpro'); ?>
                    </a>
                    <button type="button" class="tailorpro-btn tailorpro-btn-info tailorpro-import" data-import-type="orders">
                        <span class="dashicons dashicons-upload"></span>
                        <?php _e('Import', 'tailorpro'); ?>
                    </button>
                    <button type="button" class="tailorpro-btn tailorpro-btn-success tailorpro-export" data-export-type="orders">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Export', 'tailorpro'); ?>
                    </button>
                    <button type="button" class="tailorpro-btn tailorpro-btn-warning tailorpro-print">
                        <span class="dashicons dashicons-printer"></span>
                        <?php _e('Print', 'tailorpro'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="tailorpro-row mb-4">
            <div class="tailorpro-col tailorpro-col-3">
                <div class="tailorpro-stat-card">
                    <div class="tailorpro-stat-number"><?php echo $stats['pending_orders']; ?></div>
                    <div class="tailorpro-stat-label">
                        <span class="tailorpro-badge tailorpro-badge-warning"><?php _e('Pending', 'tailorpro'); ?></span>
                    </div>
                </div>
            </div>
            <div class="tailorpro-col tailorpro-col-3">
                <div class="tailorpro-stat-card">
                    <div class="tailorpro-stat-number"><?php echo $stats['in_progress_orders']; ?></div>
                    <div class="tailorpro-stat-label">
                        <span class="tailorpro-badge tailorpro-badge-info"><?php _e('In Progress', 'tailorpro'); ?></span>
                    </div>
                </div>
            </div>
            <div class="tailorpro-col tailorpro-col-3">
                <div class="tailorpro-stat-card">
                    <div class="tailorpro-stat-number"><?php echo $stats['overdue_orders']; ?></div>
                    <div class="tailorpro-stat-label">
                        <span class="tailorpro-badge tailorpro-badge-danger"><?php _e('Overdue', 'tailorpro'); ?></span>
                    </div>
                </div>
            </div>
            <div class="tailorpro-col tailorpro-col-3">
                <div class="tailorpro-stat-card">
                    <div class="tailorpro-stat-number">$<?php echo number_format($stats['outstanding_amount'] ?? 0, 2); ?></div>
                    <div class="tailorpro-stat-label">
                        <span class="tailorpro-badge tailorpro-badge-primary"><?php _e('Outstanding', 'tailorpro'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="tailorpro-card-body tailorpro-pb-2">
            <div class="tailorpro-row">
                <div class="tailorpro-col tailorpro-col-3">
                    <div class="tailorpro-input-group">
                        <input type="text" class="tailorpro-form-control tailorpro-search" 
                               placeholder="<?php _e('Search orders...', 'tailorpro'); ?>" 
                               data-search-target="orders">
                        <div class="tailorpro-input-group-text">
                            <span class="dashicons dashicons-search"></span>
                        </div>
                    </div>
                </div>
                <div class="tailorpro-col tailorpro-col-2">
                    <select class="tailorpro-form-control order-status-filter" id="status-filter">
                        <option value=""><?php _e('All Statuses', 'tailorpro'); ?></option>
                        <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php _e('Pending', 'tailorpro'); ?></option>
                        <option value="confirmed" <?php selected($status_filter, 'confirmed'); ?>><?php _e('Confirmed', 'tailorpro'); ?></option>
                        <option value="in_progress" <?php selected($status_filter, 'in_progress'); ?>><?php _e('In Progress', 'tailorpro'); ?></option>
                        <option value="ready" <?php selected($status_filter, 'ready'); ?>><?php _e('Ready', 'tailorpro'); ?></option>
                        <option value="completed" <?php selected($status_filter, 'completed'); ?>><?php _e('Completed', 'tailorpro'); ?></option>
                        <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>><?php _e('Cancelled', 'tailorpro'); ?></option>
                    </select>
                </div>
                <div class="tailorpro-col tailorpro-col-2">
                    <select class="tailorpro-form-control" id="priority-filter">
                        <option value=""><?php _e('All Priorities', 'tailorpro'); ?></option>
                        <option value="low"><?php _e('Low', 'tailorpro'); ?></option>
                        <option value="normal"><?php _e('Normal', 'tailorpro'); ?></option>
                        <option value="high"><?php _e('High', 'tailorpro'); ?></option>
                        <option value="urgent"><?php _e('Urgent', 'tailorpro'); ?></option>
                    </select>
                </div>
                <div class="tailorpro-col tailorpro-col-2">
                    <select class="tailorpro-form-control" id="date-filter">
                        <option value=""><?php _e('All Dates', 'tailorpro'); ?></option>
                        <option value="today"><?php _e('Today', 'tailorpro'); ?></option>
                        <option value="week"><?php _e('This Week', 'tailorpro'); ?></option>
                        <option value="month"><?php _e('This Month', 'tailorpro'); ?></option>
                        <option value="overdue" <?php selected($overdue_filter, 1); ?>><?php _e('Overdue', 'tailorpro'); ?></option>
                    </select>
                </div>
                <div class="tailorpro-col tailorpro-col-2">
                    <select class="tailorpro-form-control" id="sort-filter">
                        <option value="order_date_desc"><?php _e('Newest First', 'tailorpro'); ?></option>
                        <option value="order_date_asc"><?php _e('Oldest First', 'tailorpro'); ?></option>
                        <option value="due_date_asc"><?php _e('Due Date (Soonest)', 'tailorpro'); ?></option>
                        <option value="due_date_desc"><?php _e('Due Date (Latest)', 'tailorpro'); ?></option>
                        <option value="customer_name_asc"><?php _e('Customer (A-Z)', 'tailorpro'); ?></option>
                        <option value="total_amount_desc"><?php _e('Amount (High-Low)', 'tailorpro'); ?></option>
                    </select>
                </div>
                <div class="tailorpro-col tailorpro-col-1">
                    <button type="button" class="tailorpro-btn tailorpro-btn-secondary" id="clear-filters" title="<?php _e('Clear Filters', 'tailorpro'); ?>">
                        <span class="dashicons dashicons-dismiss"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="tailorpro-card-body">
            <div class="tailorpro-table-responsive">
                <table class="tailorpro-table tailorpro-table-striped tailorpro-table-hover tailorpro-responsive-table">
                    <thead>
                        <tr>
                            <th width="5%">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th width="12%"><?php _e('Order #', 'tailorpro'); ?></th>
                            <th width="18%"><?php _e('Customer', 'tailorpro'); ?></th>
                            <th width="10%"><?php _e('Order Date', 'tailorpro'); ?></th>
                            <th width="10%"><?php _e('Due Date', 'tailorpro'); ?></th>
                            <th width="8%"><?php _e('Amount', 'tailorpro'); ?></th>
                            <th width="8%"><?php _e('Paid', 'tailorpro'); ?></th>
                            <th width="10%"><?php _e('Status', 'tailorpro'); ?></th>
                            <th width="8%"><?php _e('Priority', 'tailorpro'); ?></th>
                            <th width="11%"><?php _e('Actions', 'tailorpro'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="orders-table-body">
                        <!-- Order rows will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="tailorpro-mt-3">
                <div class="tailorpro-row">
                    <div class="tailorpro-col tailorpro-col-6">
                        <div class="tailorpro-form-text">
                            <?php _e('Showing', 'tailorpro'); ?> <span id="orders-count">0</span> <?php _e('orders', 'tailorpro'); ?>
                        </div>
                    </div>
                    <div class="tailorpro-col tailorpro-col-6 text-right">
                        <div class="tailorpro-btn-group">
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm" id="prev-page">
                                <span class="dashicons dashicons-arrow-left-alt2"></span>
                                <?php _e('Previous', 'tailorpro'); ?>
                            </button>
                            <span class="tailorpro-btn tailorpro-btn-sm" id="current-page">1</span>
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm" id="next-page">
                                <?php _e('Next', 'tailorpro'); ?>
                                <span class="dashicons dashicons-arrow-right-alt2"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Order Modal -->
<div class="tailorpro-modal" id="edit-order-modal">
    <div class="tailorpro-modal-dialog">
        <div class="tailorpro-modal-content">
            <div class="tailorpro-modal-header">
                <h5 class="tailorpro-modal-title">
                    <span class="dashicons dashicons-edit"></span>
                    <?php _e('Edit Order', 'tailorpro'); ?>
                </h5>
                <button type="button" class="tailorpro-modal-close">&times;</button>
            </div>
            <form id="edit-order-form">
                <input type="hidden" id="edit-order-id" name="order_id">
                <div class="tailorpro-modal-body">
                    <!-- Customer Information -->
                    <div class="tailorpro-form-group">
                        <label class="tailorpro-form-label" for="edit-customer-select">
                            <?php _e('Customer', 'tailorpro'); ?> <span class="text-danger">*</span>
                        </label>
                        <select class="tailorpro-form-control" id="edit-customer-select" name="customer_id" required>
                            <option value=""><?php _e('Choose a customer...', 'tailorpro'); ?></option>
                            <?php 
                            $customers = TailorPro_DB::get_customers(0, 0, '');
                            foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer->id; ?>">
                                    <?php echo esc_html($customer->name . ' (' . $customer->phone . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Order Details -->
                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-order-number">
                                    <?php _e('Order Number', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="tailorpro-form-control" id="edit-order-number" 
                                       name="order_number" required>
                            </div>
                        </div>
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-status">
                                    <?php _e('Status', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <select class="tailorpro-form-control" id="edit-status" name="status" required>
                                    <option value="pending"><?php _e('Pending', 'tailorpro'); ?></option>
                                    <option value="confirmed"><?php _e('Confirmed', 'tailorpro'); ?></option>
                                    <option value="in_progress"><?php _e('In Progress', 'tailorpro'); ?></option>
                                    <option value="ready"><?php _e('Ready for Pickup', 'tailorpro'); ?></option>
                                    <option value="completed"><?php _e('Completed', 'tailorpro'); ?></option>
                                    <option value="cancelled"><?php _e('Cancelled', 'tailorpro'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-4">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-order-date">
                                    <?php _e('Order Date', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="tailorpro-form-control" id="edit-order-date" 
                                       name="order_date" required>
                            </div>
                        </div>
                        <div class="tailorpro-col tailorpro-col-4">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-due-date">
                                    <?php _e('Due Date', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="tailorpro-form-control" id="edit-due-date" 
                                       name="due_date" required>
                            </div>
                        </div>
                        <div class="tailorpro-col tailorpro-col-4">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-delivery-date">
                                    <?php _e('Delivery Date', 'tailorpro'); ?>
                                </label>
                                <input type="date" class="tailorpro-form-control" id="edit-delivery-date" 
                                       name="delivery_date">
                            </div>
                        </div>
                    </div>

                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-4">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-total-amount">
                                    <?php _e('Total Amount', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <div class="tailorpro-input-group">
                                    <div class="tailorpro-input-group-text">$</div>
                                    <input type="number" class="tailorpro-form-control" id="edit-total-amount" 
                                           name="total_amount" required step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="tailorpro-col tailorpro-col-4">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-paid-amount">
                                    <?php _e('Paid Amount', 'tailorpro'); ?>
                                </label>
                                <div class="tailorpro-input-group">
                                    <div class="tailorpro-input-group-text">$</div>
                                    <input type="number" class="tailorpro-form-control" id="edit-paid-amount" 
                                           name="paid_amount" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="tailorpro-col tailorpro-col-4">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-priority">
                                    <?php _e('Priority', 'tailorpro'); ?>
                                </label>
                                <select class="tailorpro-form-control" id="edit-priority" name="priority">
                                    <option value="low"><?php _e('Low', 'tailorpro'); ?></option>
                                    <option value="normal"><?php _e('Normal', 'tailorpro'); ?></option>
                                    <option value="high"><?php _e('High', 'tailorpro'); ?></option>
                                    <option value="urgent"><?php _e('Urgent', 'tailorpro'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="tailorpro-form-group">
                        <label class="tailorpro-form-label" for="edit-order-notes">
                            <?php _e('Order Notes', 'tailorpro'); ?>
                        </label>
                        <textarea class="tailorpro-form-control" id="edit-order-notes" name="notes" rows="3"
                                  placeholder="<?php _e('Additional notes about this order', 'tailorpro'); ?>"></textarea>
                    </div>
                </div>
                <div class="tailorpro-modal-footer">
                    <button type="button" class="tailorpro-btn tailorpro-btn-secondary" data-dismiss="modal">
                        <?php _e('Cancel', 'tailorpro'); ?>
                    </button>
                    <button type="button" class="tailorpro-btn tailorpro-btn-info" id="view-measurements">
                        <span class="dashicons dashicons-admin-tools"></span>
                        <?php _e('View Measurements', 'tailorpro'); ?>
                    </button>
                    <button type="submit" class="tailorpro-btn tailorpro-btn-primary">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Update Order', 'tailorpro'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="tailorpro-modal" id="order-details-modal">
    <div class="tailorpro-modal-dialog">
        <div class="tailorpro-modal-content">
            <div class="tailorpro-modal-header">
                <h5 class="tailorpro-modal-title">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('Order Details', 'tailorpro'); ?>
                </h5>
                <button type="button" class="tailorpro-modal-close">&times;</button>
            </div>
            <div class="tailorpro-modal-body" id="order-details-content">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="tailorpro-modal-footer">
                <button type="button" class="tailorpro-btn tailorpro-btn-secondary" data-dismiss="modal">
                    <?php _e('Close', 'tailorpro'); ?>
                </button>
                <button type="button" class="tailorpro-btn tailorpro-btn-info" id="print-order">
                    <span class="dashicons dashicons-printer"></span>
                    <?php _e('Print Order', 'tailorpro'); ?>
                </button>
                <button type="button" class="tailorpro-btn tailorpro-btn-success" id="download-invoice">
                    <span class="dashicons dashicons-download"></span>
                    <?php _e('Download Invoice', 'tailorpro'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Order Confirmation Modal -->
<div class="tailorpro-modal" id="delete-order-modal">
    <div class="tailorpro-modal-dialog">
        <div class="tailorpro-modal-content">
            <div class="tailorpro-modal-header">
                <h5 class="tailorpro-modal-title">
                    <span class="dashicons dashicons-warning"></span>
                    <?php _e('Delete Order', 'tailorpro'); ?>
                </h5>
                <button type="button" class="tailorpro-modal-close">&times;</button>
            </div>
            <div class="tailorpro-modal-body">
                <p><?php _e('Are you sure you want to delete this order? This action cannot be undone.', 'tailorpro'); ?></p>
                <div id="order-to-delete" class="tailorpro-alert tailorpro-alert-warning"></div>
            </div>
            <div class="tailorpro-modal-footer">
                <button type="button" class="tailorpro-btn tailorpro-btn-secondary" data-dismiss="modal">
                    <?php _e('Cancel', 'tailorpro'); ?>
                </button>
                <button type="button" class="tailorpro-btn tailorpro-btn-danger" id="confirm-delete-order">
                    <span class="dashicons dashicons-trash"></span>
                    <?php _e('Delete Order', 'tailorpro'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentPage = 1;
    let totalOrders = 0;
    let ordersPerPage = 20;
    let deleteOrderId = null;
    let currentOrderId = null;

    // Load orders on page load
    loadOrders();

    // Search functionality
    $('.tailorpro-search').on('input', function() {
        const query = $(this).val();
        if (query.length >= 2 || query.length === 0) {
            currentPage = 1;
            loadOrders();
        }
    });

    // Filter functionality
    $('#status-filter, #priority-filter, #date-filter, #sort-filter').on('change', function() {
        currentPage = 1;
        loadOrders();
    });

    $('#clear-filters').on('click', function() {
        $('.tailorpro-search').val('');
        $('#status-filter, #priority-filter, #date-filter, #sort-filter').val('');
        currentPage = 1;
        loadOrders();
    });

    // Pagination
    $('#prev-page').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadOrders();
        }
    });

    $('#next-page').on('click', function() {
        const totalPages = Math.ceil(totalOrders / ordersPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            loadOrders();
        }
    });

    // Edit order
    $(document).on('click', '[data-edit-order]', function() {
        const orderId = $(this).data('edit-order');
        editOrder(orderId);
    });

    // View order details
    $(document).on('click', '[data-view-order]', function() {
        const orderId = $(this).data('view-order');
        viewOrderDetails(orderId);
    });

    // Delete order
    $(document).on('click', '[data-delete-order]', function() {
        deleteOrderId = $(this).data('delete-order');
        showDeleteConfirmation(deleteOrderId);
    });

    // Update order status
    $(document).on('change', '.status-select', function() {
        const orderId = $(this).data('order-id');
        const status = $(this).val();
        updateOrderStatus(orderId, status);
    });

    // Handle edit order form submission
    $('#edit-order-form').on('submit', function(e) {
        e.preventDefault();
        updateOrder();
    });

    // View measurements
    $('#view-measurements').on('click', function() {
        if (currentOrderId) {
            viewOrderMeasurements(currentOrderId);
        }
    });

    // Print order
    $('#print-order').on('click', function() {
        if (currentOrderId) {
            printOrder(currentOrderId);
        }
    });

    function loadOrders() {
        const search = $('.tailorpro-search').val() || '';
        const status = $('#status-filter').val() || '';
        const priority = $('#priority-filter').val() || '';
        const dateFilter = $('#date-filter').val() || '';
        const sortBy = $('#sort-filter').val() || 'order_date_desc';

        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_get_orders',
            search: search,
            status: status,
            priority: priority,
            date_filter: dateFilter,
            sort: sortBy,
            limit: ordersPerPage,
            offset: (currentPage - 1) * ordersPerPage
        }, function(response) {
            if (response.success) {
                renderOrders(response.data.orders);
                totalOrders = response.data.total;
                updatePagination();
            }
        });
    }

    function renderOrders(orders) {
        const tbody = $('#orders-table-body');
        tbody.empty();

        if (orders.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="10" class="text-center tailorpro-text-muted">
                        <span class="dashicons dashicons-info"></span>
                        <?php _e('No orders found', 'tailorpro'); ?>
                    </td>
                </tr>
            `);
            return;
        }

        orders.forEach(function(order) {
            const statusClass = `tailorpro-badge-${order.status.replace('_', '-')}`;
            const isOverdue = new Date(order.due_date) < new Date() && !['completed', 'cancelled'].includes(order.status);
            const priorityClass = `tailorpro-badge-${order.priority}`;
            
            const outstanding = order.total_amount - order.paid_amount;
            const outstandingClass = outstanding > 0 ? 'text-danger' : 'text-success';

            const row = `
                <tr data-id="${order.id}" class="${isOverdue ? 'overdue' : ''}">
                    <td>
                        <input type="checkbox" value="${order.id}" class="order-checkbox">
                    </td>
                    <td>
                        <strong>${order.order_number}</strong>
                        <br><small class="tailorpro-text-muted">${new Date(order.order_date).toLocaleDateString()}</small>
                    </td>
                    <td>
                        <strong>${order.customer_name}</strong>
                        <br><small class="tailorpro-text-muted">${order.customer_phone}</small>
                    </td>
                    <td>${formatDate(order.order_date)}</td>
                    <td>
                        ${formatDate(order.due_date)}
                        ${isOverdue ? '<br><span class="tailorpro-badge tailorpro-badge-danger tailorpro-badge-sm">Overdue</span>' : ''}
                    </td>
                    <td>$${parseFloat(order.total_amount).toFixed(2)}</td>
                    <td class="${outstandingClass}">
                        $${parseFloat(order.paid_amount).toFixed(2)}
                        ${outstanding > 0 ? `<br><small>$${outstanding.toFixed(2)} due</small>` : '<br><small>Paid</small>'}
                    </td>
                    <td>
                        <select class="tailorpro-form-control tailorpro-form-control-sm status-select" 
                                data-order-id="${order.id}">
                            <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="confirmed" ${order.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                            <option value="in_progress" ${order.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                            <option value="ready" ${order.status === 'ready' ? 'selected' : ''}>Ready</option>
                            <option value="completed" ${order.status === 'completed' ? 'selected' : ''}>Completed</option>
                            <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                        </select>
                    </td>
                    <td>
                        <span class="tailorpro-badge ${priorityClass}">${order.priority.toUpperCase()}</span>
                    </td>
                    <td class="no-print">
                        <div class="tailorpro-btn-group">
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-info" 
                                    data-view-order="${order.id}" 
                                    data-tooltip="<?php _e('View Details', 'tailorpro'); ?>">
                                <span class="dashicons dashicons-visibility"></span>
                            </button>
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-warning" 
                                    data-edit-order="${order.id}" 
                                    data-tooltip="<?php _e('Edit Order', 'tailorpro'); ?>">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-danger" 
                                    data-delete-order="${order.id}" 
                                    data-confirm="<?php _e('Are you sure you want to delete this order?', 'tailorpro'); ?>"
                                    data-tooltip="<?php _e('Delete Order', 'tailorpro'); ?>">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        $('#orders-count').text(orders.length);
    }

    function updatePagination() {
        const totalPages = Math.ceil(totalOrders / ordersPerPage);
        $('#current-page').text(currentPage);
        
        $('#prev-page').prop('disabled', currentPage <= 1);
        $('#next-page').prop('disabled', currentPage >= totalPages);
    }

    function editOrder(orderId) {
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_get_order',
            order_id: orderId
        }, function(response) {
            if (response.success) {
                const order = response.data;
                currentOrderId = order.id;
                
                $('#edit-order-id').val(order.id);
                $('#edit-customer-select').val(order.customer_id);
                $('#edit-order-number').val(order.order_number);
                $('#edit-status').val(order.status);
                $('#edit-order-date').val(order.order_date);
                $('#edit-due-date').val(order.due_date);
                $('#edit-delivery-date').val(order.delivery_date || '');
                $('#edit-total-amount').val(order.total_amount);
                $('#edit-paid-amount').val(order.paid_amount);
                $('#edit-priority').val(order.priority);
                $('#edit-order-notes').val(order.notes || '');
                
                $('#edit-order-modal').addClass('show');
            } else {
                showAlert('error', response.message);
            }
        });
    }

    function viewOrderDetails(orderId) {
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_get_order',
            order_id: orderId
        }, function(response) {
            if (response.success) {
                currentOrderId = orderId;
                const order = response.data;
                
                const detailsHtml = `
                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-6">
                            <h4><?php _e('Order Information', 'tailorpro'); ?></h4>
                            <table class="tailorpro-table tailorpro-table-sm">
                                <tr><td><?php _e('Order Number:', 'tailorpro'); ?></td><td><strong>${order.order_number}</strong></td></tr>
                                <tr><td><?php _e('Order Date:', 'tailorpro'); ?></td><td>${formatDate(order.order_date)}</td></tr>
                                <tr><td><?php _e('Due Date:', 'tailorpro'); ?></td><td>${formatDate(order.due_date)}</td></tr>
                                <tr><td><?php _e('Delivery Date:', 'tailorpro'); ?></td><td>${order.delivery_date ? formatDate(order.delivery_date) : '-'}</td></tr>
                                <tr><td><?php _e('Status:', 'tailorpro'); ?></td><td><span class="tailorpro-badge tailorpro-badge-${order.status.replace('_', '-')}">${order.status.replace('_', ' ').toUpperCase()}</span></td></tr>
                                <tr><td><?php _e('Priority:', 'tailorpro'); ?></td><td><span class="tailorpro-badge tailorpro-badge-${order.priority}">${order.priority.toUpperCase()}</span></td></tr>
                            </table>
                        </div>
                        <div class="tailorpro-col tailorpro-col-6">
                            <h4><?php _e('Customer Information', 'tailorpro'); ?></h4>
                            <table class="tailorpro-table tailorpro-table-sm">
                                <tr><td><?php _e('Name:', 'tailorpro'); ?></td><td><strong>${order.customer_name}</strong></td></tr>
                                <tr><td><?php _e('Phone:', 'tailorpro'); ?></td><td>${order.customer_phone}</td></tr>
                                <tr><td><?php _e('Total Amount:', 'tailorpro'); ?></td><td>$${parseFloat(order.total_amount).toFixed(2)}</td></tr>
                                <tr><td><?php _e('Paid Amount:', 'tailorpro'); ?></td><td>$${parseFloat(order.paid_amount).toFixed(2)}</td></tr>
                                <tr><td><?php _e('Outstanding:', 'tailorpro'); ?></td><td>$${(order.total_amount - order.paid_amount).toFixed(2)}</td></tr>
                            </table>
                        </div>
                    </div>
                    ${order.notes ? `
                        <div class="tailorpro-mt-3">
                            <h4><?php _e('Notes', 'tailorpro'); ?></h4>
                            <div class="tailorpro-card">
                                <div class="tailorpro-card-body">
                                    ${order.notes}
                                </div>
                            </div>
                        </div>
                    ` : ''}
                `;
                
                $('#order-details-content').html(detailsHtml);
                $('#order-details-modal').addClass('show');
            } else {
                showAlert('error', response.message);
            }
        });
    }

    function showDeleteConfirmation(orderId) {
        $('#order-to-delete').html(`<?php _e('Order ID:', 'tailorpro'); ?> ${orderId}`);
        $('#delete-order-modal').addClass('show');
    }

    $('#confirm-delete-order').on('click', function() {
        if (deleteOrderId) {
            deleteOrder(deleteOrderId);
        }
    });

    function updateOrderStatus(orderId, status) {
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_update_order_status',
            order_id: orderId,
            status: status
        }, function(response) {
            if (response.success) {
                showAlert('success', '<?php _e('Order status updated successfully', 'tailorpro'); ?>');
            } else {
                showAlert('error', response.message);
            }
        });
    }

    function updateOrder() {
        const formData = $('#edit-order-form').serialize();
        formData += '&action=tailorpro_edit_order';

        $.post(tailorpro_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                showAlert('success', response.message);
                $('#edit-order-modal').removeClass('show');
                loadOrders();
            } else {
                showAlert('error', response.message);
            }
        });
    }

    function deleteOrder(orderId) {
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_delete_order',
            order_id: orderId
        }, function(response) {
            if (response.success) {
                showAlert('success', response.message);
                $('#delete-order-modal').removeClass('show');
                loadOrders();
            } else {
                showAlert('error', response.message);
            }
        });
    }

    function viewOrderMeasurements(orderId) {
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_get_measurements',
            order_id: orderId
        }, function(response) {
            if (response.success) {
                let measurementsHtml = '<h4><?php _e('Measurements', 'tailorpro'); ?></h4>';
                
                if (response.data.length === 0) {
                    measurementsHtml += '<p class="tailorpro-text-muted"><?php _e('No measurements found for this order.', 'tailorpro'); ?></p>';
                } else {
                    response.data.forEach(function(measurement, index) {
                        measurementsHtml += `
                            <div class="tailorpro-card mb-3">
                                <div class="tailorpro-card-header">
                                    <h5><?php _e('Item', 'tailorpro'); ?> ${index + 1}: ${measurement.item_type} - ${measurement.item_name || 'N/A'}</h5>
                                </div>
                                <div class="tailorpro-card-body">
                                    <div class="tailorpro-row">
                        `;
                        
                        Object.keys(measurement.measurements).forEach(function(key) {
                            measurementsHtml += `
                                <div class="tailorpro-col tailorpro-col-4">
                                    <strong>${key.charAt(0).toUpperCase() + key.slice(1)}:</strong> ${measurement.measurements[key]}
                                </div>
                            `;
                        });
                        
                        measurementsHtml += `
                                    </div>
                                    ${measurement.notes ? `
                                        <div class="tailorpro-mt-2">
                                            <strong><?php _e('Notes:', 'tailorpro'); ?></strong> ${measurement.notes}
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    });
                }
                
                // Update the modal content
                $('.tailorpro-modal-body').first().html(measurementsHtml);
            }
        });
    }

    function printOrder(orderId) {
        // Implementation for printing order
        window.print();
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString();
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'tailorpro-alert-success' : 'tailorpro-alert-danger';
        const alert = $(`
            <div class="${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);
        
        $('.tailorpro-container').prepend(alert);
        
        setTimeout(() => {
            alert.fadeOut();
        }, 5000);
    }
});
</script>

<style>
/* Orders page specific styles */
.tailorpro-stat-card {
    transition: transform 0.2s ease;
    cursor: pointer;
}

.tailorpro-stat-card:hover {
    transform: translateY(-2px);
}

.tailorpro-table tbody tr.overdue {
    background-color: rgba(220, 53, 69, 0.1);
    border-left: 4px solid var(--tailorpro-danger);
}

.tailorpro-table tbody tr.overdue:hover {
    background-color: rgba(220, 53, 69, 0.2);
}

.status-select {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}

.order-checkbox {
    margin: 0;
}

/* Badge sizing */
.tailorpro-badge-sm {
    font-size: 0.75rem;
    padding: 0.125rem 0.375rem;
}

/* Modal adjustments */
.tailorpro-modal-dialog {
    max-width: 800px;
    margin: 3rem auto;
}

#order-details-modal .tailorpro-modal-dialog {
    max-width: 900px;
}

/* Table responsiveness */
@media (max-width: 768px) {
    .tailorpro-responsive-table {
        font-size: 14px;
    }
    
    .tailorpro-table th,
    .tailorpro-table td {
        padding: 8px 4px;
    }
    
    .tailorpro-btn-group {
        flex-direction: column;
    }
    
    .tailorpro-btn-group .tailorpro-btn {
        margin-bottom: 2px;
        border-radius: 4px;
        padding: 4px 8px;
    }
    
    .tailorpro-row .tailorpro-col {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .tailorpro-stat-card {
        margin-bottom: 1rem;
    }
}

/* Print styles for order details */
@media print {
    .no-print {
        display: none !important;
    }
    
    .tailorpro-modal {
        position: static !important;
        display: block !important;
    }
    
    .tailorpro-modal-content {
        box-shadow: none !important;
        border: 1px solid #000 !important;
    }
}
</style>