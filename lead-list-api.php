<?php
/*
Plugin Name: Lead List API
Description: O plugin Lead List API  cria uma API RESTful para receber e gerenciar leads diretamente do seu site WordPress. 
Version: 1.0
Author:  Thiago Ribeiro
*/

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Constantes globais  */
define( 'LEADLISTAPI_DOMAIN', 'leadlistapi' );  
define( 'LEADLISTAPI_DIR_PATH', dirname( __FILE__ ) );  
define( 'LEADLISTAPI_TEMPLATE_PATH', LEADLISTAPI_DIR_PATH . '/app/view/templates/' ); 
define( 'LEADLISTAPI_DB_TABLE', 'leadlist_api' );   
define( 'LEADLISTAPI_DIR_URL', plugin_dir_url(__FILE__) ); 

// Include lead controller
require_once LEADLISTAPI_DIR_PATH . '/app/controller/lead_controller.php';

// Include lead model
require_once LEADLISTAPI_DIR_PATH . '/app/model/lead-model.php';

// Include settings view
require_once LEADLISTAPI_DIR_PATH . '/app/view/home.php';

// Include rest API endpoint
require_once LEADLISTAPI_DIR_PATH . '/app/api/lead_api.php';


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