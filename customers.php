<?php
/**
 * Customers Page Template
 * 
 * Manage customer information, contact details, and history
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle customer actions
if (isset($_POST['action']) && $_POST['action'] === 'add_customer') {
    if (check_ajax_referer('tailorpro_customer_nonce', 'nonce', false)) {
        $customer_data = array(
            'name' => sanitize_text_field($_POST['customer_name']),
            'phone' => sanitize_text_field($_POST['customer_phone']),
            'email' => sanitize_email($_POST['customer_email']),
            'address' => sanitize_textarea_field($_POST['customer_address']),
            'city' => sanitize_text_field($_POST['customer_city']),
            'notes' => sanitize_textarea_field($_POST['customer_notes'])
        );
        
        $customer_id = TailorPro_DB::insert_customer($customer_data);
        
        if (is_wp_error($customer_id)) {
            $error_message = $customer_id->get_error_message();
        } else {
            $success_message = __('Customer added successfully!', 'tailorpro');
        }
    }
}

// Handle customer editing
if (isset($_GET['edit'])) {
    $edit_customer_id = absint($_GET['edit']);
    $edit_customer = TailorPro_DB::get_customer($edit_customer_id);
}

// Check for demo data
$demo_data_installed = get_option('tailorpro_demo_data_installed', false);
?>

<div class="wrap">
    <div class="tailorpro-container">
        <!-- Page Header -->
        <div class="tailorpro-card-header">
            <div class="tailorpro-row">
                <div class="tailorpro-col tailorpro-col-8">
                    <h1 class="tailorpro-heading tailorpro-heading-1">
                        <span class="dashicons dashicons-groups"></span>
                        <?php _e('Customers', 'tailorpro'); ?>
                        <span class="tailorpro-text-muted">(<?php echo TailorPro_DB::count_customers(); ?>)</span>
                    </h1>
                </div>
                <div class="tailorpro-col tailorpro-col-4 text-right">
                    <button type="button" class="tailorpro-btn tailorpro-btn-primary" data-modal="add-customer-modal">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('Add Customer', 'tailorpro'); ?>
                    </button>
                    <button type="button" class="tailorpro-btn tailorpro-btn-info tailorpro-import" data-import-type="customers">
                        <span class="dashicons dashicons-upload"></span>
                        <?php _e('Import', 'tailorpro'); ?>
                    </button>
                    <button type="button" class="tailorpro-btn tailorpro-btn-success tailorpro-export" data-export-type="customers">
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

        <!-- Demo Data Notice -->
        <?php if (!$demo_data_installed): ?>
            <div class="tailorpro-alert tailorpro-alert-info">
                <div class="tailorpro-row">
                    <div class="tailorpro-col tailorpro-col-8">
                        <strong><?php _e('Welcome to Tailor Pro!', 'tailorpro'); ?></strong>
                        <?php _e('Get started by installing demo data to explore the features.', 'tailorpro'); ?>
                    </div>
                    <div class="tailorpro-col tailorpro-col-4 text-right">
                        <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-info" id="install-demo-data">
                            <?php _e('Install Demo Data', 'tailorpro'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="tailorpro-alert tailorpro-alert-success">
                <?php echo esc_html($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="tailorpro-alert tailorpro-alert-danger">
                <?php echo esc_html($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Search and Filters -->
        <div class="tailorpro-card-body tailorpro-pb-2">
            <div class="tailorpro-row">
                <div class="tailorpro-col tailorpro-col-6">
                    <div class="tailorpro-input-group">
                        <input type="text" class="tailorpro-form-control tailorpro-search" 
                               placeholder="<?php _e('Search customers...', 'tailorpro'); ?>" 
                               data-search-target="customers">
                        <div class="tailorpro-input-group-text">
                            <span class="dashicons dashicons-search"></span>
                        </div>
                    </div>
                </div>
                <div class="tailorpro-col tailorpro-col-6 text-right">
                    <select class="tailorpro-form-control" id="customer-sort">
                        <option value="name"><?php _e('Sort by Name', 'tailorpro'); ?></option>
                        <option value="date"><?php _e('Sort by Date Added', 'tailorpro'); ?></option>
                        <option value="phone"><?php _e('Sort by Phone', 'tailorpro'); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="tailorpro-card-body">
            <div class="tailorpro-table-responsive">
                <table class="tailorpro-table tailorpro-table-striped tailorpro-table-hover">
                    <thead>
                        <tr>
                            <th width="5%">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th width="25%"><?php _e('Name', 'tailorpro'); ?></th>
                            <th width="20%"><?php _e('Phone', 'tailorpro'); ?></th>
                            <th width="25%"><?php _e('Email', 'tailorpro'); ?></th>
                            <th width="15%"><?php _e('City', 'tailorpro'); ?></th>
                            <th width="10%"><?php _e('Actions', 'tailorpro'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="customers-table-body">
                        <!-- Customer rows will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="tailorpro-mt-3">
                <div class="tailorpro-row">
                    <div class="tailorpro-col tailorpro-col-6">
                        <div class="tailorpro-form-text">
                            <?php _e('Showing', 'tailorpro'); ?> <span id="customers-count">0</span> <?php _e('customers', 'tailorpro'); ?>
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

<!-- Add Customer Modal -->
<div class="tailorpro-modal" id="add-customer-modal">
    <div class="tailorpro-modal-dialog">
        <div class="tailorpro-modal-content">
            <div class="tailorpro-modal-header">
                <h5 class="tailorpro-modal-title">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <?php _e('Add New Customer', 'tailorpro'); ?>
                </h5>
                <button type="button" class="tailorpro-modal-close">&times;</button>
            </div>
            <form id="customer-form">
                <div class="tailorpro-modal-body">
                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="customer-name">
                                    <?php _e('Full Name', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="tailorpro-form-control" id="customer-name" 
                                       name="customer_name" required 
                                       placeholder="<?php _e('Enter full name', 'tailorpro'); ?>">
                            </div>
                        </div>
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="customer-phone">
                                    <?php _e('Phone Number', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="tailorpro-form-control" id="customer-phone" 
                                       name="customer_phone" required 
                                       placeholder="<?php _e('Enter phone number', 'tailorpro'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="customer-email">
                                    <?php _e('Email Address', 'tailorpro'); ?>
                                </label>
                                <input type="email" class="tailorpro-form-control" id="customer-email" 
                                       name="customer_email" 
                                       placeholder="<?php _e('Enter email address', 'tailorpro'); ?>">
                            </div>
                        </div>
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="customer-city">
                                    <?php _e('City', 'tailorpro'); ?>
                                </label>
                                <input type="text" class="tailorpro-form-control" id="customer-city" 
                                       name="customer_city" 
                                       placeholder="<?php _e('Enter city', 'tailorpro'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="tailorpro-form-group">
                        <label class="tailorpro-form-label" for="customer-address">
                            <?php _e('Address', 'tailorpro'); ?>
                        </label>
                        <textarea class="tailorpro-form-control" id="customer-address" 
                                  name="customer_address" rows="2"
                                  placeholder="<?php _e('Enter full address', 'tailorpro'); ?>"></textarea>
                    </div>

                    <div class="tailorpro-form-group">
                        <label class="tailorpro-form-label" for="customer-notes">
                            <?php _e('Notes', 'tailorpro'); ?>
                        </label>
                        <textarea class="tailorpro-form-control" id="customer-notes" 
                                  name="customer_notes" rows="3"
                                  placeholder="<?php _e('Additional notes about customer', 'tailorpro'); ?>"></textarea>
                    </div>
                </div>
                <div class="tailorpro-modal-footer">
                    <button type="button" class="tailorpro-btn tailorpro-btn-secondary" data-dismiss="modal">
                        <?php _e('Cancel', 'tailorpro'); ?>
                    </button>
                    <button type="submit" class="tailorpro-btn tailorpro-btn-primary">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Add Customer', 'tailorpro'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="tailorpro-modal" id="edit-customer-modal">
    <div class="tailorpro-modal-dialog">
        <div class="tailorpro-modal-content">
            <div class="tailorpro-modal-header">
                <h5 class="tailorpro-modal-title">
                    <span class="dashicons dashicons-edit"></span>
                    <?php _e('Edit Customer', 'tailorpro'); ?>
                </h5>
                <button type="button" class="tailorpro-modal-close">&times;</button>
            </div>
            <form id="edit-customer-form">
                <input type="hidden" id="edit-customer-id" name="customer_id">
                <div class="tailorpro-modal-body">
                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-customer-name">
                                    <?php _e('Full Name', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="tailorpro-form-control" id="edit-customer-name" 
                                       name="customer_name" required 
                                       placeholder="<?php _e('Enter full name', 'tailorpro'); ?>">
                            </div>
                        </div>
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-customer-phone">
                                    <?php _e('Phone Number', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="tailorpro-form-control" id="edit-customer-phone" 
                                       name="customer_phone" required 
                                       placeholder="<?php _e('Enter phone number', 'tailorpro'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-customer-email">
                                    <?php _e('Email Address', 'tailorpro'); ?>
                                </label>
                                <input type="email" class="tailorpro-form-control" id="edit-customer-email" 
                                       name="customer_email" 
                                       placeholder="<?php _e('Enter email address', 'tailorpro'); ?>">
                            </div>
                        </div>
                        <div class="tailorpro-col tailorpro-col-6">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="edit-customer-city">
                                    <?php _e('City', 'tailorpro'); ?>
                                </label>
                                <input type="text" class="tailorpro-form-control" id="edit-customer-city" 
                                       name="customer_city" 
                                       placeholder="<?php _e('Enter city', 'tailorpro'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="tailorpro-form-group">
                        <label class="tailorpro-form-label" for="edit-customer-address">
                            <?php _e('Address', 'tailorpro'); ?>
                        </label>
                        <textarea class="tailorpro-form-control" id="edit-customer-address" 
                                  name="customer_address" rows="2"
                                  placeholder="<?php _e('Enter full address', 'tailorpro'); ?>"></textarea>
                    </div>

                    <div class="tailorpro-form-group">
                        <label class="tailorpro-form-label" for="edit-customer-notes">
                            <?php _e('Notes', 'tailorpro'); ?>
                        </label>
                        <textarea class="tailorpro-form-control" id="edit-customer-notes" 
                                  name="customer_notes" rows="3"
                                  placeholder="<?php _e('Additional notes about customer', 'tailorpro'); ?>"></textarea>
                    </div>
                </div>
                <div class="tailorpro-modal-footer">
                    <button type="button" class="tailorpro-btn tailorpro-btn-secondary" data-dismiss="modal">
                        <?php _e('Cancel', 'tailorpro'); ?>
                    </button>
                    <button type="submit" class="tailorpro-btn tailorpro-btn-primary">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Update Customer', 'tailorpro'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Customer Confirmation Modal -->
<div class="tailorpro-modal" id="delete-customer-modal">
    <div class="tailorpro-modal-dialog">
        <div class="tailorpro-modal-content">
            <div class="tailorpro-modal-header">
                <h5 class="tailorpro-modal-title">
                    <span class="dashicons dashicons-warning"></span>
                    <?php _e('Delete Customer', 'tailorpro'); ?>
                </h5>
                <button type="button" class="tailorpro-modal-close">&times;</button>
            </div>
            <div class="tailorpro-modal-body">
                <p><?php _e('Are you sure you want to delete this customer? This action cannot be undone.', 'tailorpro'); ?></p>
                <p class="tailorpro-text-muted">
                    <strong><?php _e('Note:', 'tailorpro'); ?></strong>
                    <?php _e('You cannot delete customers who have existing orders.', 'tailorpro'); ?>
                </p>
            </div>
            <div class="tailorpro-modal-footer">
                <button type="button" class="tailorpro-btn tailorpro-btn-secondary" data-dismiss="modal">
                    <?php _e('Cancel', 'tailorpro'); ?>
                </button>
                <button type="button" class="tailorpro-btn tailorpro-btn-danger" id="confirm-delete-customer">
                    <span class="dashicons dashicons-trash"></span>
                    <?php _e('Delete Customer', 'tailorpro'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentPage = 1;
    let totalCustomers = 0;
    let customersPerPage = 20;
    let deleteCustomerId = null;

    // Load customers on page load
    loadCustomers();

    // Search functionality
    $('.tailorpro-search').on('input', function() {
        const query = $(this).val();
        if (query.length >= 2 || query.length === 0) {
            currentPage = 1;
            loadCustomers();
        }
    });

    // Pagination
    $('#prev-page').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadCustomers();
        }
    });

    $('#next-page').on('click', function() {
        const totalPages = Math.ceil(totalCustomers / customersPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            loadCustomers();
        }
    });

    // Edit customer
    $(document).on('click', '[data-edit-customer]', function() {
        const customerId = $(this).data('edit-customer');
        editCustomer(customerId);
    });

    // Delete customer
    $(document).on('click', '[data-delete-customer]', function() {
        deleteCustomerId = $(this).data('delete-customer');
        $('#delete-customer-modal').addClass('show');
    });

    $('#confirm-delete-customer').on('click', function() {
        if (deleteCustomerId) {
            deleteCustomer(deleteCustomerId);
        }
    });

    // Handle customer form submission
    $('#customer-form').on('submit', function(e) {
        e.preventDefault();
        addCustomer();
    });

    // Handle edit customer form submission
    $('#edit-customer-form').on('submit', function(e) {
        e.preventDefault();
        updateCustomer();
    });

    function loadCustomers() {
        const search = $('.tailorpro-search').val() || '';
        const sortBy = $('#customer-sort').val() || 'name';

        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_get_customers',
            search: search,
            limit: customersPerPage,
            offset: (currentPage - 1) * customersPerPage,
            sort: sortBy
        }, function(response) {
            if (response.success) {
                renderCustomers(response.data.customers);
                totalCustomers = response.data.total;
                updatePagination();
            }
        });
    }

    function renderCustomers(customers) {
        const tbody = $('#customers-table-body');
        tbody.empty();

        if (customers.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center tailorpro-text-muted">
                        <span class="dashicons dashicons-info"></span>
                        <?php _e('No customers found', 'tailorpro'); ?>
                    </td>
                </tr>
            `);
            return;
        }

        customers.forEach(function(customer) {
            const row = `
                <tr data-id="${customer.id}">
                    <td>
                        <input type="checkbox" value="${customer.id}" class="customer-checkbox">
                    </td>
                    <td>
                        <strong>${customer.name}</strong>
                        ${customer.notes ? `<br><small class="tailorpro-text-muted">${customer.notes.substring(0, 50)}...</small>` : ''}
                    </td>
                    <td>${customer.phone}</td>
                    <td>${customer.email || '<span class="tailorpro-text-muted">-</span>'}</td>
                    <td>${customer.city || '<span class="tailorpro-text-muted">-</span>'}</td>
                    <td class="no-print">
                        <div class="tailorpro-btn-group">
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-info" 
                                    data-edit-customer="${customer.id}" 
                                    data-tooltip="<?php _e('Edit Customer', 'tailorpro'); ?>">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-danger" 
                                    data-delete-customer="${customer.id}" 
                                    data-confirm="<?php _e('Are you sure you want to delete this customer?', 'tailorpro'); ?>"
                                    data-tooltip="<?php _e('Delete Customer', 'tailorpro'); ?>">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        $('#customers-count').text(customers.length);
    }

    function updatePagination() {
        const totalPages = Math.ceil(totalCustomers / customersPerPage);
        $('#current-page').text(currentPage);
        
        $('#prev-page').prop('disabled', currentPage <= 1);
        $('#next-page').prop('disabled', currentPage >= totalPages);
    }

    function addCustomer() {
        const formData = $('#customer-form').serialize();
        formData += '&action=tailorpro_add_customer';

        $.post(tailorpro_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                showAlert('success', response.message);
                $('#add-customer-modal').removeClass('show');
                $('#customer-form')[0].reset();
                loadCustomers();
            } else {
                showAlert('error', response.message);
            }
        });
    }

    function editCustomer(customerId) {
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_get_customer',
            customer_id: customerId
        }, function(response) {
            if (response.success) {
                const customer = response.data;
                $('#edit-customer-id').val(customer.id);
                $('#edit-customer-name').val(customer.name);
                $('#edit-customer-phone').val(customer.phone);
                $('#edit-customer-email').val(customer.email || '');
                $('#edit-customer-city').val(customer.city || '');
                $('#edit-customer-address').val(customer.address || '');
                $('#edit-customer-notes').val(customer.notes || '');
                $('#edit-customer-modal').addClass('show');
            } else {
                showAlert('error', response.message);
            }
        });
    }

    function updateCustomer() {
        const formData = $('#edit-customer-form').serialize();
        formData += '&action=tailorpro_edit_customer';

        $.post(tailorpro_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                showAlert('success', response.message);
                $('#edit-customer-modal').removeClass('show');
                loadCustomers();
            } else {
                showAlert('error', response.message);
            }
        });
    }

    function deleteCustomer(customerId) {
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_delete_customer',
            customer_id: customerId
        }, function(response) {
            if (response.success) {
                showAlert('success', response.message);
                $('#delete-customer-modal').removeClass('show');
                loadCustomers();
            } else {
                showAlert('error', response.message);
            }
        });
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
/* Customer page specific styles */
.tailorpro-text-muted {
    font-size: 0.875rem;
    color: var(--tailorpro-text-muted);
}

.customer-checkbox {
    margin: 0;
}

#customers-table-body tr:hover {
    background-color: var(--tailorpro-bg-tertiary);
}

.tailorpro-btn-group .tailorpro-btn {
    padding: 0.25rem 0.5rem;
    margin: 0 1px;
}

/* Modal adjustments */
.tailorpro-modal-dialog {
    max-width: 600px;
    margin: 3rem auto;
}

@media (max-width: 768px) {
    .tailorpro-row .tailorpro-col {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .tailorpro-btn-group {
        flex-direction: column;
    }
    
    .tailorpro-btn-group .tailorpro-btn {
        margin-bottom: 2px;
        border-radius: 4px;
    }
}
</style>