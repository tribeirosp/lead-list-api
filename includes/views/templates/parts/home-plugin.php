<?php
/**
 * leads table template
 *  
 */
?> 
<div class="lead-lest-api">

<?php include LEADLISTAPI_DIR_PATH . '/includes/views/templates/parts/menu.php'; ?>


<div class="wrap">
    <h1><?php _e( 'Página Inicial', 'lead-list-api' ); ?></h1>
    <p><?php _e( 'Instruções de uso', 'lead-list-api' ); ?></p>
    <ol>
        <li><?php _e( 'Gerar um token de acesso', 'lead-list-api' ); ?> - <a href="<?php echo esc_url( admin_url( 'admin.php?page=lead-list-api-token' ) ); ?>" title="<?php _e( 'Gerar Token', 'lead-list-api' ); ?>"><?php _e( 'Gerar Token', 'lead-list-api' ); ?></a></li>
        <li><?php _e( 'Enviar dados do lead usando as informações abaixo:', 'lead-list-api' ); ?></li>
    </ol>
</div>

    <h3><b><?php _e( 'EndPoint', 'lead-list-api' ); ?></b><br /></h3>
    <p>
        <b><?php _e( 'Método POST: ', 'lead-list-api' ); ?> </b>
        <?php echo esc_url( home_url( 'wp-json/lead-list-api/v1/integration' ) ); ?>
    <p>
    <p><b><?php _e( 'Content-Type: ', 'lead-list-api' ); ?></b><?php _e( 'application/json', 'lead-list-api' ); ?></p>
    <p><b><?php _e( 'Authorization: Bearer ', 'lead-list-api' ); ?></b> <?php _e( 'your-token-xxxxx', 'lead-list-api' ); ?></p>
    <p><b><?php _e( 'Dados no formato Json ex:', 'lead-list-api' ); ?></b>
    <pre> 
        {
        "name": "teste",
        "email": "teste@teste.com",
        "telephone": "11 99999999",
        "state": "xx",
        "city": "xx",
        "page_conversion": "www.site.com.br/xyz"
        }
    </pre>
    </p>




<h3><?php _e( 'Exemplo de como enviar as informações usado o cURL do PHP:', 'lead-list-api' ); ?></h3>
<pre>
<code>
        $url = <?php echo esc_url( home_url( 'wp-json/lead-list-api/v1/integration' ) ); ?>

        $token = 'xxxxxxyyyyyyyzzzzz';
        $data = array(
            'name' => 'teste',
            'email' => 'teste@teste.com',
            'telephone' => '11 99999999',
            'state' => 'xx',
            'city' => 'xx',
            'page_conversion' => 'www.site.com.br/xyz'
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response;
        exit();
        </code>
</pre>
</div>