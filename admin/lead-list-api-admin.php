<?php
/**
 * Admin functions for Lead List API
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize the admin menu and pages
 */
function lead_list_api_admin_init() {
    add_action('admin_menu', 'lead_list_api_add_admin_menu');
}
/**
 * Add admin menu and pages
 */
function lead_list_api_add_admin_menu() {
    // Add top-level menu page
    add_menu_page(
        __('Lead List API', 'lead-list-api'), // Page title
        __('Lead List API', 'lead-list-api'), // Menu title
        'manage_options', // Capability required to access the menu
        'lead-list-api', // Menu slug
        'lead_list_api_main_page_cb', // Callback function to render the page
        'dashicons-admin-users', // Icon
        30 // Position
    );

    // Add submenu page for leads
    add_submenu_page(
        'lead-list-api', // Parent menu slug
        __('Leads Cadastrados', 'lead-list-api'), // Page title
        __('Leads Cadastrados', 'lead-list-api'), // Menu title
        'manage_options', // Capability required to access the page
        'lead-list-api-leads', // Menu slug
        'lead_list_api_leads_page_cb' // Callback function to render the page
    );

    // Add submenu page for token generation
    add_submenu_page(
        'lead-list-api', // Parent menu slug
        __('Gerar Token', 'lead-list-api'), // Page title
        __('Gerar Token', 'lead-list-api'), // Menu title
        'manage_options', // Capability required to access the page
        'lead-list-api-token', // Menu slug
        'lead_list_api_token_page_cb' // Callback function to render the page
    );

      // Add submenu page for fields
      add_submenu_page(
        'lead-list-api', // Parent menu slug
        __('Gerenciar Campos', 'lead-list-api'), // Page title
        __('Gerenciar Campos', 'lead-list-api'), // Menu title
        'manage_options', // Capability required to access the page
        'lead-list-api-fields', // Menu slug
        'lead_list_api_fields_page_cb' // Callback function to render the page
    );

}


function lead_list_api_admin_styles() {
    // Enfileira o arquivo CSS no back-end do WordPress
    wp_enqueue_style( 'lead-list-api-styles', plugins_url( 'css/styles.css', __FILE__ ), array(), '1.0.0', 'all' );
}
add_action( 'admin_enqueue_scripts', 'lead_list_api_admin_styles' );


/**
 * render the main page
 */
function lead_list_api_main_page_cb() {
  require_once LEADLISTAPI_DIR_PATH . '/includes/models/admin-model.php';
   Admin_Model::show_home_admin(); 
}

/**
 * render the leads page
 */
function lead_list_api_leads_page_cb() {
    require_once LEADLISTAPI_DIR_PATH . '/includes/models/admin-model.php';
    Admin_Model::show_leads_admin(); 
}

/**
 * render the token page
 */
function lead_list_api_token_page_cb() {
        require_once LEADLISTAPI_DIR_PATH . '/includes/models/admin-model.php';
        Admin_Model::show_token(); 

}

function lead_list_api_fields_page_cb() {
    require_once LEADLISTAPI_DIR_PATH . '/includes/models/admin-model.php';
    Admin_Model::show_fields_admin(); 
}

// Initialize the admin menu
lead_list_api_admin_init();