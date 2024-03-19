<?php
/*
Plugin Name: Lead List API
Description: Este plugin cria uma API para receber leads 
Version: 1.0
Author: Thiago Ribeiro
*/
// Inicia a sessão no início do arquivo PHP
session_start();
/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Classe Lead_Model
 * Responsável por gerenciar as operações relacionadas aos leads no banco de dados.
 */
class Lead_Model {

    // cria a tabela 'leadlistapi'
    public static function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE;
        // Verifica se a tabela já existe no banco de dados
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            $sql = "CREATE TABLE $table_name (
             idlead INT NOT NULL AUTO_INCREMENT,
             nome VARCHAR(255) NOT NULL,
             email VARCHAR(255) NOT NULL,
             estado VARCHAR(2),
             cidade VARCHAR(255),
             telefone VARCHAR(255),
             PRIMARY KEY (idlead)
            ) ENGINE=MyISAM $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
        }
    }

    // cria a tabela 'leadlistapi_conversion'
    public static function create_conversion_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'leadlistapi_conversion';
        // Verifica se a tabela já existe no banco de dados
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            $sql = "CREATE TABLE $table_name (
             id_conversion INT NOT NULL AUTO_INCREMENT,
             id_lead INT NOT NULL,
             data_conversao DATETIME NOT NULL,
             pagina_conversao VARCHAR(255),
             PRIMARY KEY (id_conversion),
             FOREIGN KEY (id_lead) REFERENCES " . $wpdb->prefix . LEADLISTAPI_DB_TABLE . "(idlead)
            ) ENGINE=MyISAM $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
        }
    }

    // cria a tabela 'leadlistapi_token'
    public static function create_token_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'leadlistapi_token';
        // Verifica se a tabela já existe no banco de dados
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
            $sql = "CREATE TABLE $table_name (
             id_token INT NOT NULL AUTO_INCREMENT,
             token VARCHAR(255) NOT NULL,
             token_name VARCHAR(255) NOT NULL,
             data_geracao DATETIME NOT NULL,
             PRIMARY KEY (id_token)
            ) ENGINE=MyISAM $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
        }
    }
}

/**
* Classe Admin_Model
* Responsável por gerenciar as operações relacionadas a area administrativa.
*/
class Admin_Model {

 
    public static function export_lead_data_to_csv() {
        if ( isset( $_GET['export_lead_data'] ) && $_GET['export_lead_data'] === 'csv' ) {
            global $wpdb;
    
            $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE;
    
            // Nome do arquivo CSV
            $filename = 'lead_data.csv';
    
            // Cabeçalhos do arquivo CSV
            $headers = array(
                'ID',
                'Nome',
                'Email',
                'Estado',
                'Cidade',
                'Telefone'
            );
    
            // Abre um arquivo temporário para escrita.
            $arquiv_temp = fopen( 'php://temp', 'w' );
    
            // Define os cabeçalhos apropriados para o download do arquivo
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
    
            // Adiciona a marca de ordem de bytes UTF-8 (BOM)
            fwrite($arquiv_temp, "\xEF\xBB\xBF");
    
            // Escreve o cabeçalho no arquivo CSV
            fputcsv( $arquiv_temp, $headers );
    
            // Define o número fixo de linhas para buscar por vez, isso serve para 
            // não sobrecarregar o servidor em caso de bases muito grandes
            $rows_per_query = 100;
            $offset = 0;
    
            while (true) {
                // Consulta para selecionar um subconjunto dos dados da tabela
                $query = "SELECT * FROM $table_name LIMIT $offset, $rows_per_query";
    
                // Obter resultados da consulta
                $results = $wpdb->get_results( $query, ARRAY_A );
    
                if (!$results) {
                    break;
                }
    
                // Escreve os dados da tabela no arquivo CSV
                foreach ( $results as $row ) {
                    fputcsv( $arquiv_temp, $row );
                }
    
                $offset += $rows_per_query;
            }
    
            // Volta ao início do arquivo
            rewind( $arquiv_temp );
    
            // Saída do conteúdo do arquivo CSV
            fpassthru( $arquiv_temp );
    
            // Fecha o arquivo
            fclose( $arquiv_temp );
    
            exit;
        }
    }

    public static function LeadsData() {
        global $wpdb; // variável $wpdb para a conexão com o banco de dados
    
        $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE;
    
        // SQL para recuperar os dados cadastrados
        $results = $wpdb->get_results("SELECT * FROM $table_name");
    
        $data = array(); // Array para armazenar os dados recuperados
    
        // Iterar pelos resultados e armazenar os dados no array
        foreach ($results as $result) {
            $row = array(); // Array de valores de cada linha
            $row['idlead'] = $result->idlead;
            $row['nome'] = $result->nome;
            $row['email'] = $result->email;
            $row['telefone'] = $result->telefone;
            $row['estado'] = $result->estado;
            $row['cidade'] = $result->cidade;
            $data[] = $row; // Adicionar a linha ao array de dados
        }

        return $data; // Retorna os dados  
    }

    public static function show_leads_admin() {
        $data = Admin_Model::LeadsData();
    
        // número de itens a serem exibidos por página
        $items_per_page = 100;
    
        // número total de páginas
        $total_pages = ceil(count($data) / $items_per_page);
    
        // número da página atual a partir do parâmetro "paged" na URL
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    
        // Calcule o deslocamento para a consulta SQL com base na página atual
        $offset = ($current_page - 1) * $items_per_page;
    
        // Obtenha os itens a serem exibidos na página atual usando o deslocamento e o número de itens por página
        $items_to_display = array_slice($data, $offset, $items_per_page);
    
        // Dados necessários para o template
        $headers = !empty($data[0]) ? array_keys($data[0]) : array();
        $pagination_args = array(
            'base' => esc_url(add_query_arg('paged', '%#%')),
            'format' => '',
            'total' => $total_pages,
            'current' => $current_page,
            'show_all' => false,
            'end_size' => 1,
            'mid_size' => 2,
            'prev_next' => true,
            'prev_text' => __('« Anterior'),
            'next_text' => __('Próximo »'),
        );
    
        // arquivo de template
        include LEADLISTAPI_DIR_PATH . '/app/view/templates/parts/leads_table_template.php';
    }

    public static function delete_selected_leads() {
        if (isset($_POST['action']) && $_POST['action'] === 'delete_selected_leads') {
            global $wpdb;
            $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE;
    
            // IDs dos leads selecionados
            $lead_ids = $_POST['lead_ids'] ?? [];
    
            // número da página atual ou define como 1 se não estiver definido
            $paged = $_POST['paged'] ?? 1;
    
            // Verifica se foram selecionados leads para exclusão
            if (!empty($lead_ids)) {
                foreach ($lead_ids as $lead_id) {
                    $wpdb->delete(
                        $table_name,
                        array('idlead' => $lead_id),
                        array('%d')
                    );
                }
                // mensagem de sucesso
                $_SESSION['admin_notice'] = ['message' => 'Exclusão realizada com sucesso.', 'class' => 'notice-success'];
            } else {
                // mensagem de aviso
                $_SESSION['admin_notice'] = ['message' => 'Nenhum lead foi selecionado para exclusão.', 'class' => 'notice-warning'];
            }
    
            // Redireciona de volta para a mesma página de leads após a exclusão
            wp_redirect(admin_url("admin.php?page=leadlistapi_settings_page&paged=$paged"));
            exit;
        }
    }

    public static function save_token() {

        if (isset($_POST['action']) && $_POST['action'] === 'save_token') {

            if (isset($_POST['save_token_nonce_field']) && wp_verify_nonce($_POST['save_token_nonce_field'], 'save_token_nonce')) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'leadlistapi_token';
    
                 
                if (isset($_POST['token_name'])) {
                    
                    $token_name = sanitize_text_field($_POST['token_name']);
    
                    // Gera automaticamente um token
                    $token = wp_generate_password(32, false);

                    // Insere o token no banco de dados
                    $wpdb->insert(
                        $table_name,
                        array(
                            'token' => $token,
                            'token_name' => $token_name,
                            'data_geracao' => current_time('mysql')
                        )
                    );
                    var_dump($wpdb->last_error);

                    $_SESSION['admin_notice'] = ['message' =>  $wpdb->last_error . 'Token salvo com sucesso. ', 'class' => 'notice-success'];
                } else {
                    $_SESSION['admin_notice'] = ['message' => 'Erro ao salvar o token. Todos os campos são obrigatórios.', 'class' => 'notice-error'];
                }
            } else {
                // Exibe uma mensagem de erro se o nonce não for válido
                $_SESSION['admin_notice'] = ['message' => 'Erro ao salvar o token. Nonce inválido.', 'class' => 'notice-error'];
            }
            // Redireciona de volta para a página de configurações após salvar o token
            wp_redirect(admin_url("admin.php?page=leadlistapi_token_page"));
            exit;
        }
    }

    public static function TokensData() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'leadlistapi_token';
        
        // Execute uma consulta SQL para recuperar os dados dos tokens
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        
        $tokens = array(); // Array para armazenar os dados dos tokens
        
        // Iterar pelos resultados e armazenar os dados no array
        foreach ($results as $result) {
            $token = array(
                'id_token' => $result->id_token,
                'token' => $result->token,
                'token_name' => $result->token_name,
                'data_geracao' => $result->data_geracao
            );
            $tokens[] = $token; // Adicionar o token ao array de tokens
        }
        
        return $tokens; // Retorna os dados dos tokens recuperados do banco de dados
    }
    
    public static function show_token() {
       
            $tokens = Admin_Model::TokensData(); // Função fictícia para recuperar os dados dos tokens
            
            // Número de itens a serem exibidos por página
            $items_per_page = 100;
            
            // Número total de páginas
            $total_pages = ceil(count($tokens) / $items_per_page);
            
            // Número da página atual a partir do parâmetro "paged" na URL
            $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
            
            // Calcule o deslocamento para a consulta com base na página atual
            $offset = ($current_page - 1) * $items_per_page;
            
            // Obtenha os tokens a serem exibidos na página atual usando o deslocamento e o número de itens por página
            $tokens_to_display = array_slice($tokens, $offset, $items_per_page);
            
            // Dados necessários para o template
            $headers = !empty($tokens[0]) ? array_keys($tokens[0]) : array();
            $pagination_args = array(
                'base' => esc_url(add_query_arg('paged', '%#%')),
                'format' => '',
                'total' => $total_pages,
                'current' => $current_page,
                'show_all' => false,
                'end_size' => 1,
                'mid_size' => 2,
                'prev_next' => true,
                'prev_text' => __('« Anterior'),
                'next_text' => __('Próximo »'),
            );
            
            // Arquivo de template para exibir os tokens
            include LEADLISTAPI_DIR_PATH . '/app/view/templates/parts/tokens_table_template.php';
    }
    
    public static function delete_selected_token() {
        if (isset($_POST['action']) && $_POST['action'] === 'delete_selected_tokens') {
            global $wpdb;
            $table_name = $wpdb->prefix . 'leadlistapi_token';
    
            // IDs dos tokens selecionados
            $token_ids = isset($_POST['token_ids']) ? $_POST['token_ids'] : [];
    
            // Número da página atual ou definir como 1 se não estiver definido
            $paged = isset($_POST['paged']) ? $_POST['paged'] : 1;
    
            // Verifica se foram selecionados tokens para exclusão
            if (!empty($token_ids)) {
                foreach ($token_ids as $token_id) {
                    $wpdb->delete(
                        $table_name,
                        array('id_token' => $token_id),
                        array('%d')
                    );
                }
                // Mensagem de sucesso
                $_SESSION['admin_notice'] = ['message' => 'Exclusão realizada com sucesso.', 'class' => 'notice-success'];
            } else {
                // Mensagem de aviso
                $_SESSION['admin_notice'] = ['message' => 'Nenhum token foi selecionado para exclusão.', 'class' => 'notice-warning'];
            }
    
            // Redireciona de volta para a mesma página de tokens após a exclusão
            wp_redirect(admin_url("admin.php?page=leadlistapi_token_page&paged=$paged"));
            exit;
        }
    }
} 


/**
* hook de ação para chamar a função quando necessário
*/
add_action('admin_notices', function() {
    if (isset($_SESSION['admin_notice'])) { $notice = $_SESSION['admin_notice'];
         echo "<div class='notice {$notice['class']} is-dismissible'>
               <p>{$notice['message']}</p></div>";
         // Limpa a mensagem após exibição
         unset($_SESSION['admin_notice']);
    }
});

// Adiciona a ação que liga a função 'save_token' ao gancho 'admin_post_save_token'
add_action('admin_init', array('Admin_Model', 'save_token'));


 
// site.com.br/wp-admin/wp-admin?action=delete_selected_tokens&id=14
add_action('admin_init', array('Admin_Model', 'delete_selected_token'));

// site.com.br/wp-admin/wp-admin?action=delete_lead&id=14
add_action('admin_init', array('Admin_Model', 'delete_selected_leads'));

// site.com.br/wp-admin?export_lead_data=csv
add_action( 'admin_init', array( 'Admin_Model', 'export_lead_data_to_csv' ) );
 

 
