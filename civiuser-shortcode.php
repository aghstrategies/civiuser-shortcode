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
add_filter('widget_text', 'do_shortcode');

function civiuser_process_shortcode($attributes, $content = NULL) {
  // look up logged in contacts ID
  $userId = CRM_Core_Session::singleton()->getLoggedInContactID();
  $userDiv = "Please <a href='" . wp_login_url(get_permalink()) . "' title='Login'>Login</a> to view this content";
  if (!empty($userId)) {
    // get info of logged in contact thru the api
    try {
      $contactInfo = civicrm_api3('Contact', 'getsingle', array(
        'id' => $userId,
      ));
    }
    catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
      CRM_Core_Error::debug_log_message(ts('API Error %1', array(
        'domain' => 'civiuser-shortcode',
        1 => $error,
      )));
    }

    // Create div of logged in contact's information
    $userDiv = "
    <div class='civiuser'>
      <div>{$contactInfo['first_name']} {$contactInfo['last_name']}</div>
      <div>{$contactInfo['street_address']}</div>
      <div>{$contactInfo['supplemental_address_1']}</div>
      <div>{$contactInfo['supplemental_address_2']}</div>
      <div>{$contactInfo['supplemental_address_3']}</div>
      <div>{$contactInfo['city']} {$contactInfo['state_province_name']}</div> <div>{$contactInfo['postal_code']}</div>
      <div>{$contactInfo['phone']}</div>
      <div>{$contactInfo['email']}</div>
      <a href='" . get_permalink() . "/update-contact-information/'>Update</a>
    </div>";
  }
  // print that div
  return "$userDiv";
}
