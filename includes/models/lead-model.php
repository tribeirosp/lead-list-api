<?php
/**
 * Lead model for Lead List API
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}
class Lead_Model {

    // cria a tabela 'lead_list_api'
    public static function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;
        // Verifica se a tabela já existe no banco de dados
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            $sql = "CREATE TABLE $table_name (
             lead_id INT NOT NULL AUTO_INCREMENT,
             name VARCHAR(255) NOT NULL,
             email VARCHAR(255) NOT NULL,
             state VARCHAR(255),
             city VARCHAR(255),
             telephone VARCHAR(255),
             PRIMARY KEY (lead_id)
            ) ENGINE=MyISAM $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
        }
    }

    // cria a tabela 'lead_list_api_conversion'
    public static function create_conversion_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix .  LEADLISTAPI_DB_TABLE_CONVERSION;
        // Verifica se a tabela já existe no banco de dados
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            $sql = "CREATE TABLE $table_name (
             id_conversion INT NOT NULL AUTO_INCREMENT,
             id_lead INT NOT NULL,
             data_conversion DATE NOT NULL,
             time_conversion TIME NOT NULL,
             page_conversion VARCHAR(255),
             token_conversion_name VARCHAR(255),
             PRIMARY KEY (id_conversion),
             FOREIGN KEY (id_lead) REFERENCES " . $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD . "(lead_id)
            ) ENGINE=MyISAM $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
        }
    }

    // cria a tabela 'lead_list_api_token'
    public static function create_token_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_TOKEN;
        // Verifica se a tabela já existe no banco de dados
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            $sql = "CREATE TABLE $table_name (
             id_token INT NOT NULL AUTO_INCREMENT,
             token VARCHAR(255) NOT NULL,
             token_name VARCHAR(255) NOT NULL,
             generation_date DATETIME NOT NULL,
             PRIMARY KEY (id_token)
            ) ENGINE=MyISAM $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
        }
    }
}

/**
 * Função para verificar se o token enviado na solicitação corresponde ao token esperado.
 */
function verify_api_token() {
    global $wpdb;
    $table_name = $wpdb->prefix .LEADLISTAPI_DB_TABLE_TOKEN;
    
    // Verifica se o cabeçalho Authorization está presente na solicitação
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $response = new WP_REST_Response(array(
            'error' => 'Token não fornecido',
        ), 401);
        wp_send_json_error($response);
    }

    // Extrai o token do cabeçalho Authorization
    $sent_token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);

    // Consulta o banco de dados para verificar se o token está presente
    $token_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE token = %s", $sent_token));

    // Verifica se o token existe no banco de dados
    if (!$token_exists) {
        $response = new WP_REST_Response(array(
            'error' => 'Token inválido',
        ), 401);
        wp_send_json_error($response);
    }

    return true; // Token válido
}