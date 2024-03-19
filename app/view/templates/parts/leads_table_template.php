<?php
/**
 * table leads admin page
 *  
 */
?> 
<form method="post" action="<?php echo admin_url('admin.php'); ?>">
    <button type="submit">Excluir Leads Selecionados</button> <!-- Botão de envio para excluir leads selecionados -->

    <input type="hidden" name="action" value="delete_selected_leads"> <!-- Ação para indicar a exclusão de leads selecionados -->
    <input type="hidden" name="paged" value="<?php echo isset($_GET['paged']) ? $_GET['paged'] : 1; ?>">

    <table style="border-collapse: collapse; width: 100%;">
        <!-- Cabeçalho da tabela -->
        <tr>
            <th style="border: 1px solid black; padding: 8px;">Excluir</th> <!-- Cabeçalho para o checkbox -->
            <?php foreach ($headers as $header) : ?>
                <th style="border: 1px solid black; padding: 8px;"><?php echo $header; ?></th>
            <?php endforeach; ?>
        </tr>

        <!-- Linhas da tabela -->
        <?php foreach ($items_to_display as $row) : ?>
            <tr>
                <td style="border: 1px solid black; padding: 8px;">
                    <input type="checkbox" name="lead_ids[]" value="<?php echo $row['idlead']; ?>">
                </td> <!-- Campo de checkbox para marcar o lead -->
                <?php foreach ($row as $value) : ?>
                    <td style="border: 1px solid black; padding: 8px;"><?php echo $value; ?></td>
                <?php endforeach; ?>
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