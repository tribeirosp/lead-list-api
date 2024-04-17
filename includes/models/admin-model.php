<?php
/**
 * Lead model for Lead List API
 * Classe Admin_Model
 * Responsável por gerenciar as operações relacionadas a area administrativa.
 */
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
} 
session_start();
 
class Admin_Model {

    public static function export_lead_data_to_csv() {
        if ( isset( $_GET['export_lead_data'] ) && $_GET['export_lead_data'] === 'csv' ) {
            global $wpdb;
    
            $table_leads = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;
            $table_conversion = $wpdb->prefix . LEADLISTAPI_DB_TABLE_CONVERSION;
    
            // Nome do arquivo CSV
            $filename = 'lead_list_api_data.csv';
    
            // Cabeçalhos do arquivo CSV (obtidos dinamicamente)
            $headers = array();
    
            // Obtém os nomes das colunas da tabela de leads
            $columns_leads = $wpdb->get_col( "DESC {$table_leads}", 0 );
    
            // Adicionar os nomes das colunas da tabela de leads ao cabeçalho
            foreach ( $columns_leads as $column ) {
                $headers[] = $column;
            }
    
            // Adicionar as colunas específicas da tabela de conversão ao cabeçalho
            $headers = array_merge($headers, array('Data Conversion', 'Time Conversion', 'Page Conversion'));
    
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
                $query = "SELECT l.*, c.data_conversion, c.time_conversion, c.page_conversion
                          FROM $table_leads l
                          LEFT JOIN $table_conversion c ON l.lead_id = c.id_lead
                          LIMIT $offset, $rows_per_query";
    
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
    
        $table_leads = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;
        $conversion_table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_CONVERSION;
    
        // Consulta para obter o número total de leads
        $total_leads_query = $wpdb->get_var("
            SELECT COUNT(lead_id) 
            FROM $table_leads
        ");
        $total_leads = intval($total_leads_query);
    
        // Obtém os nomes das colunas da tabela de leads
        $columns_leads = $wpdb->get_col( "DESC {$table_leads}", 0 );
    
        // SQL para recuperar os dados cadastrados
        $results = $wpdb->get_results("
            SELECT l.*, MAX(CONCAT(c.data_conversion, ' ', c.time_conversion)) AS last_conversion_datetime, COUNT(c.id_conversion) AS num_conversions
            FROM $table_leads l
            LEFT JOIN $conversion_table_name c ON l.lead_id = c.id_lead
            GROUP BY l.lead_id
            ORDER BY last_conversion_datetime DESC
        ");
    
        $data = array(); // Array para armazenar os dados recuperados
    
        // Iterar pelos resultados e armazenar os dados no array
        foreach ($results as $result) {
            $row = array(); // Array de valores de cada linha
            
            // Adiciona todas as colunas da tabela de leads ao array
            foreach ($columns_leads as $column) {
                $row[$column] = $result->$column;
            }
            $row['last conversion datetime'] = $result->last_conversion_datetime;
            $row['nº conversions'] = $result->num_conversions;
            $data[] = $row; // Adicionar a linha ao array de dados
        }
        return array(
            'leads' => $data,
            'total_leads' => $total_leads // Retorna o número total de leads
        ); 
    }
    
    public static function show_leads_admin() {
        $leads_data = Admin_Model::LeadsData();
        $data = $leads_data['leads']; // Dados dos leads
        $total_leads = $leads_data['total_leads']; // Número total de leads
    
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
        include LEADLISTAPI_DIR_PATH . '/includes/views/templates/parts/leads_table_template.php';
    
        
   
    }
    
    public static function delete_selected_leads() {
        if (isset($_POST['action']) && $_POST['action'] === 'delete_selected_leads') {
            global $wpdb;
            $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;
            $conversion_table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_CONVERSION;
    
            // IDs dos leads selecionados
            $lead_ids = $_POST['lead_ids'] ?? [];
    
            // número da página atual ou define como 1 se não estiver definido
            $paged = $_POST['paged'] ?? 1;
    
            // Verifica se foram selecionados leads para exclusão
            if (!empty($lead_ids)) {
                foreach ($lead_ids as $lead_id) {
                    // Exclui o registro da tabela de conversão associado ao lead
                    $wpdb->delete(
                        $conversion_table_name,
                        array('id_lead' => $lead_id),
                        array('%d')
                    );
    
                    // Exclui o registro da tabela de leads
                    $wpdb->delete(
                        $table_name,
                        array('lead_id' => $lead_id),
                        array('%d')
                    );
                }
                // mensagem de sucesso
                $message =  __('Exclusão realizada com sucesso.', 'lead-list-api');
                $_SESSION['admin_notice'] = ['message' => $message, 'class' => 'notice-success'];
            } else {
                // mensagem de aviso
                $message =  __('Nenhum lead foi selecionado para exclusão.', 'lead-list-api');
                $_SESSION['admin_notice'] = ['message' => $message, 'class' => 'notice-warning'];
            }
    
            // Redireciona de volta para a mesma página de leads após a exclusão
            wp_redirect(admin_url("admin.php?page=lead-list-api-leads&paged=$paged"));
            exit;
        }
    }
       
    public static function show_home_admin() {
    
        include LEADLISTAPI_DIR_PATH . '/includes/views/templates/parts/home-plugin.php';
    }

    public static function generate_example_json() {
        global $wpdb;
        $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;
    
        // Obtém os nomes dos campos da tabela
        $fields = $wpdb->get_results("DESCRIBE $table_name");
    
        $json_data = array();
    
        // Percorre os campos e adiciona ao JSON de exemplo
        foreach ($fields as $field) {
            // Ignora campos específicos, se necessário
            if (in_array($field->Field, ['lead_id'])) {
                continue;
            }
    
            // Adiciona o campo ao JSON de exemplo com um valor de exemplo
            $json_data[$field->Field] = "exemplo_" . str_replace('_', ' ', $field->Field);
        }
    
        // Adiciona o campo "page_conversion" ao JSON de exemplo
        $json_data["page_conversion"] = "www.site.com";
    
        // Converte o array associativo em JSON formatado
        $json_string = json_encode($json_data, JSON_PRETTY_PRINT);
    
        // Exibe o JSON de exemplo
        echo $json_string;
    }
 
    public static function show_fields_admin() {
        global $wpdb;
        $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;
        // Obtenha todos os campos da tabela
        $fields = $wpdb->get_results("DESCRIBE $table_name");
        // Arquivo de template
        include LEADLISTAPI_DIR_PATH . '/includes/views/templates/parts/page-manage-fields.php';
    }
    
    public static function add_field_to_lead_table() {

        if (isset($_POST['action']) && $_POST['action'] === 'add_field') {
    
            if (isset($_POST['add_field_nonce_field']) && wp_verify_nonce($_POST['add_field_nonce_field'], 'add_field_nonce')) {
                global $wpdb;
                $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;
     
                 
                if (isset($_POST['new_field_name'])) {
                    
                    $new_field_name = sanitize_text_field($_POST['new_field_name']);
    
                    // Verifica se o nome do campo é válido
                    if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $new_field_name)) {
                        $message = __('O campo não pode começar com números, conter caracteres especiais, espaços ou acentos.', 'lead-list-api');
                    } elseif (strlen($new_field_name) > 70) {
                        // Verifica se o nome do campo excede 70 caracteres
                        $message = __('O nome do campo não pode ter mais de 70 caracteres.', 'lead-list-api');
                    } else {
                        // Verifica se o campo já existe na tabela
                        $existing_fields = $wpdb->get_results("DESCRIBE $table_name");
                        foreach ($existing_fields as $field) {
                            if ($field->Field == $new_field_name) {
                                $message = __('O campo já existe.', 'lead-list-api');
                                break;
                            }
                        }
                        // Se a mensagem ainda estiver vazia, adiciona o novo campo à tabela
                        if (empty($message)) {
                            $wpdb->query("ALTER TABLE $table_name ADD $new_field_name VARCHAR(255) NULL");
                            $message = __('Campo adicionado com sucesso.', 'lead-list-api');
                        }
                    }
            
                    // Define a mensagem de aviso para ser exibida
                    $_SESSION['admin_notice'] = ['message' => $message, 'class' => 'notice-success'];
                   
                }
            } 
            // Redireciona de volta para a página de configurações após add o campo
            wp_redirect(admin_url("admin.php?page=lead-list-api-fields"));
    
            exit;
        }
    }

    public static function admin_field_to_lead_table() {
        if (isset($_POST['submit_edit_field'])) {
            // Verifica nonce antes de chamar a função de edição
            if (isset($_POST['edit_field_nonce_field']) && wp_verify_nonce($_POST['edit_field_nonce_field'], 'edit_field_nonce')) {
                $field_name = $_POST['field_name'];
                $new_field_name = sanitize_text_field($_POST['new_field_name']);
                if (self::edit_field_in_lead_table($field_name, $new_field_name)) {
                     $message = __('Campo renomeado com sucesso.', 'lead-list-api');
                     $_SESSION['admin_notice'] = ['message' => $message, 'class' => 'notice-success'];
                 } else {
                   $message = __('O nome do campo não pode conter caracteres especiais, espaços ou acentos.', 'lead-list-api');
                   $_SESSION['admin_notice'] = ['message' => $message, 'class' => 'notice-error'];
                 } 
            }
        }
        if (isset($_POST['submit_delete_field'])) {
            // Verifica nonce antes de chamar a função de exclusão
            if (isset($_POST['delete_field_nonce_field']) && wp_verify_nonce($_POST['delete_field_nonce_field'], 'delete_field_nonce')) {
                $field_name = $_POST['field_name'];
                if (self::delete_field_from_lead_table($field_name)) {
                     $message = __('Campo excluído com sucesso.', 'lead-list-api');
                     $_SESSION['admin_notice'] = ['message' => $message, 'class' => 'notice-success'];
                 } else {
                    $message = __('O campo não pode ser excluído.', 'lead-list-api');
                    $_SESSION['admin_notice'] = ['message' => $message, 'class' => 'notice-error'];
                }
            }
        }
    }

    public static function edit_field_in_lead_table($field_name, $new_field_name) {
        global $wpdb;
        $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;

        // Confirmação antes de renomear
        if (preg_match('/^[A-Za-z0-9_]+$/', $new_field_name)) {
            $wpdb->query("ALTER TABLE $table_name CHANGE $field_name $new_field_name VARCHAR(255) NULL");
            return true; // Campo renomeado com sucesso
        } else {
            return false; // Nome do campo inválido
        }
    }
 
    public static function delete_field_from_lead_table($field_name) {
        global $wpdb;
        $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;

        // Confirmação antes de excluir
        if (!in_array($field_name, ['lead_id', 'name', 'email', 'state', 'city', 'telephone'])) {
            $wpdb->query("ALTER TABLE $table_name DROP $field_name");
            return true; // Campo excluído com sucesso
        } else {
            return false; // Campo não pode ser excluído
        }
    }
    
    public static function save_token() {

        if (isset($_POST['action']) && $_POST['action'] === 'save_token') {

            if (isset($_POST['save_token_nonce_field']) && wp_verify_nonce($_POST['save_token_nonce_field'], 'save_token_nonce')) {
                global $wpdb;
                $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_TOKEN;
     
                 
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
                            'generation_date' => current_time('mysql')
                        )
                    );
                     

                    $_SESSION['admin_notice'] = ['message' =>  $wpdb->last_error . 'Token salvo com sucesso. ', 'class' => 'notice-success'];
                } else {
                    $_SESSION['admin_notice'] = ['message' => 'Erro ao salvar o token. Todos os campos são obrigatórios.', 'class' => 'notice-error'];
                }
            } else {
                // Exibe uma mensagem de erro se o nonce não for válido
                $_SESSION['admin_notice'] = ['message' => 'Erro ao salvar o token. Nonce inválido.', 'class' => 'notice-error'];
            }
            // Redireciona de volta para a página de configurações após salvar o token
            wp_redirect(admin_url("admin.php?page=lead-list-api-token"));

            exit;
        }
    }

    public static function TokensData() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_TOKEN;
        
        // Execute uma consulta SQL para recuperar os dados dos tokens
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        
        $tokens = array(); // Array para armazenar os dados dos tokens
        
        // Iterar pelos resultados e armazenar os dados no array
        foreach ($results as $result) {
            $token = array(
                'id_token' => $result->id_token,
                'token' => $result->token,
                'token_name' => $result->token_name,
                'generation_date' => $result->generation_date
            );
            $tokens[] = $token; // Adicionar o token ao array de tokens
        }
        
        return $tokens; // Retorna os dados dos tokens recuperados do banco de dados
    }
    
    public static function show_token() {
       
            $tokens = Admin_Model::TokensData(); // Função  para recuperar os dados dos tokens
          
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
            include LEADLISTAPI_DIR_PATH . '/includes/views/templates/parts/tokens_table_template.php';
    }
    
    public static function delete_selected_token() {
        if (isset($_POST['action']) && $_POST['action'] === 'delete_selected_tokens') {
            global $wpdb;
            $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_TOKEN;
    
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
            wp_redirect(admin_url("admin.php?page=lead-list-api-token&paged=$paged"));
            exit;
        }
    }
} 


/**
* hook de ação para chamar a função de aviso quando necessário
*/
add_action('admin_notices', function() {
    if (isset($_SESSION['admin_notice'])) { $notice = $_SESSION['admin_notice'];
         echo "<div class='notice {$notice['class']} is-dismissible'>
               <p>{$notice['message']}</p></div>";
            // Limpa a mensagem após exibição
            unset($_SESSION['admin_notice']);
    }
});




add_action('admin_init', array('Admin_Model', 'admin_field_to_lead_table'));

 // Adiciona a ação que liga a função 'add_field_to_lead_table' ao gancho 'admin_post_add_field'
add_action('admin_init', array('Admin_Model', 'add_field_to_lead_table'));

// Adiciona a ação que liga a função 'save_token' ao gancho 'admin_post_save_token'
add_action('admin_init', array('Admin_Model', 'save_token'));


// site.com.br/wp-admin/wp-admin?action=delete_selected_tokens&id=14
add_action('admin_init', array('Admin_Model', 'delete_selected_token'));

// site.com.br/wp-admin/wp-admin?action=delete_lead&id=14
add_action('admin_init', array('Admin_Model', 'delete_selected_leads'));

// site.com.br/wp-admin?export_lead_data=csv
add_action( 'admin_init', array( 'Admin_Model', 'export_lead_data_to_csv' ) );