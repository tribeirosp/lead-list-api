<?php
/**
 * token page
 */
?> 
<?php   include LEADLISTAPI_DIR_PATH . '/app/view/templates/parts/menu.php';  ?>

<div class="wrap">
    <h1><?php _e( 'Gerar Token', 'lead-list-api' ); ?></h1>
    <p><?php _e( 'Para usar a API é nescessario gerar um tokem de acesso', 'lead-list-api' ); ?></p>
    <form method="post" action="<?php echo admin_url('admin.php'); ?>">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="token_name"><?php _e( 'Nome do Token', 'lead-list-api' ); ?></label></th>
                <td><input type="text" id="token_name" name="token_name" class="regular-text" required></td>
            </tr>
        </table>
        <input type="hidden" name="action" value="save_token">
        <?php wp_nonce_field( 'save_token_nonce', 'save_token_nonce_field' ); ?>
        <?php submit_button( __( 'Gerar Token', 'lead-list-api' ), 'primary', 'submit_token' ); ?>
    </form>
</div>


<div id="leadlistapi-settings-page" class="wrap">
    <?php
        // Include lead model
        require_once LEADLISTAPI_DIR_PATH . '/app/model/lead-model.php';
        Admin_Model::show_token(); 
    ?>
</div>





