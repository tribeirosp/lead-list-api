<?php
/**
 * table leads admin page
 *  
 */
?> 
<form method="post" action="<?php echo admin_url('admin.php'); ?>">
    <button type="submit">Excluir Token Selecionado</button> <!-- Botão de envio para excluir tokens selecionados -->

    <input type="hidden" name="action" value="delete_selected_tokens"> <!-- Ação para indicar a exclusão de tokens selecionados -->
    <input type="hidden" name="paged" value="<?php echo isset($_GET['paged']) ? $_GET['paged'] : 1; ?>">

    <table style="border-collapse: collapse; width: 100%;">
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
               
                <td style="border: 1px solid black; padding: 8px;"><?php echo $row['data_geracao']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</form>

<!-- Exibição da paginação -->
<?php if ($total_pages > 1) : ?>
    <br />
    <div class="pagination">
        <?php echo paginate_links($pagination_args); ?>
    </div>
<?php endif; ?>
