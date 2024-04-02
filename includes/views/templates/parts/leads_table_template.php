<?php
/**
 * leads table template
 *  
 */
?> 
<div class="lead-lest-api">


    <?php   include LEADLISTAPI_DIR_PATH . '/includes/views/templates/parts/menu.php';  ?>


    <div class="wrap">
        <h1><?php _e( 'Leads Cadastrados', 'lead-list-api' ); ?></h1>
        <p><?php _e( 'Total de leads cadastrados: ', 'lead-list-api' ); echo $total_leads;   ?> </p>
    </div>


    <?php if (empty($data)) : ?>  
        <p><?php echo __('Nenhum lead encontrado', 'lead-list-api'); ?></p>
    <?php else : ?> 
        <form method="post" action="<?php echo admin_url('admin.php'); ?>">
            <button type="submit" class="delete-leads button button-primary">Excluir Leads Selecionados</button> <!-- Botão de envio para excluir leads selecionados -->

            <input type="hidden" name="action" value="delete_selected_leads"> <!-- Ação para indicar a exclusão de leads selecionados -->
            <input type="hidden" name="paged" value="<?php echo isset($_GET['paged']) ? $_GET['paged'] : 1; ?>">

            <table  class="leads-list-teble "  style="border-collapse: collapse; width: 100%; ">
                <!-- Cabeçalho da tabela -->
                <tr>
                    <th style="border: 1px solid black; padding: 8px;">Excluir</th> <!-- Cabeçalho para o checkbox -->
                    <?php foreach ($headers as $header) : ?>
                        <th style="border: 1px solid black; padding: 8px;"><?php echo $header; ?></th>
                    <?php endforeach; ?>
                    <!-- Novas colunas adicionadas -->
                
                </tr>

                <!-- Linhas da tabela -->
            
                <?php  foreach ($items_to_display as $row) : ?>
                    <tr>
                        <td style="border: 1px solid black; padding: 8px;">
                            <input type="checkbox" name="lead_ids[]" value="<?php echo $row['idlead']; ?>">
                        </td> <!-- Campo de checkbox para marcar o lead -->
                        <?php foreach ($row as $key => $value) : ?>
                            <td style="border: 1px solid black; padding: 8px;">
                                <?php 
                                    // Condição para exibir os novos dados nas colunas correspondentes
                                    if ($key === 'token_name') {
                                        echo $value;
                                    } elseif ($key === 'data_conversion') {
                                        echo $value;
                                    } elseif ($key === 'num_conversions') {
                                        echo $value;
                                    } else {
                                        echo $value; 
                                    }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </form>
    <?php endif; ?>

    <!-- Exibição da paginação -->
    <?php if ($total_pages > 1) : ?>
        <br />
        <div class="pagination">
            <?php echo paginate_links($pagination_args); ?>
        </div>
    <?php endif; ?>

</div>