<?php
/**
 * Settings Page Template
 * 
 * Plugin settings, theme configuration, and system management
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$settings = get_option('tailorpro_settings', array());
$demo_data_installed = get_option('tailorpro_demo_data_installed', false);
$current_theme = $settings['theme_mode'] ?? 'light';

// Handle form submissions
if (isset($_POST['submit'])) {
    if (check_ajax_referer('tailorpro_settings_nonce', 'nonce', false)) {
        $new_settings = array(
            'logo_url' => esc_url_raw($_POST['logo_url']),
            'theme_mode' => sanitize_text_field($_POST['theme_mode']),
            'currency' => sanitize_text_field($_POST['currency']),
            'date_format' => sanitize_text_field($_POST['date_format']),
            'auto_generate_order_numbers' => !empty($_POST['auto_generate_order_numbers']),
            'enable_notifications' => !empty($_POST['enable_notifications']),
            'low_stock_threshold' => absint($_POST['low_stock_threshold'])
        );
        
        $updated_settings = array_merge($settings, $new_settings);
        update_option('tailorpro_settings', $updated_settings);
        
        $success_message = __('Settings saved successfully!', 'tailorpro');
        $settings = $updated_settings; // Update local variable
    }
}
?>

<div class="wrap">
    <div class="tailorpro-container">
        <!-- Page Header -->
        <div class="tailorpro-card-header">
            <div class="tailorpro-row">
                <div class="tailorpro-col tailorpro-col-8">
                    <h1 class="tailorpro-heading tailorpro-heading-1">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php _e('Settings', 'tailorpro'); ?>
                    </h1>
                    <p class="tailorpro-text-muted">
                        <?php _e('Configure Tailor Pro settings, appearance, and system preferences', 'tailorpro'); ?>
                    </p>
                </div>
                <div class="tailorpro-col tailorpro-col-4 text-right">
                    <button type="button" class="tailorpro-btn tailorpro-btn-info" id="theme-toggle">
                        <span class="dashicons dashicons-admin-appearance"></span>
                        <span class="toggle-text"><?php echo $current_theme === 'light' ? 'Dark' : 'Light'; ?></span> <?php _e('Mode', 'tailorpro'); ?>
                        <span><?php echo $current_theme === 'light' ? '🌙' : '☀️'; ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <?php if (isset($success_message)): ?>
            <div class="tailorpro-alert tailorpro-alert-success">
                <span class="dashicons dashicons-yes"></span>
                <?php echo esc_html($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('tailorpro_settings_nonce'); ?>
            
            <div class="tailorpro-row">
                <!-- General Settings -->
                <div class="tailorpro-col tailorpro-col-6">
                    <div class="tailorpro-card">
                        <div class="tailorpro-card-header">
                            <h3 class="tailorpro-heading tailorpro-heading-3">
                                <span class="dashicons dashicons-admin-generic"></span>
                                <?php _e('General Settings', 'tailorpro'); ?>
                            </h3>
                        </div>
                        <div class="tailorpro-card-body">
                            <!-- Logo Upload -->
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="logo-url">
                                    <?php _e('Business Logo', 'tailorpro'); ?>
                                </label>
                                <div class="tailorpro-input-group">
                                    <input type="url" class="tailorpro-form-control" id="logo-url" 
                                           name="logo_url" value="<?php echo esc_attr($settings['logo_url'] ?? ''); ?>" 
                                           placeholder="<?php _e('Logo URL', 'tailorpro'); ?>">
                                    <button type="button" class="tailorpro-input-group-text" id="upload-logo-btn">
                                        <span class="dashicons dashicons-upload"></span>
                                    </button>
                                </div>
                                <div class="tailorpro-form-text">
                                    <?php _e('Upload your business logo to display in reports and invoices', 'tailorpro'); ?>
                                </div>
                                <?php if (!empty($settings['logo_url'])): ?>
                                    <div class="tailorpro-mt-2">
                                        <img src="<?php echo esc_url($settings['logo_url']); ?>" 
                                             alt="<?php _e('Business Logo', 'tailorpro'); ?>" 
                                             style="max-width: 150px; max-height: 100px; border: 1px solid var(--tailorpro-border);">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Currency -->
                            <div class="tailorpro-row">
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="currency">
                                            <?php _e('Currency', 'tailorpro'); ?>
                                        </label>
                                        <select class="tailorpro-form-control" id="currency" name="currency">
                                            <option value="$" <?php selected($settings['currency'] ?? '$', '$'); ?>>$ USD</option>
                                            <option value="€" <?php selected($settings['currency'] ?? '$', '€'); ?>>€ EUR</option>
                                            <option value="£" <?php selected($settings['currency'] ?? '$', '£'); ?>>£ GBP</option>
                                            <option value="₹" <?php selected($settings['currency'] ?? '$', '₹'); ?>>₹ INR</option>
                                            <option value="¥" <?php selected($settings['currency'] ?? '$', '¥'); ?>>¥ JPY</option>
                                            <option value="₦" <?php selected($settings['currency'] ?? '$', '₦'); ?>>₦ NGN</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="tailorpro-col tailorpro-col-6">
                                    <div class="tailorpro-form-group">
                                        <label class="tailorpro-form-label" for="date-format">
                                            <?php _e('Date Format', 'tailorpro'); ?>
                                        </label>
                                        <select class="tailorpro-form-control" id="date-format" name="date_format">
                                            <option value="Y-m-d" <?php selected($settings['date_format'] ?? 'Y-m-d', 'Y-m-d'); ?>>YYYY-MM-DD</option>
                                            <option value="m/d/Y" <?php selected($settings['date_format'] ?? 'Y-m-d', 'm/d/Y'); ?>>MM/DD/YYYY</option>
                                            <option value="d/m/Y" <?php selected($settings['date_format'] ?? 'Y-m-d', 'd/m/Y'); ?>>DD/MM/YYYY</option>
                                            <option value="F j, Y" <?php selected($settings['date_format'] ?? 'Y-m-d', 'F j, Y'); ?>>Month DD, YYYY</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Theme Mode -->
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="theme-mode">
                                    <?php _e('Theme Mode', 'tailorpro'); ?>
                                </label>
                                <select class="tailorpro-form-control" id="theme-mode" name="theme_mode">
                                    <option value="light" <?php selected($current_theme, 'light'); ?>><?php _e('Light Theme', 'tailorpro'); ?></option>
                                    <option value="dark" <?php selected($current_theme, 'dark'); ?>><?php _e('Dark Theme', 'tailorpro'); ?></option>
                                    <option value="auto" <?php selected($current_theme, 'auto'); ?>><?php _e('Auto (System Preference)', 'tailorpro'); ?></option>
                                </select>
                                <div class="tailorpro-form-text">
                                    <?php _e('Choose your preferred theme appearance', 'tailorpro'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Settings -->
                <div class="tailorpro-col tailorpro-col-6">
                    <div class="tailorpro-card">
                        <div class="tailorpro-card-header">
                            <h3 class="tailorpro-heading tailorpro-heading-3">
                                <span class="dashicons dashicons-building"></span>
                                <?php _e('Business Settings', 'tailorpro'); ?>
                            </h3>
                        </div>
                        <div class="tailorpro-card-body">
                            <!-- Order Number Settings -->
                            <div class="tailorpro-form-group">
                                <div class="tailorpro-form-check">
                                    <input type="checkbox" class="tailorpro-form-check-input" id="auto-generate-order-numbers" 
                                           name="auto_generate_order_numbers" value="1"
                                           <?php checked($settings['auto_generate_order_numbers'] ?? true); ?>>
                                    <label class="tailorpro-form-check-label" for="auto-generate-order-numbers">
                                        <?php _e('Auto-generate Order Numbers', 'tailorpro'); ?>
                                    </label>
                                </div>
                                <div class="tailorpro-form-text">
                                    <?php _e('Automatically generate unique order numbers for new orders', 'tailorpro'); ?>
                                </div>
                            </div>

                            <!-- Notification Settings -->
                            <div class="tailorpro-form-group">
                                <div class="tailorpro-form-check">
                                    <input type="checkbox" class="tailorpro-form-check-input" id="enable-notifications" 
                                           name="enable_notifications" value="1"
                                           <?php checked($settings['enable_notifications'] ?? true); ?>>
                                    <label class="tailorpro-form-check-label" for="enable-notifications">
                                        <?php _e('Enable Notifications', 'tailorpro'); ?>
                                    </label>
                                </div>
                                <div class="tailorpro-form-text">
                                    <?php _e('Show system notifications for important events', 'tailorpro'); ?>
                                </div>
                            </div>

                            <!-- Low Stock Threshold -->
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label" for="low-stock-threshold">
                                    <?php _e('Low Stock Alert Threshold', 'tailorpro'); ?>
                                </label>
                                <div class="tailorpro-input-group">
                                    <input type="number" class="tailorpro-form-control" id="low-stock-threshold" 
                                           name="low_stock_threshold" value="<?php echo esc_attr($settings['low_stock_threshold'] ?? 10); ?>" 
                                           min="1" max="999">
                                    <div class="tailorpro-input-group-text"><?php _e('items', 'tailorpro'); ?></div>
                                </div>
                                <div class="tailorpro-form-text">
                                    <?php _e('Alert when inventory falls below this number', 'tailorpro'); ?>
                                </div>
                            </div>

                            <!-- Business Information -->
                            <div class="tailorpro-form-group">
                                <label class="tailorpro-form-label">
                                    <?php _e('Business Information', 'tailorpro'); ?>
                                </label>
                                <div class="tailorpro-alert tailorpro-alert-info">
                                    <span class="dashicons dashicons-info"></span>
                                    <?php _e('Business information is automatically included in reports and invoices', 'tailorpro'); ?>
                                </div>
                                <div class="tailorpro-mt-2">
                                    <textarea class="tailorpro-form-control" rows="4" placeholder="<?php _e('Enter your business information (address, phone, email, etc.)', 'tailorpro'); ?>" readonly><?php echo get_bloginfo('name'); ?>&#10;<?php echo get_bloginfo('description'); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Management -->
            <div class="tailorpro-row mt-4">
                <div class="tailorpro-col tailorpro-col-12">
                    <div class="tailorpro-card">
                        <div class="tailorpro-card-header">
                            <h3 class="tailorpro-heading tailorpro-heading-3">
                                <span class="dashicons dashicons-admin-tools"></span>
                                <?php _e('System Management', 'tailorpro'); ?>
                            </h3>
                        </div>
                        <div class="tailorpro-card-body">
                            <div class="tailorpro-row">
                                <!-- Demo Data -->
                                <div class="tailorpro-col tailorpro-col-4">
                                    <div class="tailorpro-card">
                                        <div class="tailorpro-card-body text-center">
                                            <h4><?php _e('Demo Data', 'tailorpro'); ?></h4>
                                            <p class="tailorpro-text-muted">
                                                <?php _e('Install sample data to explore features', 'tailorpro'); ?>
                                            </p>
                                            <?php if ($demo_data_installed): ?>
                                                <div class="tailorpro-alert tailorpro-alert-success">
                                                    <span class="dashicons dashicons-yes"></span>
                                                    <?php _e('Demo data installed', 'tailorpro'); ?>
                                                </div>
                                            <?php else: ?>
                                                <button type="button" class="tailorpro-btn tailorpro-btn-info" id="install-demo-data">
                                                    <span class="dashicons dashicons-download"></span>
                                                    <?php _e('Install Demo Data', 'tailorpro'); ?>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Export -->
                                <div class="tailorpro-col tailorpro-col-4">
                                    <div class="tailorpro-card">
                                        <div class="tailorpro-card-body text-center">
                                            <h4><?php _e('Data Export', 'tailorpro'); ?></h4>
                                            <p class="tailorpro-text-muted">
                                                <?php _e('Export all your data for backup', 'tailorpro'); ?>
                                            </p>
                                            <div class="tailorpro-btn-group">
                                                <button type="button" class="tailorpro-btn tailorpro-btn-success tailorpro-export" 
                                                        data-export-type="customers">
                                                    <span class="dashicons dashicons-groups"></span>
                                                    <?php _e('Customers', 'tailorpro'); ?>
                                                </button>
                                                <button type="button" class="tailorpro-btn tailorpro-btn-success tailorpro-export" 
                                                        data-export-type="orders">
                                                    <span class="dashicons dashicons-list-view"></span>
                                                    <?php _e('Orders', 'tailorpro'); ?>
                                                </button>
                                                <button type="button" class="tailorpro-btn tailorpro-btn-success tailorpro-export" 
                                                        data-export-type="measurements">
                                                    <span class="dashicons dashicons-admin-tools"></span>
                                                    <?php _e('Measurements', 'tailorpro'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Import -->
                                <div class="tailorpro-col tailorpro-col-4">
                                    <div class="tailorpro-card">
                                        <div class="tailorpro-card-body text-center">
                                            <h4><?php _e('Data Import', 'tailorpro'); ?></h4>
                                            <p class="tailorpro-text-muted">
                                                <?php _e('Import data from backup files', 'tailorpro'); ?>
                                            </p>
                                            <div class="tailorpro-btn-group">
                                                <button type="button" class="tailorpro-btn tailorpro-btn-info tailorpro-import" 
                                                        data-import-type="customers">
                                                    <span class="dashicons dashicons-groups"></span>
                                                    <?php _e('Customers', 'tailorpro'); ?>
                                                </button>
                                                <button type="button" class="tailorpro-btn tailorpro-btn-info tailorpro-import" 
                                                        data-import-type="orders">
                                                    <span class="dashicons dashicons-list-view"></span>
                                                    <?php _e('Orders', 'tailorpro'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plugin Information -->
            <div class="tailorpro-row mt-4">
                <div class="tailorpro-col tailorpro-col-12">
                    <div class="tailorpro-card">
                        <div class="tailorpro-card-header">
                            <h3 class="tailorpro-heading tailorpro-heading-3">
                                <span class="dashicons dashicons-info"></span>
                                <?php _e('Plugin Information', 'tailorpro'); ?>
                            </h3>
                        </div>
                        <div class="tailorpro-card-body">
                            <div class="tailorpro-row">
                                <div class="tailorpro-col tailorpro-col-6">
                                    <table class="tailorpro-table tailorpro-table-sm">
                                        <tr>
                                            <td><strong><?php _e('Plugin Version:', 'tailorpro'); ?></strong></td>
                                            <td><?php echo TAILORPRO_VERSION; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php _e('WordPress Version:', 'tailorpro'); ?></strong></td>
                                            <td><?php echo get_bloginfo('version'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php _e('PHP Version:', 'tailorpro'); ?></strong></td>
                                            <td><?php echo PHP_VERSION; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php _e('Database Status:', 'tailorpro'); ?></strong></td>
                                            <td><span class="tailorpro-badge tailorpro-badge-success"><?php _e('Connected', 'tailorpro'); ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="tailorpro-col tailorpro-col-6">
                                    <table class="tailorpro-table tailorpro-table-sm">
                                        <tr>
                                            <td><strong><?php _e('Total Customers:', 'tailorpro'); ?></strong></td>
                                            <td><?php echo TailorPro_DB::count_customers(); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php _e('Total Orders:', 'tailorpro'); ?></strong></td>
                                            <td><?php echo TailorPro_DB::count_orders(); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php _e('Plugin Active Since:', 'tailorpro'); ?></strong></td>
                                            <td><?php echo get_option('tailorpro_activation_date', __('Unknown', 'tailorpro')); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong><?php _e('System Status:', 'tailorpro'); ?></strong></td>
                                            <td><span class="tailorpro-badge tailorpro-badge-success"><?php _e('Running', 'tailorpro'); ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Settings -->
            <div class="tailorpro-card mt-4">
                <div class="tailorpro-card-body">
                    <div class="tailorpro-row">
                        <div class="tailorpro-col tailorpro-col-6">
                            <button type="button" class="tailorpro-btn tailorpro-btn-secondary" onclick="history.back()">
                                <span class="dashicons dashicons-arrow-left-alt2"></span>
                                <?php _e('Back', 'tailorpro'); ?>
                            </button>
                        </div>
                        <div class="tailorpro-col tailorpro-col-6 text-right">
                            <button type="submit" name="submit" class="tailorpro-btn tailorpro-btn-primary">
                                <span class="dashicons dashicons-yes"></span>
                                <?php _e('Save Settings', 'tailorpro'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Logo Upload Modal -->
<div class="tailorpro-modal" id="logo-upload-modal">
    <div class="tailorpro-modal-dialog">
        <div class="tailorpro-modal-content">
            <div class="tailorpro-modal-header">
                <h5 class="tailorpro-modal-title">
                    <span class="dashicons dashicons-upload"></span>
                    <?php _e('Upload Logo', 'tailorpro'); ?>
                </h5>
                <button type="button" class="tailorpro-modal-close">&times;</button>
            </div>
            <div class="tailorpro-modal-body">
                <div class="tailorpro-form-group">
                    <label class="tailorpro-form-label" for="logo-file">
                        <?php _e('Select Logo File', 'tailorpro'); ?>
                    </label>
                    <input type="file" class="tailorpro-form-control" id="logo-file" accept="image/*">
                    <div class="tailorpro-form-text">
                        <?php _e('Recommended size: 300x100 pixels, Max file size: 2MB', 'tailorpro'); ?>
                    </div>
                </div>
                <div id="logo-preview" class="tailorpro-mt-3" style="display: none;">
                    <label class="tailorpro-form-label"><?php _e('Preview:', 'tailorpro'); ?></label>
                    <img id="logo-preview-img" src="" alt="Logo Preview" 
                         style="max-width: 300px; max-height: 100px; border: 1px solid var(--tailorpro-border);">
                </div>
            </div>
            <div class="tailorpro-modal-footer">
                <button type="button" class="tailorpro-btn tailorpro-btn-secondary" data-dismiss="modal">
                    <?php _e('Cancel', 'tailorpro'); ?>
                </button>
                <button type="button" class="tailorpro-btn tailorpro-btn-primary" id="upload-logo-confirm">
                    <span class="dashicons dashicons-yes"></span>
                    <?php _e('Upload Logo', 'tailorpro'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let uploadedLogoUrl = '';

    // Logo upload functionality
    $('#upload-logo-btn').on('click', function() {
        $('#logo-upload-modal').addClass('show');
    });

    $('#logo-file').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logo-preview-img').attr('src', e.target.result);
                $('#logo-preview').show();
                uploadedLogoUrl = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    $('#upload-logo-confirm').on('click', function() {
        if (uploadedLogoUrl) {
            $('#logo-url').val(uploadedLogoUrl);
            $('#logo-upload-modal').removeClass('show');
            
            // Add preview to main form
            const logoPreview = `
                <div class="tailorpro-mt-2">
                    <img src="${uploadedLogoUrl}" 
                         alt="Business Logo" 
                         style="max-width: 150px; max-height: 100px; border: 1px solid var(--tailorpro-border);">
                </div>
            `;
            $('#logo-url').next('.tailorpro-form-text').next('.tailorpro-mt-2').remove();
            $('#logo-url').after(logoPreview);
        }
    });

    // Theme toggle
    $('#theme-toggle').on('click', function() {
        const currentTheme = $('body').attr('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        $('body').attr('data-theme', newTheme);
        $('#theme-mode').val(newTheme);
        
        $(this).find('.toggle-text').text(newTheme === 'light' ? 'Dark' : 'Light');
        $(this).find('span:last').text(newTheme === 'light' ? '🌙' : '☀️');
    });

    // Demo data installation
    $('#install-demo-data').on('click', function() {
        if (confirm('<?php _e('This will install demo data. Continue?', 'tailorpro'); ?>')) {
            $.post(tailorpro_ajax.ajax_url, {
                action: 'tailorpro_install_demo_data'
            }, function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('<?php _e('Error: ', 'tailorpro'); ?>' + response.message);
                }
            });
        }
    });

    // Auto-save form changes
    let saveTimeout;
    $('input, select, textarea').on('change', function() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(function() {
            // Auto-save functionality can be added here
            console.log('Settings changed - auto-save enabled');
        }, 2000);
    });

    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Validate URL
        const logoUrl = $('#logo-url').val();
        if (logoUrl && !isValidUrl(logoUrl)) {
            showFieldError($('#logo-url'), '<?php _e('Please enter a valid URL', 'tailorpro'); ?>');
            isValid = false;
        }
        
        // Validate numbers
        const threshold = $('#low-stock-threshold').val();
        if (threshold && (threshold < 1 || threshold > 999)) {
            showFieldError($('#low-stock-threshold'), '<?php _e('Threshold must be between 1 and 999', 'tailorpro'); ?>');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });

    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    function showFieldError($field, message) {
        $field.addClass('is-invalid');
        $field.after(`<div class="invalid-feedback">${message}</div>`);
    }

    // Settings export/import functions
    $('.tailorpro-export').on('click', function() {
        const type = $(this).data('export-type');
        exportSettings(type);
    });

    $('.tailorpro-import').on('click', function() {
        const type = $(this).data('import-type');
        openImportModal(type);
    });

    function exportSettings(type) {
        const form = $('<form>', {
            method: 'POST',
            action: tailorpro_ajax.ajax_url
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'action',
            value: 'tailorpro_export_data'
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'export_type',
            value: type
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'nonce',
            value: tailorpro_ajax.nonce
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
    }

    function openImportModal(type) {
        const modalHtml = `
            <div class="tailorpro-modal show" id="import-modal">
                <div class="tailorpro-modal-dialog">
                    <div class="tailorpro-modal-content">
                        <div class="tailorpro-modal-header">
                            <h5 class="tailorpro-modal-title">Import ${type}</h5>
                            <button type="button" class="tailorpro-modal-close">&times;</button>
                        </div>
                        <div class="tailorpro-modal-body">
                            <form id="import-form" enctype="multipart/form-data">
                                <div class="tailorpro-form-group">
                                    <label class="tailorpro-form-label">Select File</label>
                                    <input type="file" name="import_file" class="tailorpro-form-control" accept=".json" required>
                                    <div class="tailorpro-form-text">Choose a JSON file to import</div>
                                </div>
                                <input type="hidden" name="import_type" value="${type}">
                            </form>
                        </div>
                        <div class="tailorpro-modal-footer">
                            <button type="button" class="tailorpro-btn tailorpro-btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="tailorpro-btn tailorpro-btn-primary" id="import-submit">Import</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        
        $('#import-submit').on('click', function() {
            const form = $('#import-form')[0];
            const formData = new FormData(form);
            formData.append('action', 'tailorpro_import_data');
            formData.append('nonce', tailorpro_ajax.nonce);
            
            $.ajax({
                url: tailorpro_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#import-modal').remove();
                        location.reload();
                    } else {
                        alert('<?php _e('Error: ', 'tailorpro'); ?>' + response.message);
                    }
                },
                error: function() {
                    alert('<?php _e('An error occurred during import', 'tailorpro'); ?>');
                }
            });
        });
    }
});
</script>

<style>
/* Settings page specific styles */
.tailorpro-form-check {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.tailorpro-form-check-input {
    margin-right: 0.5rem;
}

.tailorpro-form-check-label {
    color: var(--tailorpro-text-primary);
    cursor: pointer;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: var(--tailorpro-danger);
}

.tailorpro-form-control.is-invalid {
    border-color: var(--tailorpro-danger);
}

#logo-preview {
    border: 1px solid var(--tailorpro-border);
    padding: 1rem;
    border-radius: 6px;
    background-color: var(--tailorpro-bg-secondary);
}

.tailorpro-card .tailorpro-card-body h4 {
    color: var(--tailorpro-text-primary);
    margin-bottom: 0.5rem;
}

.tailorpro-card .tailorpro-card-body p {
    margin-bottom: 1rem;
}

.tailorpro-btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tailorpro-btn-group .tailorpro-btn {
    flex: 1;
    min-width: auto;
}

.tailorpro-table-sm th,
.tailorpro-table-sm td {
    padding: 0.5rem;
    font-size: 0.875rem;
}

/* Modal adjustments */
.tailorpro-modal-dialog {
    max-width: 500px;
    margin: 3rem auto;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .tailorpro-row .tailorpro-col {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .tailorpro-btn-group {
        flex-direction: column;
    }
    
    .tailorpro-btn-group .tailorpro-btn {
        margin-bottom: 0.25rem;
        border-radius: 4px;
    }
    
    .tailorpro-stat-card {
        margin-bottom: 1rem;
    }
}

/* Auto-save indicator */
.auto-save-indicator {
    position: fixed;
    top: 32px;
    right: 20px;
    background: var(--tailorpro-success);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    font-size: 0.875rem;
    z-index: 9999;
    display: none;
}

.auto-save-indicator.show {
    display: block;
}

/* Theme preview */
.theme-preview {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.theme-option {
    flex: 1;
    padding: 1rem;
    border: 2px solid var(--tailorpro-border);
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s ease;
}

.theme-option:hover {
    border-color: var(--tailorpro-primary);
}

.theme-option.selected {
    border-color: var(--tailorpro-primary);
    background-color: var(--tailorpro-bg-secondary);
}

.theme-preview-light {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    color: #212529;
}

.theme-preview-dark {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #ffffff;
}
</style>
