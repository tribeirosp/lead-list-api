<?php
/*
Plugin Name: Lead List API
Description: Este plugin cria uma API para receber leads 
Version: 1.0
Author: Thiago Ribeiro
*/

// Se este arquivo for acessado diretamente, aborte.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Constantes globais  */ 
define( 'LEADLISTAPI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LEADLISTAPI_DIR_URL', plugin_dir_url(__FILE__) ); 
define( 'LEADLISTAPI_DOMAIN', 'leadlistapi' );  
define( 'LEADLISTAPI_DIR_PATH', dirname( __FILE__ ) );  
define( 'LEADLISTAPI_DB_TABLE_LEAD',       'lead_list_api' );   
define( 'LEADLISTAPI_DB_TABLE_CONVERSION', 'lead_list_api_conversion');   
define( 'LEADLISTAPI_DB_TABLE_TOKEN',      'lead_list_api_token');   
define( 'LEADLISTAPI_TEMPLATE_PATH', LEADLISTAPI_DIR_PATH . '/includes/views/templates/' ); 


// Outros arquivos do plugin
require_once LEADLISTAPI_DIR_PATH . '/admin/lead-list-api-admin.php';
 

require_once LEADLISTAPI_DIR_PATH . '/includes/controllers/lead-controller.php';


require_once LEADLISTAPI_DIR_PATH . '/includes/models/lead-model.php';
require_once LEADLISTAPI_DIR_PATH . '/includes/models/admin-model.php';

// Função para criar as tabelas durante a ativação do plugin
function lead_list_api_activation() {
    if (method_exists('Lead_Model', 'create_table')) {
       Lead_Model::create_table();
   }

    if (method_exists('Lead_Model', 'create_conversion_table')) {
       Lead_Model::create_conversion_table();
   }

 
   if (method_exists('Lead_Model', 'create_token_table')) {
       Lead_Model::create_token_table();
   }
}

// Registra a função de ativação do plugin
register_activation_hook(__FILE__, 'lead_list_api_activation');