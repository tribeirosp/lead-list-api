<?php
/*
Plugin Name: Lead List API
Description: Este plugin cria uma API para receber leads 
Version: 1.0
*/
/** Prevent direct access */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Função para processar os dados do lead enviados via API.
 */
function api_post_data_lead($request)
{
    // Recupera os valores enviados pelo formulário
    $dados_form = $request->get_params();

    // Verifica se os dados foram enviados em formato JSON
    if (!is_array($dados_form)) {
        $response = new WP_REST_Response('Dados inválidos, verifique se os dados estão no formato JSON', 400);
        return $response;
    }

    // Verifica se os campos obrigatórios estão presentes no JSON
    $campos_obrigatorios = array('nome', 'email');
    foreach ($campos_obrigatorios as $campo) {
        if (!array_key_exists($campo, $dados_form)) {
            $response = new WP_REST_Response('Campos obrigatórios nome e email estão ausentes', 400);
            return $response;
        }
    }

    // Processa os dados e atualiza ou insere o lead no banco de dados
    $resultado = processar_lead($dados_form);
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
function processar_lead($data_lead)
{
    global $wpdb;
    $table_name = $wpdb->prefix . LEADLISTAPI_DB_TABLE;

    try {
        // Obter dados do JSON
        $nome = sanitize_text_field($data_lead['nome']);
        $email = isset($data_lead['email']) ? filter_var($data_lead['email'], FILTER_VALIDATE_EMAIL) : '';
        $telefone = isset($data_lead['telefone']) ? sanitize_text_field($data_lead['telefone']) : '';
        $estado = isset($data_lead['estado']) ? sanitize_text_field($data_lead['estado']) : '';
        $cidade = isset($data_lead['cidade']) ? sanitize_text_field($data_lead['cidade']) : '';

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
            if ($existing_lead->nome !== $nome) {
                $dados_atualizados['nome'] = $nome;
            }
            if ($existing_lead->telefone !== $telefone) {
                $dados_atualizados['telefone'] = $telefone;
            }
            if ($existing_lead->estado !== $estado) {
                $dados_atualizados['estado'] = $estado;
            }
            if ($existing_lead->cidade !== $cidade) {
                $dados_atualizados['cidade'] = $cidade;
            }
            if (!empty($dados_atualizados)) {
                $wpdb->update($table_name, $dados_atualizados, array('email' => $email));
                return 'Lead atualizado';
            } else {
                return 'Nenhum dado foi modificado';
            }
        } else {
            // Se o lead não existe, inserir um novo cadastro
            $wpdb->insert(
                $table_name,
                array(
                    'nome' => $nome,
                    'email' => $email,
                    'telefone' => $telefone,
                    'estado' => $estado,
                    'cidade' => $cidade
                )
            );
            return 'Novo lead inserido';
        }
    } catch (PDOException $e) {
        // Tratamento de erro em caso de exceção relacionada ao banco de dados
        return 'Erro de banco de dados: ' . $e->getMessage();
    } catch (Exception $e) {
        // Tratamento de erro em caso de exceção genérica
        return 'Erro ao processar os dados do lead: ' . $e->getMessage();
    }
}