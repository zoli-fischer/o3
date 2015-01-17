<?php
/**
 * O3 Engine Admin utility functions file
 *
 * With Admin utility you can manage the installed O3 modules
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace o3\admin\functions;

/** Error message output for authentication */
function auth_output() {
  echo '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <meta charset="utf-8">
  <title>O3 v'.O3_VERSION.'</title>
</head>
<body>  
  <h1>Rejected!</h1>
    <p>Wrong username or password!</p>
    <p><a href="javascript:{window.location.reload()}">Retry...</a></p>
</body>
</html>';
  die();
}

/** Check/handle authentication for O3 Admin utility */
function auth_check() {
  if ( O3_ADMIN_PASSWORD == "password" ) {
    auth_output( "The password must be changed in 'config.custom.php'" );
    return false;
  }
  
  if ( true || isset($_SERVER["PHP_AUTH_USER"]) ) {

    if (!isset($_SERVER["PHP_AUTH_USER"]) ||
        !isset($_SERVER["PHP_AUTH_PW"]) ||
        $_SERVER["PHP_AUTH_USER"] != O3_ADMIN_USERNAME ||
        $_SERVER["PHP_AUTH_PW"]   != O3_ADMIN_PASSWORD) {      
  
      header('WWW-Authenticate: Basic realm="O3 Login"');
      header('HTTP/1.0 401 Unauthorized');

      auth_output( '<h1>Rejected!</h1>
<p>Wrong username or password!</p>
<p><a href="javascript:{window.location.reload()}">Retry...</a></p>' );    
    }
    
    else {      
      $_SESSION["authenticated"] = true;
      return true;
    }
  }
}


/** Get list of admin themes */
function get_admin_themes() {

  $themes = array();

  $paths = o3_read_path( O3_ADMIN_DIR.'/themes' );  
  if ( count($paths) > 0 ) {
    foreach ( $paths as $value ) {
       if ( $value['file'] != 1 ) 
          $themes[] = $value['name'];
    }
  }

  return $themes;

}

/** Get list of o3 modules */
function get_modules() {

  $themes = array();

  $paths = o3_read_path( O3_MOD_DIR );  
  if ( count($paths) > 0 ) {
    foreach ( $paths as $value ) {
       if ( $value['file'] != 1 && file_exists($value['path'].'/admin') ) 
          $themes[] = $value['name'];
    }
  }

  return $themes;

}


/** Notification message container */
$NOTIFICATION_MSG_LIST = array();

/** Add notification message */
function add_notification_msg( $msg ) {
  global $NOTIFICATION_MSG_LIST;
  $NOTIFICATION_MSG_LIST[] = $msg;
}

/** Get notification messages */
function get_notification_msg() {
  global $NOTIFICATION_MSG_LIST;
  return $NOTIFICATION_MSG_LIST;
}


/** Create a config table from an array */
function generateConfigTable( $data ) {
  $buffer = '';
  if ( count($data) > 0 ) {
    $buffer .= '<table cellpadding="0" cellspacing="0" class="config_table">
    <tr>
      <th>Config name</th><th>Value</th><th>Description</th><th>Status</th>
    </tr>';
    foreach ( $data as $key => $value) {
         $buffer .= "<tr>
             <td>".o3_html($value['name'])."</td><td>".o3_html($value['value'])."</td><td>".nl2br(o3_html($value['description']))."</td><td class='status_type_".o3_html($value['status_type'])."'>".o3_html($value['status'])."</td>
         </tr>";
    }
    $buffer .= '</table>';
  }
  return $buffer;
}

?>