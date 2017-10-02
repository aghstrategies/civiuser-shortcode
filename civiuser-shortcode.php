
<?php
   /*
   Plugin Name: Civicrm User Shortcode
   Plugin URI: http://aghstrategies.com
   Description: a plugin to create a shortcode to display the information of the logged in wordpress users civicrm contact.
   Version: 1.0
   Author: AGH Strategies
   Author URI: http://aghstrategies.com
   License: GPL2
   */

   add_shortcode('civiuser_shortcode', 'civiuser_process_shortcode');
   civicrm_initialize();


   function civiuser_process_shortcode($attributes, $content = NULL) {
    //  wp_register_script('civipcp-widget-js', plugins_url('js/civipcp-widget.js', __FILE__), array('jquery', 'underscore'));
    //  wp_enqueue_style('civipcp-widget-css', plugins_url('css/civipcp-widget.css', __FILE__));
     try {
      $event = civicrm_api3('Event', 'getsingle', array(
        'return' => array("title"),
        'id' => $page_id,
      ));
     }
     catch (CiviCRM_API3_Exception $e) {
       $error = $e->getMessage();
       CRM_Core_Error::debug_log_message(ts('API Error %1', array(
         'domain' => 'civiuser-shortcode',
         1 => $error,
       )));
     }
     $userDiv = "<div class='civiuser'></div>";
     echo "$userDiv";
   }
