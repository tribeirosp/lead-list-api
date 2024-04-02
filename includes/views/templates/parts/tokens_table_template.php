<?php
/**
 * table leads admin page
 *  
 */
?> 
        
<div class="lead-lest-api">

<?php   include LEADLISTAPI_DIR_PATH . '/includes/views/templates/parts/menu.php';  ?>

<div class="wrap">
    <h1><?php _e( 'Gerar Token', 'lead-list-api' ); ?></h1>
    <p><?php _e( 'Para usar a API é nescessario gerar um tokem de acesso', 'lead-list-api' ); ?></p>
    <form method="post" action="<?php echo admin_url('admin.php'); ?>">
        <div class="form-token">
            <ul>
                <li><label for="token_name"><?php _e( 'Nome do Token', 'lead-list-api' ); ?></label></li>
                <li><input type="text" id="token_name" name="token_name" class="regular-text" required></li>
                <li>
                    <input type="hidden" name="action" value="save_token">
                    <?php wp_nonce_field( 'save_token_nonce', 'save_token_nonce_field' ); ?>
                    <?php submit_button( __( 'Gerar Token', 'lead-list-api' ), 'primary', 'submit_token' ); ?>
                </li>
            </ul>
        </div>

    </form>
</div>



<?php if (empty($tokens)) {  
  echo '<p>' . __('Nenhum tokens encontrado', 'lead-list-api') . '</p>'; return;  ?>
 <?php } else { ?> 

    <form method="post" action="<?php echo admin_url('admin.php'); ?>">
    <button type="submit" class="delete-token button button-primary">Excluir Token Selecionado</button> <!-- Botão de envio para excluir tokens selecionados -->

    <input type="hidden" name="action" value="delete_selected_tokens"> <!-- Ação para indicar a exclusão de tokens selecionados -->
    <input type="hidden" name="paged" value="<?php echo isset($_GET['paged']) ? $_GET['paged'] : 1; ?>">

    <table class="leads-list-teble " style="border-collapse: collapse; width: 100%;">
        <!-- Cabeçalho da tabela -->
        <tr>
            <th style="border: 1px solid black; padding: 8px;">Excluir</th> <!-- Cabeçalho para o checkbox -->
            <th style="border: 1px solid black; padding: 8px;">ID</th>
            <th style="border: 1px solid black; padding: 8px;">Token Name</th>
            <th style="border: 1px solid black; padding: 8px;">Token Bearer</th>
       
            <th style="border: 1px solid black; padding: 8px;">Data de criação</th>
        </tr>

        <!-- Linhas da tabela -->
        <?php foreach ($tokens_to_display as $row) : ?>
            <tr>
                <td style="border: 1px solid black; padding: 8px;">
                    <input type="checkbox" name="token_ids[]" value="<?php echo $row['id_token']; ?>">
                </td> <!-- Campo de checkbox para marcar o token -->
                <td style="border: 1px solid black; padding: 8px;"><?php echo $row['id_token']; ?></td>
                <td style="border: 1px solid black; padding: 8px;"><?php echo $row['token_name']; ?></td>
                <td style="border: 1px solid black; padding: 8px;"><?php echo $row['token']; ?></td>
               
                <td style="border: 1px solid black; padding: 8px;"><?php echo $row['generation_date']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</form>

<?php  } ?>
 


<!-- Exibição da paginação -->
<?php if ($total_pages > 1) : ?>
    <br />
    <div class="pagination">
        <?php echo paginate_links($pagination_args); ?>
    </div>
<?php endif; ?>

</div>