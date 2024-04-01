<?php
/**
 * Lead model for Lead List API
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Register the Endpoint to receive leads.
 * /wp-json/lead-list-api/v1/integration
 */
add_action('rest_api_init', function () {
    register_rest_route('lead-list-api/v1', '/integration', array(
        'methods' => 'POST',
        'callback' => 'validate_data_api',
        'permission_callback' => 'verify_api_token', // Verifies the token before running the endpoint
    ));

    // Chamada à função para verificar o limite de taxa antes de processar a solicitação
    if (!check_rate_limit()) {
        // Se o limite de taxa for excedido, retorne uma resposta de erro
        $response = new WP_REST_Response('Limite de taxa de solicitações excedido', 429); // 429 Too Many Requests
        $response->set_headers(array('Retry-After' => 1)); // Indica quando o cliente deve tentar novamente (em segundos)
        wp_send_json_error($response);
    }
});

/**
 * Função para limitar o número de envios por cliente.
 */
$request_counts = array();
function check_rate_limit()
{
    $client_ip = $_SERVER['REMOTE_ADDR'];
    $current_time = time();
    global $request_counts;

    // Limite de 3 solicitações por cliente dentro de 1 segundo
    $rate_limit = 3;
    $interval = 1; // segundos

    // Se o cliente ainda não tiver nenhuma entrada, inicialize-a
    if (!isset($request_counts[$client_ip])) {
        $request_counts[$client_ip] = array($current_time);
        return true; // Permite a solicitação
    }

    // Remova as solicitações mais antigas que estão fora do intervalo
    while (!empty($request_counts[$client_ip]) && $request_counts[$client_ip][0] < $current_time - $interval) {
        array_shift($request_counts[$client_ip]);
    }

    // Verifique se o número de solicitações está dentro do limite
    if (count($request_counts[$client_ip]) < $rate_limit) {
        // Adicione a nova solicitação à lista
        $request_counts[$client_ip][] = $current_time;
        return true; // Permite a solicitação
    } else {
        return false; // Excede o limite de taxa
    }
}
 

/**
 * Função para fazer o pre-processamento dos dados recebidos via API.
 */   
function validate_data_api($request)
{
    // Recupera os valores enviados pelo formulário
    $dados_form = $request->get_params();

    // Verifica se os dados foram enviados em formato JSON
    if (!is_array($dados_form)) {
        $response = new WP_REST_Response('Dados inválidos, verifique se os dados estão no formato JSON', 400);
        return $response;
    }

    // Verifica se os campos obrigatórios estão presentes no JSON
    $campos_obrigatorios = array('name', 'email');
    foreach ($campos_obrigatorios as $campo) {
        if (!array_key_exists($campo, $dados_form)) {
            $response = new WP_REST_Response('Campos obrigatórios nome e email estão ausentes', 400);
            return $response;
        }
    }

    // Processa os dados e atualiza ou insere o lead no banco de dados
    $resultado = send_lead_bd($dados_form);
    if ($resultado) {
        $response = new WP_REST_Response($resultado, 200);
    } else {
        $response = new WP_REST_Response('Falha ao processar os dados do lead', 500);
    }
    return $response;
}

/**
 * Função para salvar os dados do lead.
 */
function send_lead_bd($data_lead) {
    global $wpdb;
    $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_LEAD;
    $token_table_name =  $wpdb->prefix . LEADLISTAPI_DB_TABLE_TOKEN;
    // Extrai o token do cabeçalho e remove o 'Bearer'
    $get_sent_token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
    // Consulta o banco de dados para verificar o token name
    $token_name = $wpdb->get_var($wpdb->prepare("SELECT token_name FROM $token_table_name WHERE token = %s", $get_sent_token));
    try {
        // Obter dados do JSON
        $name = sanitize_text_field($data_lead['name']);
        $email = isset($data_lead['email']) ? filter_var($data_lead['email'], FILTER_VALIDATE_EMAIL) : '';
        $telephone = isset($data_lead['telephone']) ? sanitize_text_field($data_lead['telephone']) : '';
        $state = isset($data_lead['state']) ? sanitize_text_field($data_lead['state']) : '';
        $city = isset($data_lead['city']) ? sanitize_text_field($data_lead['city']) : '';
        $page_conversion  = isset($data_lead['page_conversion']) ? $data_lead['page_conversion'] : '';

        // Verificar se o e-mail é válido
        if (!$email) {
            $response = new WP_REST_Response('E-mail inválido', 400);
            return $response;
        }

        // Verificar se o lead já existe na base de dados
        $existing_lead = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE email = %s", $email));

        if ($existing_lead) {
            // Se o lead já existe, atualizar apenas os campos modificados
            $dados_atualizados = array();
            if ($existing_lead->name !== $name) {
                $dados_atualizados['name'] = $name;
            }
            if ($existing_lead->telephone !== $telephone) {
                $dados_atualizados['telephone'] = $telephone;
            }
            if ($existing_lead->state !== $state) {
                $dados_atualizados['state'] = $state;
            }
            if ($existing_lead->city !== $city) {
                $dados_atualizados['city'] = $city;
            }
            if (!empty($dados_atualizados)) {
                $wpdb->update($table_name, $dados_atualizados, array('email' => $email));
                
                // Inserir dados de conversão  
                insert_conversion_data($existing_lead->idlead, $page_conversion, $token_name); 

                $menssagen =  __('Lead atualizado', 'lead-list-api');
                $response = new WP_REST_Response($menssagen, 200);
                return $response;

            } else {
                 // Inserir dados de conversão 
                insert_conversion_data($existing_lead->idlead, $page_conversion, $token_name);    

                $menssagen =  __('Nenhum dado foi modificado', 'lead-list-api');
                $response = new WP_REST_Response($menssagen, 200);
                return $response;
            
            }
        } else {
            // Se o lead não existe, inserir um novo cadastro
            $wpdb->insert(
                $table_name,
                array(
                    'name' => $name,
                    'email' => $email,
                    'telephone' => $telephone,
                    'state' => $state,
                    'city' => $city 
                )
            );
                // Inserir dados de conversão
                insert_conversion_data($wpdb->insert_id, $page_conversion, $token_name);

                $menssagen =  __('Novo lead inserido', 'lead-list-api');
                $response = new WP_REST_Response($menssagen, 200);
                return $response;

        }
    } catch (Exception $e) {
        // Tratamento de erro em caso de exceção genérica
        $message =  __('Erro ao processar os dados do lead:', 'lead-list-api');
        $response = new WP_REST_Response($message. ' '. $e->getMessage(), 500);
        $response->header('Content-Type', 'application/json');
        return $response;
    }
}

function insert_conversion_data($id_lead, $page_conversion, $token_name) {
  global $wpdb;
  $conversion_table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE_CONVERSION;
    
  $current_datetime = current_time('mysql'); // Obtém a data e hora atual no formato MySQL

  // Separa a data e a hora da data e hora atual
  $current_date = date('Y-m-d', strtotime($current_datetime));
  $current_time = date('H:i:s', strtotime($current_datetime));



  try {
    $wpdb->insert($conversion_table_name, array(
      'id_lead' => $id_lead,
      'data_conversion' => $current_date, // Insere apenas a data
      'time_conversion' => $current_time, // Insere apenas a hora
       'page_conversion' => $page_conversion,
      'token_conversion_name' => $token_name,
    ));
    return true;
  } catch (Exception $e) {

    $message =  __('Erro ao inserir dados de conversão:', 'lead-list-api');
    throw new Exception( $message . $e->getMessage(), 400);

  }
}