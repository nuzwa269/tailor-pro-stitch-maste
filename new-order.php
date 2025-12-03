<?php
/**
 * New Order Page Template
 * 
 * Create new orders with customer selection, measurements, and payment details
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get customers for dropdown
$customers = TailorPro_DB::get_customers(0, 0, '');

// Get settings
$settings = get_option('tailorpro_settings', array());
$auto_generate_orders = $settings['auto_generate_order_numbers'] ?? true;
?>

<div class="wrap">
    <div class="tailorpro-container">
        <!-- Page Header -->
        <div class="tailorpro-card-header">
            <h1 class="tailorpro-heading tailorpro-heading-1">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php _e('New Order', 'tailorpro'); ?>
            </h1>
            <p class="tailorpro-text-muted">
                <?php _e('Create a new tailoring order with customer details and measurements', 'tailorpro'); ?>
            </p>
        </div>

        <!-- Order Form -->
        <form id="order-form">
            <div class="tailorpro-row">
                <!-- Customer Information -->
                <div class="tailorpro-col tailorpro-col-6">
                    <div class="tailorpro-card">
                        <div class="tailorpro-card-header">
                            <h3 class="tailorpro-heading tailorpro-heading-3">
                                <span class="dashicons dashicons-groups"></span>
                                <?php _e('Customer Information', 'tailorpro'); ?>
                            </h3>
                        </div>
                        <div class="tailorpro-card-body">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="customer-select">
                                    <?php _e('Select Customer', 'tailorpro'); ?> <span class="text-danger">*</span>
                                </label>
                                <select class="tailorpro-form-control tailorpro-form-select" id="customer-select" name="customer_id" required>
                                    <option value=""><?php _e('Choose a customer...', 'tailorpro'); ?></option>
                                    <?php foreach ($customers as $customer): ?>
                                        <option value="<?php echo $customer->id; ?>" 
                                                data-phone="<?php echo esc_attr($customer->phone); ?>"
                                                data-email="<?php echo esc_attr($customer->email); ?>"
                                                data-address="<?php echo esc_attr($customer->address); ?>"
                                                data-city="<?php echo esc_attr($customer->city); ?>">
                                            <?php echo esc_html($customer->name . ' (' . $customer->phone . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="tailorpro-form-text">
                                    <a href="<?php echo admin_url('admin.php?page=tailorpro-customers'); ?>" 
                                       target="_blank"><?php _e('Add new customer', 'tailorpro'); ?></a>
                                </div>
                            </div>

                            <div class="tailorpro-row">
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="customer-phone">
                                            <?php _e('Phone', 'tailorpro'); ?>
                                        </label>
                                        <input type="text" class="tailorpro-form-control" id="customer-phone" readonly>
                                    </div>
                                </div>
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="customer-email">
                                            <?php _e('Email', 'tailorpro'); ?>
                                        </label>
                                        <input type="email" class="tailorpro-form-control" id="customer-email" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="tailorpro-row">
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="customer-city">
                                            <?php _e('City', 'tailorpro'); ?>
                                        </label>
                                        <input type="text" class="tailorpro-form-control" id="customer-city" readonly>
                                    </div>
                                </div>
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="customer-address">
                                            <?php _e('Address', 'tailorpro'); ?>
                                        </label>
                                        <input type="text" class="tailorpro-form-control" id="customer-address" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="tailorpro-col tailorpro-col-6">
                    <div class="tailorpro-card">
                        <div class="tailorpro-card-header">
                            <h3 class="tailorpro-heading tailorpro-heading-3">
                                <span class="dashicons dashicons-list-view"></span>
                                <?php _e('Order Details', 'tailorpro'); ?>
                            </h3>
                        </div>
                        <div class="tailorpro-card-body">
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="order-number">
                                    <?php _e('Order Number', 'tailorpro'); ?> <?php echo $auto_generate_orders ? '<span class="text-muted">(Auto-generated)</span>' : '<span class="text-danger">*</span>'; ?>
                                </label>
                                <input type="text" class="tailorpro-form-control" id="order-number" 
                                       name="order_number" value="<?php echo $auto_generate_orders ? TailorPro_DB::generate_order_number() : ''; ?>"
                                       <?php echo $auto_generate_orders ? 'readonly' : 'required'; ?>
                                       placeholder="<?php _e('Enter order number', 'tailorpro'); ?>">
                                <?php if ($auto_generate_orders): ?>
                                    <div class="tailorpro-form-text">
                                        <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-outline-primary" id="regenerate-order-number">
                                            <?php _e('Generate New Number', 'tailorpro'); ?>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="tailorpro-row">
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="order-date">
                                            <?php _e('Order Date', 'tailorpro'); ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="tailorpro-form-control" id="order-date" 
                                               name="order_date" required value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="due-date">
                                            <?php _e('Due Date', 'tailorpro'); ?> <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="tailorpro-form-control" id="due-date" 
                                               name="due_date" required 
                                               value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="tailorpro-row">
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="delivery-date">
                                            <?php _e('Delivery Date', 'tailorpro'); ?>
                                        </label>
                                        <input type="date" class="tailorpro-form-control" id="delivery-date" name="delivery_date">
                                    </div>
                                </div>
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="priority">
                                            <?php _e('Priority', 'tailorpro'); ?>
                                        </label>
                                        <select class="tailorpro-form-control" id="priority" name="priority">
                                            <option value="low"><?php _e('Low', 'tailorpro'); ?></option>
                                            <option value="normal" selected><?php _e('Normal', 'tailorpro'); ?></option>
                                            <option value="high"><?php _e('High', 'tailorpro'); ?></option>
                                            <option value="urgent"><?php _e('Urgent', 'tailorpro'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="tailorpro-row">
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="total-amount">
                                            <?php _e('Total Amount', 'tailorpro'); ?> <span class="text-danger">*</span>
                                        </label>
                                        <div class="tailorpro-input-group">
                                            <div class="tailorpro-input-group-text"><?php echo $settings['currency'] ?? '$'; ?></div>
                                            <input type="number" class="tailorpro-form-control" id="total-amount" 
                                                   name="total_amount" required step="0.01" min="0" 
                                                   placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="paid-amount">
                                            <?php _e('Paid Amount', 'tailorpro'); ?>
                                        </label>
                                        <div class="tailorpro-input-group">
                                            <div class="tailorpro-input-group-text"><?php echo $settings['currency'] ?? '$'; ?></div>
                                            <input type="number" class="tailorpro-form-control" id="paid-amount" 
                                                   name="paid_amount" step="0.01" min="0" value="0"
                                                   placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="status">
                                    <?php _e('Status', 'tailorpro'); ?>
                                </label>
                                <select class="tailorpro-form-control" id="status" name="status">
                                    <option value="pending"><?php _e('Pending', 'tailorpro'); ?></option>
                                    <option value="confirmed"><?php _e('Confirmed', 'tailorpro'); ?></option>
                                    <option value="in_progress"><?php _e('In Progress', 'tailorpro'); ?></option>
                                    <option value="ready"><?php _e('Ready for Pickup', 'tailorpro'); ?></option>
                                </select>
                            </div>

                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="order-notes">
                                    <?php _e('Order Notes', 'tailorpro'); ?>
                                </label>
                                <textarea class="tailorpro-form-control" id="order-notes" name="notes" rows="3"
                                          placeholder="<?php _e('Additional notes about this order', 'tailorpro'); ?>"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Measurements Section -->
            <div class="tailorpro-row mt-4">
                <div class="tailorpro-col tailorpro-col-12">
                    <div class="tailorpro-card">
                        <div class="tailorpro-card-header">
                            <div class="tailorpro-row">
                                <div class="tailorpro-col tailorpro-col-8">
                                    <h3 class="tailorpro-heading tailorpro-heading-3">
                                        <span class="dashicons dashicons-admin-settings"></span>
                                        <?php _e('Measurements', 'tailorpro'); ?>
                                    </h3>
                                </div>
                                <div class="tailorpro-col tailorpro-col-4 text-right">
                                    <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-primary" id="add-measurement">
                                        <span class="dashicons dashicons-plus-alt"></span>
                                        <?php _e('Add Item', 'tailorpro'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tailorpro-card-body">
                            <div id="measurements-container">
                                <div class="tailorpro-alert tailorpro-alert-info">
                                    <span class="dashicons dashicons-info"></span>
                                    <?php _e('Add measurements for garments. Click "Add Item" to include multiple items.', 'tailorpro'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="tailorpro-card mt-4">
                <div class="tailorpro-card-body">
                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-6">
                            <button type="button" class="tailorpro-btn tailorpro-btn-secondary" onclick="history.back()">
                                <span class="dashicons dashicons-arrow-left-alt2"></span>
                                <?php _e('Cancel', 'tailorpro'); ?>
                            </button>
                        </div>
                        <div class="tailorpro-col tailorpro-col-6 text-right">
                            <button type="submit" class="tailorpro-btn tailorpro-btn-success">
                                <span class="dashicons dashicons-yes"></span>
                                <?php _e('Create Order', 'tailorpro'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Measurement Item Template -->
<template id="measurement-item-template">
    <div class="measurement-item tailorpro-card mb-3" data-item-id="">
        <div class="tailorpro-card-body">
            <div class="tailorpro-row">
                <div class="tailorpro-col tailorpro-col-4">
                    <div class="tailorpro-form-group">
                        <label class="tailorpro-form-label">
                            <?php _e('Item Type', 'tailorpro'); ?> <span class="text-danger">*</span>
                        </label>
                        <select class="tailorpro-form-control item-type" required>
                            <option value=""><?php _e('Select type...', 'tailorpro'); ?></option>
                            <option value="shirt"><?php _e('Shirt', 'tailorpro'); ?></option>
                            <option value="pants"><?php _e('Pants', 'tailorpro'); ?></option>
                            <option value="suit"><?php _e('Suit', 'tailorpro'); ?></option>
                            <option value="dress"><?php _e('Dress', 'tailorpro'); ?></option>
                            <option value="jacket"><?php _e('Jacket', 'tailorpro'); ?></option>
                            <option value="coat"><?php _e('Coat', 'tailorpro'); ?></option>
                            <option value="other"><?php _e('Other', 'tailorpro'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="tailorpro-col tailorpro-col-4">
                    <div class="tailorpro-form-group">
                        <label class="tailorpro-form-label">
                            <?php _e('Item Name', 'tailorpro'); ?>
                        </label>
                        <input type="text" class="tailorpro-form-control item-name" 
                               placeholder="<?php _e('e.g., Formal Shirt, Business Suit', 'tailorpro'); ?>">
                    </div>
                </div>
                <div class="tailorpro-col tailorpro-col-4">
                    <div class="tailorpro-form-group">
                        <label class="tailorpro-form-label">
                            &nbsp;
                        </label>
                        <div>
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-danger remove-item">
                                <span class="dashicons dashicons-trash"></span>
                                <?php _e('Remove', 'tailorpro'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="measurements-grid">
                <div class="tailorpro-row">
                    <div class="tailorpro-col tailorpro-col-3">
                        <div class="tailorpro-form-group">
                            <label class="tailorpro-form-label"><?php _e('Length', 'tailorpro'); ?></label>
                            <input type="text" class="tailorpro-form-control" data-measure="length" placeholder="">
                        </div>
                    </div>
                    <div class="tailorpro-col tailorpro-col-3">
                        <div class="tailorpro-form-group">
                            <label class="tailorpro-form-label"><?php _e('Chest/Bust', 'tailorpro'); ?></label>
                            <input type="text" class="tailorpro-form-control" data-measure="chest" placeholder="">
                        </div>
                    </div>
                    <div class="tailorpro-col tailorpro-col-3">
                        <div class="tailorpro-form-group">
                            <label class="tailorpro-form-label"><?php _e('Waist', 'tailorpro'); ?></label>
                            <input type="text" class="tailorpro-form-control" data-measure="waist" placeholder="">
                        </div>
                    </div>
                    <div class="tailorpro-col tailorpro-col-3">
                        <div class="tailorpro-form-group">
                            <label class="tailorpro-form-label"><?php _e('Shoulder', 'tailorpro'); ?></label>
                            <input type="text" class="tailorpro-form-control" data-measure="shoulder" placeholder="">
                        </div>
                    </div>
                </div>
                
                <div class="tailorpro-row">
                    <div class="tailorpro-col tailorpro-col-3">
                        <div class="tailorpro-form-group">
                            <label class="tailorpro-form-label"><?php _e('Sleeve', 'tailorpro'); ?></label>
                            <input type="text" class="tailorpro-form-control" data-measure="sleeve" placeholder="">
                        </div>
                    </div>
                    <div class="tailorpro-col tailorpro-col-3">
                        <div class="tailorpro-form-group">
                            <label class="tailorpro-form-label"><?php _e('Neck', 'tailorpro'); ?></label>
                            <input type="text" class="tailorpro-form-control" data-measure="neck" placeholder="">
                        </div>
                    </div>
                    <div class="tailorpro-col tailorpro-col-3">
                        <div class="tailorpro-form-group">
                            <label class="tailorpro-form-label"><?php _e('Hips', 'tailorpro'); ?></label>
                            <input type="text" class="tailorpro-form-control" data-measure="hips" placeholder="">
                        </div>
                    </div>
                    <div class="tailorpro-col tailorpro-col-3">
                        <div class="tailorpro-form-group">
                            <label class="tailorpro-form-label"><?php _e('Crotch', 'tailorpro'); ?></label>
                            <input type="text" class="tailorpro-form-control" data-measure="crotch" placeholder="">
                        </div>
                    </div>
                </div>
                
                <div class="tailorpro-row">
                    <div class="tailorpro-col tailorpro-col-12">
                        <div class="tailorpro-form-group">
                            <label class="tailorpro-form-label"><?php _e('Item Notes', 'tailorpro'); ?></label>
                            <textarea class="tailorpro-form-control item-notes" rows="2" 
                                      placeholder="<?php _e('Additional notes for this item', 'tailorpro'); ?>"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
jQuery(document).ready(function($) {
    let measurementCounter = 0;
    const maxMeasurements = 10;

    // Customer selection handler
    $('#customer-select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#customer-phone').val(selectedOption.data('phone') || '');
            $('#customer-email').val(selectedOption.data('email') || '');
            $('#customer-city').val(selectedOption.data('city') || '');
            $('#customer-address').val(selectedOption.data('address') || '');
        } else {
            $('#customer-phone, #customer-email, #customer-city, #customer-address').val('');
        }
    });

    // Regenerate order number
    $('#regenerate-order-number').on('click', function() {
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_generate_order_number'
        }, function(response) {
            if (response.success) {
                $('#order-number').val(response.data.order_number);
            }
        });
    });

    // Add measurement item
    $('#add-measurement').on('click', function() {
        if (measurementCounter >= maxMeasurements) {
            alert('<?php _e('Maximum number of items reached.', 'tailorpro'); ?>');
            return;
        }

        const template = $('#measurement-item-template').html();
        const $item = $(template);
        $item.attr('data-item-id', ++measurementCounter);
        $('#measurements-container').append($item);

        // Remove info alert when first item is added
        if (measurementCounter === 1) {
            $('#measurements-container .tailorpro-alert').hide();
        }

        updateMeasurementPlaceholders($item.find('.item-type').val(), $item);
    });

    // Remove measurement item
    $(document).on('click', '.remove-item', function() {
        const $item = $(this).closest('.measurement-item');
        $item.remove();
        measurementCounter--;

        if (measurementCounter === 0) {
            $('#measurements-container .tailorpro-alert').show();
        }
    });

    // Update measurement placeholders based on item type
    $(document).on('change', '.item-type', function() {
        const $item = $(this).closest('.measurement-item');
        updateMeasurementPlaceholders($(this).val(), $item);
    });

    // Form submission
    $('#order-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        if (!validateForm()) {
            return;
        }

        // Prepare form data
        const formData = new FormData(this);
        formData.append('action', 'tailorpro_add_order');
        
        // Add measurements data
        const measurements = collectMeasurements();
        if (measurements.length > 0) {
            formData.append('measurements', JSON.stringify(measurements));
        }

        // Submit form
        $.ajax({
            url: tailorpro_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('<?php _e('Order created successfully!', 'tailorpro'); ?>');
                    window.location.href = '<?php echo admin_url('admin.php?page=tailorpro-orders'); ?>';
                } else {
                    alert('<?php _e('Error: ', 'tailorpro'); ?>' + response.message);
                }
            },
            error: function() {
                alert('<?php _e('An error occurred. Please try again.', 'tailorpro'); ?>');
            }
        });
    });

    function updateMeasurementPlaceholders(itemType, $item) {
        const placeholders = {
            shirt: {
                length: '<?php _e('28-32 inches', 'tailorpro'); ?>',
                chest: '<?php _e('36-48 inches', 'tailorpro'); ?>',
                waist: '<?php _e('30-42 inches', 'tailorpro'); ?>',
                shoulder: '<?php _e('16-20 inches', 'tailorpro'); ?>',
                sleeve: '<?php _e('22-26 inches', 'tailorpro'); ?>',
                neck: '<?php _e('14-18 inches', 'tailorpro'); ?>',
                hips: '<?php _e('36-48 inches', 'tailorpro'); ?>',
                crotch: '<?php _e('Not applicable', 'tailorpro'); ?>'
            },
            pants: {
                length: '<?php _e('28-42 inches', 'tailorpro'); ?>',
                chest: '<?php _e('Not applicable', 'tailorpro'); ?>',
                waist: '<?php _e('28-44 inches', 'tailorpro'); ?>',
                shoulder: '<?php _e('Not applicable', 'tailorpro'); ?>',
                sleeve: '<?php _e('Not applicable', 'tailorpro'); ?>',
                neck: '<?php _e('Not applicable', 'tailorpro'); ?>',
                hips: '<?php _e('36-48 inches', 'tailorpro'); ?>',
                crotch: '<?php _e('8-12 inches', 'tailorpro'); ?>'
            },
            suit: {
                length: '<?php _e('Jacket length', 'tailorpro'); ?>',
                chest: '<?php _e('36-48 inches', 'tailorpro'); ?>',
                waist: '<?php _e('30-42 inches', 'tailorpro'); ?>',
                shoulder: '<?php _e('16-20 inches', 'tailorpro'); ?>',
                sleeve: '<?php _e('22-26 inches', 'tailorpro'); ?>',
                neck: '<?php _e('14-18 inches', 'tailorpro'); ?>',
                hips: '<?php _e('36-48 inches', 'tailorpro'); ?>',
                crotch: '<?php _e('Trouser crotch', 'tailorpro'); ?>'
            },
            dress: {
                length: '<?php _e('32-60 inches', 'tailorpro'); ?>',
                chest: '<?php _e('32-44 inches', 'tailorpro'); ?>',
                waist: '<?php _e('24-36 inches', 'tailorpro'); ?>',
                shoulder: '<?php _e('14-18 inches', 'tailorpro'); ?>',
                sleeve: '<?php _e('Varies by style', 'tailorpro'); ?>',
                neck: '<?php _e('13-17 inches', 'tailorpro'); ?>',
                hips: '<?php _e('34-46 inches', 'tailorpro'); ?>',
                crotch: '<?php _e('Not applicable', 'tailorpro'); ?>'
            }
        };

        const selectedPlaceholders = placeholders[itemType] || placeholders.shirt;
        
        $item.find('[data-measure]').each(function() {
            const measure = $(this).data('measure');
            $(this).attr('placeholder', selectedPlaceholders[measure] || '');
        });
    }

    function collectMeasurements() {
        const measurements = [];
        
        $('.measurement-item').each(function() {
            const $item = $(this);
            const itemData = {
                item_type: $item.find('.item-type').val(),
                item_name: $item.find('.item-name').val(),
                measurements: {},
                notes: $item.find('.item-notes').val()
            };
            
            // Collect all measurement fields
            $item.find('[data-measure]').each(function() {
                const measure = $(this).data('measure');
                const value = $(this).val().trim();
                if (value) {
                    itemData.measurements[measure] = value;
                }
            });
            
            // Only add if at least one measurement is provided
            if (Object.keys(itemData.measurements).length > 0) {
                measurements.push(itemData);
            }
        });
        
        return measurements;
    }

    function validateForm() {
        let isValid = true;
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Check required fields
        if (!$('#customer-select').val()) {
            showFieldError($('#customer-select'), '<?php _e('Please select a customer', 'tailorpro'); ?>');
            isValid = false;
        }
        
        if (!$('#order-number').val()) {
            showFieldError($('#order-number'), '<?php _e('Order number is required', 'tailorpro'); ?>');
            isValid = false;
        }
        
        if (!$('#order-date').val()) {
            showFieldError($('#order-date'), '<?php _e('Order date is required', 'tailorpro'); ?>');
            isValid = false;
        }
        
        if (!$('#due-date').val()) {
            showFieldError($('#due-date'), '<?php _e('Due date is required', 'tailorpro'); ?>');
            isValid = false;
        }
        
        if (!$('#total-amount').val()) {
            showFieldError($('#total-amount'), '<?php _e('Total amount is required', 'tailorpro'); ?>');
            isValid = false;
        }
        
        // Validate date logic
        const orderDate = new Date($('#order-date').val());
        const dueDate = new Date($('#due-date').val());
        
        if (dueDate <= orderDate) {
            showFieldError($('#due-date'), '<?php _e('Due date must be after order date', 'tailorpro'); ?>');
            isValid = false;
        }
        
        return isValid;
    }

    function showFieldError($field, message) {
        $field.addClass('is-invalid');
        $field.after(`<div class="invalid-feedback">${message}</div>`);
    }
});
</script>

<style>
.measurement-item {
    border-left: 4px solid var(--tailorpro-primary);
}

.measurements-grid {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--tailorpro-border);
}

.tailorpro-form-group .invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: var(--tailorpro-danger);
}

.tailorpro-form-control.is-invalid {
    border-color: var(--tailorpro-danger);
}

#measurements-container .tailorpro-alert {
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .tailorpro-row .tailorpro-col {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .measurements-grid .tailorpro-row .tailorpro-col {
        flex: 0 0 50%;
        max-width: 50%;
    }
}
</style>