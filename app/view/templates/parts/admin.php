<?php
/**
 * Settings admin page
 */
?> 

<a class="download-leads-csv" href="<?php echo esc_url( admin_url( '?export_lead_data=csv' ) ); ?>" title="Download Leads">
<?php _e( 'Download Leads' , 'lead-list-api' ); ?> </a>

<a class="download-leads-csv" href="<?php echo esc_url( admin_url( 'tokem' ) ); ?>" title="tokem">
<?php _e( 'tokem' , 'lead-list-api' ); ?> </a>

<div id="leadlistapi-settings-page" class="wrap">
  
    <?php
         
        // Include lead model
        require_once LEADLISTAPI_DIR_PATH . '/app/model/lead-model.php';
        Admin_Model::show_leads_admin();

    ?>
     

</div>


