<?php
/*
Plugin Name: Lead List API
Description: Este plugin cria uma API para receber leads 
Version: 1.0
Author: Thiago Ribeiro
*/
/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Função para exibir as páginas de configurações do plugin no painel de administração.
 */
function leadlistapi_main_page_cb() {
     include LEADLISTAPI_DIR_PATH . '/app/view/templates/parts/menu.php';   
     echo"<h2>aaaa</h2>";
}
function leadlistapi_leads_page_cb() {
    require_once LEADLISTAPI_TEMPLATE_PATH . 'show-leads.php';
}
function leadlistapi_token_page_cb() {
    require_once LEADLISTAPI_TEMPLATE_PATH . 'token.php';
}