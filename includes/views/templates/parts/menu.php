<?php
/**
 * menu admin page
 * menu.php
 */
?> 
<div class="lead-list-api-menu-admin">
    <h2><?php _e( 'Lead List API Menu', 'lead-list-api' ); ?></h2>
    <ul>
        <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=lead-list-api' ) ); ?>" title="<?php _e( 'Página Principal', 'lead-list-api' ); ?>"><?php _e( 'Página Principal', 'lead-list-api' ); ?></a></li>
        <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=lead-list-api-leads' ) ); ?>" title="<?php _e( 'Leads Cadastrados', 'lead-list-api' ); ?>"><?php _e( 'Leads Cadastrados', 'lead-list-api' ); ?></a></li>
        <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=lead-list-api-token' ) ); ?>" title="<?php _e( 'Gerar Token', 'lead-list-api' ); ?>"><?php _e( 'Gerar Token', 'lead-list-api' ); ?></a></li>
        <li><a href="<?php echo esc_url( admin_url( '?export_lead_data=csv' ) ); ?>" title="<?php _e( 'Download Leads', 'lead-list-api' ); ?>"><?php _e( 'Download Leads', 'lead-list-api' ); ?></a></li>
    </ul>
</div>




