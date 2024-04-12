<?php
/**
 * form edit fields
 *  
 */
?> 
        
<div class="lead-lest-api">

<?php include LEADLISTAPI_DIR_PATH . '/includes/views/templates/parts/menu.php';  ?>

<div class="wrap">
    <h1><?php _e( 'Gerenciar Campos', 'lead-list-api' ); ?></h1>
 
    <h2><?php _e( 'Adicione, edite e removas campos da api', 'lead-list-api' ); ?></h2>

    <h2>Adicionar Novo Campo</h2>
       <form method="post">
       <label for="new_field_name">Nome do Novo Campo:</label>
       <input type="text" id="new_field_name" name="new_field_name" required pattern="[A-Za-z0-9_]+" title="Por favor, insira apenas letras (sem acentos), números e sublinhados.">
       <button type="submit" class="page-title-action" name="add_field">Adicionar Campo</button>
       </form>

       <p><?php _e( 'Campos existentes', 'lead-list-api' ); ?></p>
       
        <ul class="edit-field">
        <?php foreach ($fields as $field) {
            echo '<li>';
            echo '<span>' . $field->Field . '</span>'; // Nome do campo
            if (!in_array($field->Field, ['lead_id', 'name', 'email', 'state', 'city', 'telephone'])) {
                // Formulário para editar e excluir o campo
                echo '<form method="post" onsubmit="return confirm(\'Tem certeza que deseja modificar este campo?\');">';
                echo '<input type="hidden" name="field_name" value="' . $field->Field . '">';
                echo '<input type="text" name="new_field_name" value="' . $field->Field . '" required pattern="[A-Za-z0-9_]+" title="Por favor, insira apenas letras (sem acentos), números e sublinhados.">';
                echo '<button type="submit" name="action"  class="button button-primary" value="edit_field">Renomear</button>';
                echo '<button type="submit" name="action" class="button button-primary" value="delete_field">Excluir</button>';
                echo '</form>';
            }
            echo '</li>';
        } ?>
        </ul>
    
     
   
</div>


 
</div>