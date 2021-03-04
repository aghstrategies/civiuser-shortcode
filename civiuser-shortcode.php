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
// civicrm_initialize();
add_filter('widget_text', 'do_shortcode');

function civiuser_process_shortcode($attributes, $content = NULL) {
civicrm_initialize();

  // set message for if not logged in
  $userDiv = "Please <a href='" . wp_login_url(get_permalink()) . "' title='Login'>Login</a> to view this content";
  // get wordpress user id
  $ufId = wp_get_current_user();
  // if the user is logged in look up their civicrm contact info thru the api
  if (!empty($ufId->ID)) {
    // get info of logged in contact thru the api
    try {
      $contactInfo = civicrm_api3('UFMatch', 'getsingle', array(
        'sequential' => 1,
        'uf_id' => $ufId->ID,
        'api.Contact.getsingle' => array('id' => "\$value.contact_id"),
      ));
    }
    catch (CiviCRM_API3_Exception $e) {
      $error = $e->getMessage();
      CRM_Core_Error::debug_log_message(ts('API Error %1', array(
        'domain' => 'civiuser-shortcode',
        1 => $error,
      )));
    }
    // if civi contact found print info and update link
    if (!empty($contactInfo['api.Contact.getsingle'])) {
      $contactInfo = $contactInfo['api.Contact.getsingle'];
      // get url to update the page
      $updateUrl = site_url('/update-contact-information/');
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
        <a href='$updateUrl'>Update</a>
      </div>";
    }
    // if no civi user found print error message (there should always be a linked civi user)
    else {
      $userDiv = "Error: No civicrm contact was found to be associated with your wordpress user please contact your system admin";
    }
  }
  // print that div
  return "$userDiv";
}
