<?php
/**
 * Show leads admin page
 */
?> 
<?php   include LEADLISTAPI_DIR_PATH . '/app/view/templates/parts/menu.php';  ?>

<div id="leadlistapi-settings-page" class="wrap">
    <?php
        // Include lead model
        require_once LEADLISTAPI_DIR_PATH . '/app/model/lead-model.php';
        Admin_Model::show_leads_admin(); 
    ?>
</div>


