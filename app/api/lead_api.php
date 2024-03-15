<?php
/*
Plugin Name: Lead List API
Description: Este plugin cria uma API para receber leads 
Version: 1.0
Author:  Thiago Ribeiro
*/
/** Prevent direct access */
if (!defined('ABSPATH')) {
    exit;
}
 
// Register admin menu
add_action( 'admin_menu', 'leadlistapi_admin_menu' );
function leadlistapi_admin_menu() {
    add_options_page(
        'Lead List API',
        'Lead List API',
        'manage_options',
        'leadlistapi_settings_page',
        'leadlistapi_settings_page_cb'
    );
}

/**
 * Registra o Endpoint para receber os leads. 
 * /wp-json/lead-list-api/v1/integration
 */
add_action('rest_api_init', function () {
    register_rest_route('lead-list-api/v1', '/integration', array(
        'methods' => 'POST',
        'callback' => 'api_post_data_lead',
        'permission_callback' => 'verify_api_token', // Verifica o token antes de executar o endpoint
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
 * Função para verificar se o token enviado na solicitação corresponde ao token esperado.
 */
function verify_api_token() {
    $token = '123456789@987654321'; // Seu token personalizado
    // Verifica se o token enviado na solicitação corresponde ao token esperado
    $sent_token = isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : '';
    if ($sent_token !== $token) {
        return false; // Token inválido
    }
    return true; // Token válido
}

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
