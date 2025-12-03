<?php
/**
 * Dashboard Page Template
 * 
 * Main dashboard showing business overview, statistics, and recent activity
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get dashboard statistics
$stats = TailorPro_DB::get_dashboard_stats();
?>

<div class="wrap">
    <h1 class="tailorpro-heading tailorpro-heading-1">
        <span class="dashicons dashicons-admin-generic"></span>
        <?php _e('Tailor Pro Dashboard', 'tailorpro'); ?>
    </h1>
    
    <!-- Quick Actions -->
    <div class="tailorpro-card mb-4">
        <div class="tailorpro-card-header">
            <h3 class="tailorpro-heading tailorpro-heading-3">
                <span class="dashicons dashicons-admin-tools"></span>
                <?php _e('Quick Actions', 'tailorpro'); ?>
            </h3>
        </div>
        <div class="tailorpro-card-body">
            <div class="tailorpro-row">
                <div class="tailorpro-col tailorpro-col-3">
                    <a href="<?php echo admin_url('admin.php?page=tailorpro-new-order'); ?>" 
                       class="tailorpro-btn tailorpro-btn-primary tailorpro-btn-block">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('New Order', 'tailorpro'); ?>
                    </a>
                </div>
                <div class="tailorpro-col tailorpro-col-3">
                    <a href="<?php echo admin_url('admin.php?page=tailorpro-customers'); ?>" 
                       class="tailorpro-btn tailorpro-btn-info tailorpro-btn-block">
                        <span class="dashicons dashicons-groups"></span>
                        <?php _e('Add Customer', 'tailorpro'); ?>
                    </a>
                </div>
                <div class="tailorpro-col tailorpro-col-3">
                    <button type="button" class="tailorpro-btn tailorpro-btn-success tailorpro-btn-block tailorpro-export" 
                            data-export-type="orders">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Export Orders', 'tailorpro'); ?>
                    </button>
                </div>
                <div class="tailorpro-col tailorpro-col-3">
                    <button type="button" class="tailorpro-btn tailorpro-btn-warning tailorpro-btn-block tailorpro-print">
                        <span class="dashicons dashicons-printer"></span>
                        <?php _e('Print Report', 'tailorpro'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="tailorpro-row mb-4">
        <!-- Total Customers -->
        <div class="tailorpro-col tailorpro-col-3">
            <div class="tailorpro-stat-card">
                <div class="tailorpro-stat-number" data-stat="total_customers">
                    <?php echo number_format($stats['total_customers']); ?>
                </div>
                <div class="tailorpro-stat-label">
                    <span class="dashicons dashicons-groups"></span>
                    <?php _e('Total Customers', 'tailorpro'); ?>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="tailorpro-col tailorpro-col-3">
            <div class="tailorpro-stat-card">
                <div class="tailorpro-stat-number" data-stat="total_orders">
                    <?php echo number_format($stats['total_orders']); ?>
                </div>
                <div class="tailorpro-stat-label">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('Total Orders', 'tailorpro'); ?>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="tailorpro-col tailorpro-col-3">
            <div class="tailorpro-stat-card">
                <div class="tailorpro-stat-number" data-stat="total_revenue">
                    $<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?>
                </div>
                <div class="tailorpro-stat-label">
                    <span class="dashicons dashicons-money-alt"></span>
                    <?php _e('Total Revenue', 'tailorpro'); ?>
                </div>
            </div>
        </div>

        <!-- Outstanding Amount -->
        <div class="tailorpro-col tailorpro-col-3">
            <div class="tailorpro-stat-card">
                <div class="tailorpro-stat-number" data-stat="outstanding_amount">
                    $<?php echo number_format($stats['outstanding_amount'] ?? 0, 2); ?>
                </div>
                <div class="tailorpro-stat-label">
                    <span class="dashicons dashicons-clock"></span>
                    <?php _e('Outstanding', 'tailorpro'); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="tailorpro-row mb-4">
        <!-- Pending Orders -->
        <div class="tailorpro-col tailorpro-col-4">
            <div class="tailorpro-card">
                <div class="tailorpro-card-header">
                    <h4 class="tailorpro-heading tailorpro-heading-4">
                        <span class="tailorpro-badge tailorpro-badge-warning"><?php echo $stats['pending_orders']; ?></span>
                        <?php _e('Pending Orders', 'tailorpro'); ?>
                    </h4>
                </div>
                <div class="tailorpro-card-body">
                    <p class="tailorpro-text-muted">
                        <?php _e('Orders awaiting processing', 'tailorpro'); ?>
                    </p>
                    <a href="<?php echo admin_url('admin.php?page=tailorpro-orders&status=pending'); ?>" 
                       class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-outline-primary">
                        <?php _e('View All', 'tailorpro'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- In Progress Orders -->
        <div class="tailorpro-col tailorpro-col-4">
            <div class="tailorpro-card">
                <div class="tailorpro-card-header">
                    <h4 class="tailorpro-heading tailorpro-heading-4">
                        <span class="tailorpro-badge tailorpro-badge-info"><?php echo $stats['in_progress_orders']; ?></span>
                        <?php _e('In Progress', 'tailorpro'); ?>
                    </h4>
                </div>
                <div class="tailorpro-card-body">
                    <p class="tailorpro-text-muted">
                        <?php _e('Orders currently being worked on', 'tailorpro'); ?>
                    </p>
                    <a href="<?php echo admin_url('admin.php?page=tailorpro-orders&status=in_progress'); ?>" 
                       class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-outline-primary">
                        <?php _e('View All', 'tailorpro'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Overdue Orders -->
        <div class="tailorpro-col tailorpro-col-4">
            <div class="tailorpro-card">
                <div class="tailorpro-card-header">
                    <h4 class="tailorpro-heading tailorpro-heading-4">
                        <span class="tailorpro-badge tailorpro-badge-danger"><?php echo $stats['overdue_orders']; ?></span>
                        <?php _e('Overdue Orders', 'tailorpro'); ?>
                    </h4>
                </div>
                <div class="tailorpro-card-body">
                    <p class="tailorpro-text-muted">
                        <?php _e('Orders past their due date', 'tailorpro'); ?>
                    </p>
                    <a href="<?php echo admin_url('admin.php?page=tailorpro-orders&overdue=1'); ?>" 
                       class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-outline-primary">
                        <?php _e('View All', 'tailorpro'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="tailorpro-row mb-4">
        <!-- Orders Chart -->
        <div class="tailorpro-col tailorpro-col-8">
            <div class="tailorpro-card">
                <div class="tailorpro-card-header">
                    <h3 class="tailorpro-heading tailorpro-heading-3">
                        <span class="dashicons dashicons-chart-area"></span>
                        <?php _e('Orders Overview', 'tailorpro'); ?>
                    </h3>
                </div>
                <div class="tailorpro-card-body">
                    <canvas id="orders-chart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="tailorpro-col tailorpro-col-4">
            <div class="tailorpro-card">
                <div class="tailorpro-card-header">
                    <h3 class="tailorpro-heading tailorpro-heading-3">
                        <span class="dashicons dashicons-chart-pie"></span>
                        <?php _e('Revenue Distribution', 'tailorpro'); ?>
                    </h3>
                </div>
                <div class="tailorpro-card-body">
                    <canvas id="revenue-chart" width="200" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="tailorpro-row">
        <!-- Recent Orders -->
        <div class="tailorpro-col tailorpro-col-6">
            <div class="tailorpro-card">
                <div class="tailorpro-card-header">
                    <h3 class="tailorpro-heading tailorpro-heading-3">
                        <span class="dashicons dashicons-clock"></span>
                        <?php _e('Recent Orders', 'tailorpro'); ?>
                    </h3>
                </div>
                <div class="tailorpro-card-body">
                    <?php
                    // Get recent orders
                    $recent_orders = TailorPro_DB::get_orders(5, 0, '', '');
                    
                    if (empty($recent_orders)): ?>
                        <p class="tailorpro-text-muted"><?php _e('No recent orders', 'tailorpro'); ?></p>
                    <?php else: ?>
                        <div class="tailorpro-table-responsive">
                            <table class="tailorpro-table tailorpro-table-sm">
                                <thead>
                                    <tr>
                                        <th><?php _e('Order #', 'tailorpro'); ?></th>
                                        <th><?php _e('Customer', 'tailorpro'); ?></th>
                                        <th><?php _e('Status', 'tailorpro'); ?></th>
                                        <th><?php _e('Due Date', 'tailorpro'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo admin_url('admin.php?page=tailorpro-orders&edit=' . $order->id); ?>">
                                                    <?php echo esc_html($order->order_number); ?>
                                                </a>
                                            </td>
                                            <td><?php echo esc_html($order->customer_name); ?></td>
                                            <td>
                                                <span class="tailorpro-badge tailorpro-badge-<?php echo str_replace('_', '-', $order->status); ?>">
                                                    <?php echo esc_html(ucfirst(str_replace('_', ' ', $order->status))); ?>
                                                </span>
                                            </td>
                                            <td><?php echo esc_html(date('M j, Y', strtotime($order->due_date))); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tailorpro-mt-3">
                            <a href="<?php echo admin_url('admin.php?page=tailorpro-orders'); ?>" 
                               class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-outline-primary">
                                <?php _e('View All Orders', 'tailorpro'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Customers -->
        <div class="tailorpro-col tailorpro-col-6">
            <div class="tailorpro-card">
                <div class="tailorpro-card-header">
                    <h3 class="tailorpro-heading tailorpro-heading-3">
                        <span class="dashicons dashicons-groups"></span>
                        <?php _e('Recent Customers', 'tailorpro'); ?>
                    </h3>
                </div>
                <div class="tailorpro-card-body">
                    <?php
                    // Get recent customers
                    $recent_customers = TailorPro_DB::get_customers(5, 0, '');
                    
                    if (empty($recent_customers)): ?>
                        <p class="tailorpro-text-muted"><?php _e('No recent customers', 'tailorpro'); ?></p>
                    <?php else: ?>
                        <div class="tailorpro-table-responsive">
                            <table class="tailorpro-table tailorpro-table-sm">
                                <thead>
                                    <tr>
                                        <th><?php _e('Name', 'tailorpro'); ?></th>
                                        <th><?php _e('Phone', 'tailorpro'); ?></th>
                                        <th><?php _e('City', 'tailorpro'); ?></th>
                                        <th><?php _e('Added', 'tailorpro'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_customers as $customer): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo admin_url('admin.php?page=tailorpro-customers&edit=' . $customer->id); ?>">
                                                    <?php echo esc_html($customer->name); ?>
                                                </a>
                                            </td>
                                            <td><?php echo esc_html($customer->phone); ?></td>
                                            <td><?php echo esc_html($customer->city ?: '-'); ?></td>
                                            <td><?php echo esc_html(date('M j, Y', strtotime($customer->created_at))); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tailorpro-mt-3">
                            <a href="<?php echo admin_url('admin.php?page=tailorpro-customers'); ?>" 
                               class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-outline-primary">
                                <?php _e('View All Customers', 'tailorpro'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- This Month Summary -->
    <?php if ($stats['month_orders'] > 0 || $stats['month_revenue'] > 0): ?>
        <div class="tailorpro-row mt-4">
            <div class="tailorpro-col tailorpro-col-12">
                <div class="tailorpro-card">
                    <div class="tailorpro-card-header">
                        <h3 class="tailorpro-heading tailorpro-heading-3">
                            <span class="dashicons dashicons-calendar-alt"></span>
                            <?php _e('This Month Summary', 'tailorpro'); ?>
                        </h3>
                    </div>
                    <div class="tailorpro-card-body">
                        <div class="tailorpro-row">
                            <div class="tailorpro-col tailorpro-col-6">
                                <div class="tailorpro-text-center">
                                    <div class="tailorpro-stat-number">
                                        <?php echo number_format($stats['month_orders']); ?>
                                    </div>
                                    <div class="tailorpro-stat-label">
                                        <?php _e('Orders This Month', 'tailorpro'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tailorpro-col tailorpro-col-6">
                                <div class="tailorpro-text-center">
                                    <div class="tailorpro-stat-number">
                                        $<?php echo number_format($stats['month_revenue'] ?? 0, 2); ?>
                                    </div>
                                    <div class="tailorpro-stat-label">
                                        <?php _e('Revenue This Month', 'tailorpro'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Chart.js Integration -->
<script>
jQuery(document).ready(function($) {
    // Only initialize charts if Chart.js is loaded
    if (typeof Chart !== 'undefined') {
        initializeCharts();
    }
    
    function initializeCharts() {
        // Orders Chart
        const ordersCtx = document.getElementById('orders-chart');
        if (ordersCtx) {
            new Chart(ordersCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Orders',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: '#007cba',
                        backgroundColor: 'rgba(0, 124, 186, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Revenue Chart
        const revenueCtx = document.getElementById('revenue-chart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Outstanding'],
                    datasets: [{
                        data: [
                            <?php echo floatval($stats['paid_amount'] ?? 0); ?>,
                            <?php echo floatval($stats['outstanding_amount'] ?? 0); ?>
                        ],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
});
</script>

<style>
/* Dashboard-specific styles */
.tailorpro-stat-card {
    transition: transform 0.2s ease;
    cursor: pointer;
}

.tailorpro-stat-card:hover {
    transform: translateY(-2px);
}

#orders-chart, #revenue-chart {
    max-height: 300px;
}

.tailorpro-table-sm th,
.tailorpro-table-sm td {
    padding: 0.5rem;
    font-size: 0.875rem;
}

/* Overdue order highlighting */
.tailorpro-table tbody tr.overdue {
    background-color: rgba(220, 53, 69, 0.1);
}

.tailorpro-table tbody tr.overdue:hover {
    background-color: rgba(220, 53, 69, 0.2);
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .tailorpro-stat-card {
        margin-bottom: 1rem;
    }
    
    .tailorpro-row .tailorpro-col {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>