<?php
/*
Plugin Name: Lead List API
Description: Este plugin cria uma API para receber leads 
Version: 1.0
Author: Thiago Ribeiro
*/

/**
* Conteúdo da página de configurações do plugin.
*/

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Função para exibir a página de configurações do plugin no painel de administração.
 */
function leadlistapi_settings_page_cb() {
    require_once LEADLISTAPI_TEMPLATE_PATH . 'admin.php';
}

 