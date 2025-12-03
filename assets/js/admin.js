/**
 * Tailor Pro Admin JavaScript
 * 
 * Handles all interactive functionality for the Tailor Pro plugin
 * including forms, modals, AJAX requests, and UI interactions.
 */

(function($) {
    'use strict';
    
    // Global variables
    let currentPage = '';
    let currentTheme = window.tailorproThemeMode || 'light';
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        initializeApp();
    });
    
    /**
     * Initialize the application
     */
    function initializeApp() {
        // Set current page
        currentPage = getCurrentPage();
        
        // Initialize components
        initializeTheme();
        initializeForms();
        initializeModals();
        initializeTables();
        initializeButtons();
        initializeTooltips();
        initializeSearch();
        initializeThemeToggle();
        
        // Load initial data
        if (currentPage === 'dashboard') {
            loadDashboardStats();
        } else if (currentPage === 'customers') {
            loadCustomers();
        } else if (currentPage === 'orders') {
            loadOrders();
        }
        
        // Set up event handlers
        setupEventHandlers();
        
        console.log('Tailor Pro initialized successfully');
    }
    
    /**
     * Get current page from URL
     */
    function getCurrentPage() {
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page') || '';
        
        if (page.includes('dashboard')) return 'dashboard';
        if (page.includes('customers')) return 'customers';
        if (page.includes('new-order')) return 'new-order';
        if (page.includes('orders')) return 'orders';
        if (page.includes('settings')) return 'settings';
        
        return 'dashboard';
    }
    
    /**
     * Initialize theme support
     */
    function initializeTheme() {
        // Set theme attribute on body
        $('body').attr('data-theme', currentTheme);
        
        // Initialize theme variables
        updateThemeVariables();
    }
    
    /**
     * Update CSS variables based on theme
     */
    function updateThemeVariables() {
        const root = document.documentElement;
        
        if (currentTheme === 'dark') {
            root.style.setProperty('--tailorpro-bg-primary', '#1a1a1a');
            root.style.setProperty('--tailorpro-bg-secondary', '#2d2d2d');
            root.style.setProperty('--tailorpro-text-primary', '#ffffff');
            root.style.setProperty('--tailorpro-text-secondary', '#b0b0b0');
            root.style.setProperty('--tailorpro-border', '#404040');
        } else {
            root.style.setProperty('--tailorpro-bg-primary', '#ffffff');
            root.style.setProperty('--tailorpro-bg-secondary', '#f8f9fa');
            root.style.setProperty('--tailorpro-text-primary', '#212529');
            root.style.setProperty('--tailorpro-text-secondary', '#6c757d');
            root.style.setProperty('--tailorpro-border', '#dee2e6');
        }
    }
    
    /**
     * Initialize form validation and submission
     */
    function initializeForms() {
        // Customer form
        $(document).on('submit', '#customer-form', handleCustomerFormSubmit);
        $(document).on('submit', '#order-form', handleOrderFormSubmit);
        $(document).on('submit', '#settings-form', handleSettingsFormSubmit);
        
        // Form validation
        $(document).on('blur', '.tailorpro-form-control[required]', validateField);
        $(document).on('input', '.tailorpro-form-control[required]', clearValidationErrors);
        
        // Phone number formatting
        $(document).on('input', 'input[name="phone"]', formatPhoneNumber);
        
        // Email validation
        $(document).on('blur', 'input[type="email"]', validateEmail);
    }
    
    /**
     * Initialize modal functionality
     */
    function initializeModals() {
        // Open modals
        $(document).on('click', '[data-modal]', function(e) {
            e.preventDefault();
            const modalId = $(this).data('modal');
            openModal(modalId);
        });
        
        // Close modals
        $(document).on('click', '.tailorpro-modal-close, [data-dismiss="modal"]', function() {
            closeModal();
        });
        
        // Close modal on backdrop click
        $(document).on('click', '.tailorpro-modal', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Close modal on Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    }
    
    /**
     * Initialize table functionality
     */
    function initializeTables() {
        // Sortable tables
        $('.tailorpro-table thead th').each(function() {
            if (!$(this).hasClass('no-sort')) {
                $(this).addClass('sortable');
            }
        });
        
        // Row selection
        $(document).on('click', '.tailorpro-table tbody tr', function() {
            $(this).toggleClass('selected');
        });
        
        // Select all checkbox
        $(document).on('change', '#select-all', function() {
            const checked = $(this).is(':checked');
            $('.tailorpro-table tbody input[type="checkbox"]').prop('checked', checked);
            $('.tailorpro-table tbody tr').toggleClass('selected', checked);
        });
    }
    
    /**
     * Initialize button functionality
     */
    function initializeButtons() {
        // Confirmation buttons
        $(document).on('click', '[data-confirm]', function(e) {
            e.preventDefault();
            const message = $(this).data('confirm');
            const callback = $(this).data('callback');
            
            if (confirm(tailorpro_ajax.strings.confirm_delete || message)) {
                if (callback && typeof window[callback] === 'function') {
                    window[callback]($(this));
                }
            }
        });
        
        // Loading state
        $(document).on('click', '.tailorpro-btn', function() {
            const btn = $(this);
            if (!btn.hasClass('no-loading')) {
                showButtonLoading(btn);
            }
        });
    }
    
    /**
     * Initialize tooltips
     */
    function initializeTooltips() {
        $('[data-tooltip]').each(function() {
            const element = $(this);
            const tooltip = element.data('tooltip');
            
            element.on('mouseenter', function() {
                showTooltip(element, tooltip);
            }).on('mouseleave', function() {
                hideTooltip();
            });
        });
    }
    
    /**
     * Initialize search functionality
     */
    function initializeSearch() {
        let searchTimeout;
        
        $(document).on('input', '.tailorpro-search', function() {
            const query = $(this).val();
            const target = $(this).data('search-target');
            
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(query, target);
            }, 500);
        });
        
        $(document).on('keyup', '.tailorpro-search', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = $(this).val();
                const target = $(this).data('search-target');
                performSearch(query, target, true);
            }
        });
    }
    
    /**
     * Initialize theme toggle
     */
    function initializeThemeToggle() {
        $(document).on('click', '#theme-toggle', function() {
            toggleTheme();
        });
    }
    
    /**
     * Setup event handlers
     */
    function setupEventHandlers() {
        // AJAX setup
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                // Add nonce to all AJAX requests
                if (settings.type === 'POST' && !settings.data.includes('nonce=')) {
                    const nonce = tailorpro_ajax.nonce;
                    settings.data += (settings.data ? '&' : '') + 'nonce=' + nonce;
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showAlert('error', tailorpro_ajax.strings.error);
            }
        });
        
        // Print functionality
        $(document).on('click', '.tailorpro-print', function(e) {
            e.preventDefault();
            window.print();
        });
        
        // Export functionality
        $(document).on('click', '.tailorpro-export', function(e) {
            e.preventDefault();
            const type = $(this).data('export-type');
            exportData(type);
        });
        
        // Import functionality
        $(document).on('click', '.tailorpro-import', function(e) {
            e.preventDefault();
            const type = $(this).data('import-type');
            openImportModal(type);
        });
        
        // Demo data installation
        $(document).on('click', '#install-demo-data', function(e) {
            e.preventDefault();
            installDemoData();
        });
    }
    
    /**
     * Theme toggle functionality
     */
    function toggleTheme() {
        currentTheme = currentTheme === 'light' ? 'dark' : 'light';
        $('body').attr('data-theme', currentTheme);
        updateThemeVariables();
        
        // Save preference via AJAX
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_save_settings',
            theme_mode: currentTheme
        });
        
        // Update toggle button
        const toggleBtn = $('#theme-toggle');
        toggleBtn.find('span').text(currentTheme === 'light' ? '🌙' : '☀️');
        toggleBtn.find('.toggle-text').text(currentTheme === 'light' ? 'Dark' : 'Light');
    }
    
    /**
     * Handle customer form submission
     */
    function handleCustomerFormSubmit(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        formData.append('action', 'tailorpro_add_customer');
        
        if (!validateCustomerForm(form)) {
            return false;
        }
        
        $.ajax({
            url: tailorpro_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('success', tailorpro_ajax.strings.success);
                    form[0].reset();
                    closeModal();
                    loadCustomers();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', tailorpro_ajax.strings.error);
            },
            complete: function() {
                hideButtonLoading(form.find('.tailorpro-btn[type="submit"]'));
            }
        });
    }
    
    /**
     * Handle order form submission
     */
    function handleOrderFormSubmit(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        formData.append('action', 'tailorpro_add_order');
        
        if (!validateOrderForm(form)) {
            return false;
        }
        
        $.ajax({
            url: tailorpro_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('success', tailorpro_ajax.strings.success);
                    form[0].reset();
                    closeModal();
                    loadOrders();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', tailorpro_ajax.strings.error);
            },
            complete: function() {
                hideButtonLoading(form.find('.tailorpro-btn[type="submit"]'));
            }
        });
    }
    
    /**
     * Handle settings form submission
     */
    function handleSettingsFormSubmit(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        formData.append('action', 'tailorpro_save_settings');
        
        $.ajax({
            url: tailorpro_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('success', tailorpro_ajax.strings.success);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', tailorpro_ajax.strings.error);
            },
            complete: function() {
                hideButtonLoading(form.find('.tailorpro-btn[type="submit"]'));
            }
        });
    }
    
    /**
     * Load dashboard statistics
     */
    function loadDashboardStats() {
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_get_dashboard_stats'
        }, function(response) {
            if (response.success) {
                updateDashboardStats(response.data);
            }
        });
    }
    
    /**
     * Update dashboard statistics display
     */
    function updateDashboardStats(stats) {
        // Update stat cards
        $('.stat-number').each(function() {
            const statType = $(this).data('stat');
            const value = stats[statType] || 0;
            $(this).text(formatNumber(value));
        });
        
        // Update charts if needed
        updateCharts(stats);
    }
    
    /**
     * Load customers list
     */
    function loadCustomers() {
        const search = $('.tailorpro-search').val() || '';
        
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_get_customers',
            search: search,
            limit: 20,
            offset: 0
        }, function(response) {
            if (response.success) {
                renderCustomers(response.data.customers);
            }
        });
    }
    
    /**
     * Render customers table
     */
    function renderCustomers(customers) {
        const tbody = $('.tailorpro-table tbody');
        tbody.empty();
        
        if (customers.length === 0) {
            tbody.html('<tr><td colspan="6" class="text-center">No customers found</td></tr>');
            return;
        }
        
        customers.forEach(function(customer) {
            const row = `
                <tr data-id="${customer.id}">
                    <td>${customer.name}</td>
                    <td>${customer.phone}</td>
                    <td>${customer.email || '-'}</td>
                    <td>${customer.city || '-'}</td>
                    <td>${formatDate(customer.created_at)}</td>
                    <td class="no-print">
                        <div class="tailorpro-btn-group">
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-info" data-edit-customer="${customer.id}">
                                ${tailorpro_ajax.strings.edit}
                            </button>
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-danger" data-delete-customer="${customer.id}" data-confirm="${tailorpro_ajax.strings.confirm_delete}">
                                ${tailorpro_ajax.strings.delete}
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    /**
     * Load orders list
     */
    function loadOrders() {
        const search = $('.tailorpro-search').val() || '';
        const status = $('.order-status-filter').val() || '';
        
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_get_orders',
            search: search,
            status: status,
            limit: 20,
            offset: 0
        }, function(response) {
            if (response.success) {
                renderOrders(response.data.orders);
            }
        });
    }
    
    /**
     * Render orders table
     */
    function renderOrders(orders) {
        const tbody = $('.tailorpro-table tbody');
        tbody.empty();
        
        if (orders.length === 0) {
            tbody.html('<tr><td colspan="8" class="text-center">No orders found</td></tr>');
            return;
        }
        
        orders.forEach(function(order) {
            const statusClass = `tailorpro-badge-${order.status.replace('_', '-')}`;
            const isOverdue = new Date(order.due_date) < new Date() && !['completed', 'cancelled'].includes(order.status);
            
            const row = `
                <tr data-id="${order.id}" class="${isOverdue ? 'overdue' : ''}">
                    <td>${order.order_number}</td>
                    <td>${order.customer_name}</td>
                    <td>${formatDate(order.order_date)}</td>
                    <td>${formatDate(order.due_date)}</td>
                    <td>$${parseFloat(order.total_amount).toFixed(2)}</td>
                    <td>$${parseFloat(order.paid_amount).toFixed(2)}</td>
                    <td><span class="tailorpro-badge ${statusClass}">${order.status.replace('_', ' ').toUpperCase()}</span></td>
                    <td class="no-print">
                        <div class="tailorpro-btn-group">
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-info" data-edit-order="${order.id}">
                                ${tailorpro_ajax.strings.edit}
                            </button>
                            <button type="button" class="tailorpro-btn tailorpro-btn-sm tailorpro-btn-danger" data-delete-order="${order.id}" data-confirm="${tailorpro_ajax.strings.confirm_delete}">
                                ${tailorpro_ajax.strings.delete}
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    /**
     * Perform search
     */
    function performSearch(query, target, immediate = false) {
        if (target === 'customers') {
            loadCustomers();
        } else if (target === 'orders') {
            loadOrders();
        }
    }
    
    /**
     * Export data
     */
    function exportData(type) {
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
    
    /**
     * Open import modal
     */
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
                        showAlert('success', response.message);
                        closeModal();
                        if (type === 'customers') loadCustomers();
                        if (type === 'orders') loadOrders();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', tailorpro_ajax.strings.error);
                }
            });
        });
    }
    
    /**
     * Install demo data
     */
    function installDemoData() {
        if (!confirm('This will install demo data. Continue?')) return;
        
        $.post(tailorpro_ajax.ajax_url, {
            action: 'tailorpro_install_demo_data'
        }, function(response) {
            if (response.success) {
                showAlert('success', response.message);
                location.reload();
            } else {
                showAlert('error', response.message);
            }
        });
    }
    
    /**
     * Open modal
     */
    function openModal(modalId) {
        const modal = $('#' + modalId);
        if (modal.length) {
            modal.addClass('show');
            modal.find('[data-autofocus]').focus();
        }
    }
    
    /**
     * Close modal
     */
    function closeModal() {
        $('.tailorpro-modal').removeClass('show');
        setTimeout(() => {
            $('.tailorpro-modal').remove();
        }, 300);
    }
    
    /**
     * Validate customer form
     */
    function validateCustomerForm(form) {
        let isValid = true;
        
        // Clear previous errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Required fields
        form.find('input[required], textarea[required], select[required]').each(function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (!value) {
                showFieldError(field, 'This field is required');
                isValid = false;
            }
        });
        
        // Email validation
        const email = form.find('input[type="email"]');
        if (email.length && email.val() && !isValidEmail(email.val())) {
            showFieldError(email, 'Please enter a valid email address');
            isValid = false;
        }
        
        return isValid;
    }
    
    /**
     * Validate order form
     */
    function validateOrderForm(form) {
        let isValid = true;
        
        // Clear previous errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Required fields
        form.find('input[required], textarea[required], select[required]').each(function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (!value) {
                showFieldError(field, 'This field is required');
                isValid = false;
            }
        });
        
        // Date validation
        const orderDate = new Date(form.find('input[name="order_date"]').val());
        const dueDate = new Date(form.find('input[name="due_date"]').val());
        
        if (dueDate <= orderDate) {
            showFieldError(form.find('input[name="due_date"]'), 'Due date must be after order date');
            isValid = false;
        }
        
        return isValid;
    }
    
    /**
     * Show field validation error
     */
    function showFieldError(field, message) {
        field.addClass('is-invalid');
        field.after(`<div class="invalid-feedback">${message}</div>`);
    }
    
    /**
     * Show button loading state
     */
    function showButtonLoading(button) {
        button.prop('disabled', true);
        button.data('original-text', button.html());
        button.html('<span class="tailorpro-loading"></span> Loading...');
    }
    
    /**
     * Hide button loading state
     */
    function hideButtonLoading(button) {
        button.prop('disabled', false);
        const originalText = button.data('original-text');
        if (originalText) {
            button.html(originalText);
        }
    }
    
    /**
     * Show alert message
     */
    function showAlert(type, message) {
        const alertClass = `tailorpro-alert-${type}`;
        const alertHtml = `
            <div class="tailorpro-alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        $('.tailorpro-alert').remove();
        
        // Add new alert
        $('.tailorpro-container').prepend(alertHtml);
        
        // Auto-dismiss success alerts
        if (type === 'success') {
            setTimeout(() => {
                $('.tailorpro-alert-success').fadeOut();
            }, 3000);
        }
    }
    
    /**
     * Show tooltip
     */
    function showTooltip(element, text) {
        const tooltip = $(`<div class="tailorpro-tooltip">${text}</div>`);
        $('body').append(tooltip);
        
        const elementPos = element.offset();
        tooltip.css({
            top: elementPos.top - tooltip.outerHeight() - 5,
            left: elementPos.left + (element.outerWidth() / 2) - (tooltip.outerWidth() / 2)
        });
    }
    
    /**
     * Hide tooltip
     */
    function hideTooltip() {
        $('.tailorpro-tooltip').remove();
    }
    
    /**
     * Format phone number
     */
    function formatPhoneNumber(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 6) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 3) {
            value = value.replace(/(\d{3})(\d{3})/, '($1) $2');
        }
        e.target.value = value;
    }
    
    /**
     * Validate email
     */
    function validateEmail(e) {
        const email = e.target.value;
        const field = $(e.target);
        
        if (email && !isValidEmail(email)) {
            field.addClass('is-invalid');
            if (!field.next('.invalid-feedback').length) {
                field.after('<div class="invalid-feedback">Please enter a valid email address</div>');
            }
        } else {
            field.removeClass('is-invalid');
            field.next('.invalid-feedback').remove();
        }
    }
    
    /**
     * Validate field on blur
     */
    function validateField(e) {
        const field = $(e.target);
        const value = field.val().trim();
        
        if (!value) {
            field.addClass('is-invalid');
        } else {
            field.removeClass('is-invalid');
        }
    }
    
    /**
     * Clear validation errors
     */
    function clearValidationErrors(e) {
        $(e.target).removeClass('is-invalid').next('.invalid-feedback').remove();
    }
    
    /**
     * Utility functions
     */
    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }
    
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString();
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function updateCharts(stats) {
        // Implement chart updates if needed
        // This would integrate with Chart.js for dashboard visualizations
    }
    
    // Global functions for callbacks
    window.tailorPro = {
        loadCustomers: loadCustomers,
        loadOrders: loadOrders,
        showAlert: showAlert,
        openModal: openModal,
        closeModal: closeModal
    };
    
})(jQuery);
