<?php
/**
 * form edit fields
 * page: page-manage-fields.php
 */
?> 
<div class="lead-lest-api">
<?php include LEADLISTAPI_DIR_PATH . '/includes/views/templates/parts/menu.php';  ?>
    <div class="wrap">
         <h1><?php _e( 'Gerenciar Campos', 'lead-list-api' ); ?></h1>
        <h2><?php _e( 'Adicione, edite e removas campos da api', 'lead-list-api' ); ?></h2>
    
       <hr />
       <form method="post" action="<?php echo admin_url('admin.php'); ?>">
            <div class="form-token">
                <ul>
                    <li><label for="new_field_name"><?php _e( 'Nome do novo campo', 'lead-list-api' ); ?></label></li>
                    <li><input type="text" id="new_field_name" name="new_field_name" class="regular-text" required></li>
                    <li>
                        <input type="hidden" name="action" value="add_field">
                        <?php wp_nonce_field( 'add_field_nonce', 'add_field_nonce_field' ); ?>
                        <?php submit_button( __( 'Adicionar Campo', 'lead-list-api' ), 'page-title-action', 'submit_add_field' ); ?>
                    </li>
                </ul>
            </div>
        </form>


        <p><?php _e( 'Campos existentes', 'lead-list-api' ); ?></p>
       
        <ul class="edit-field">
    <?php foreach ($fields as $field) {
        echo '<li>';
        echo '<span>' . $field->Field . '</span>'; // Nome do campo
        if (!in_array($field->Field, ['lead_id', 'name', 'email', 'state', 'city', 'telephone'])) {
            // Formulário para editar e excluir o campo
            echo '<form method="post">';
            echo '<input type="hidden" name="field_name" value="' . $field->Field . '">';
            echo '<input type="text" name="new_field_name" value="' . $field->Field . '" required pattern="[A-Za-z0-9_]+" title="Por favor, insira apenas letras (sem acentos), números e sublinhados.">';
            echo wp_nonce_field( 'edit_field_nonce', 'edit_field_nonce_field' );
            echo submit_button( 'Renomear', 'primary', 'submit_edit_field' );
            echo '</form>';
            
            echo '<form method="post">';
            echo '<input type="hidden" name="field_name" value="' . $field->Field . '">';
            echo wp_nonce_field( 'delete_field_nonce', 'delete_field_nonce_field' );
            echo submit_button( 'Excluir', 'primary', 'submit_delete_field' );
            echo '</form>';
        }
        echo '</li>';
    } ?>
</ul>

    </div>
</div>