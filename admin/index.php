<?php
/**
 * O3 Engine Admin utility main file
 *
 * With Admin utility you can manage the installed O3 modules
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');

define( 'O3_ADMIN', true );

//load o3
require_once(str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))).'/../o3.php');

if ( !isset($o3) )
	$o3 = new o3();
$o3->debug->show( true ); //show debug info

o3_session_start(); //start session
o3_header_encoding(); //set utf-8 document encoding

//include admin utility functions
require_once('functions.php');

//add module for loading
//$o3->module('mini');

//o3 modules load them			
$o3->load();

use o3\admin\functions as functions;

//check for authentification
functions\auth_check();

//get themes
$themes = functions\get_admin_themes();
$_SESSION['o3_admin_theme'] = isset($_SESSION['o3_admin_theme']) && isset($themes[$_SESSION['o3_admin_theme']]) ? $themes[$_SESSION['o3_admin_theme']] : O3_ADMIN_THEME;

//get modules list
$modules = functions\get_modules();
 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	
	<meta charset="utf-8">
	<title>O3 v<?php echo O3_VERSION ?></title>
	<!--[if IE]>
		<meta http-equiv="imagetoolbar" content="no" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />	
	<![endif]-->

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->

	<link rel="shortcut icon" href="<?php echo O3_URL; ?>/admin/media/favicon.ico" />
	<link rel="icon" href="<?php echo O3_URL; ?>/admin/media/favicon.ico" type="image/x-icon" />

	<link rel="stylesheet" type="text/css" href="<?php echo O3_URL; ?>/admin/css/global.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo O3_URL; ?>/admin/themes/<?php echo $_SESSION['o3_admin_theme']; ?>/main.css" />
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

	<script src="<?php echo O3_URL; ?>/admin/js/script.js"></script>

<?php

/*
//create list of css
$css_array = array( O3_ADMIN_DIR.'/css/global.css',
					O3_ADMIN_DIR.'/themes/'.$_SESSION['o3_admin_theme'].'/main.css', );

//show minified css links
echo $o3->mini->css_link( $css_array ).'<noscript>'.$o3->mini->css_link('css/noscript.css').'</noscript>';

//load jquery from googleapis
echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>';

//show minified js scripts
echo $o3->mini->js_script( O3_ADMIN_DIR.'/js/script.js' );
*/

?>

</head>
<body class="<?php echo o3_ua_body_classes(); ?>">  

	<?php

	//check if need to load content
	if ( isset($_GET['load'])) {
		switch (o3_get('load')) {
			case 'overview':
		 		require_once('overview.php');		 		
				break;
			case 'setup':
		 		require_once('setup.php'); 
				break;
		};
		//check for modules
		if ( in_array(o3_get('load'), $modules) ) {
			require_once(O3_MOD_DIR.'/'.o3_get('load').'/admin/index.php');
		}

		?>
		
		<script type="text/javascript">
			window.parent.push_notification_msg('<?php echo addslashes(json_encode(o3\admin\functions\get_notification_msg())); ?>');
		</script>

		<?php
	} else { 

	?>
	
	<div class="main">
		<div class="mainr">
			<div class="nav">
				<div class="navt">
					<div class="navr">
						<div class="logo">
							O<sub>3</sub> Admin utility
						</div>			
					</div>
					<div class="navr">	
						<div class="menu" id="admin_menu">
							<div id="admin_menud">
								<div class="menud">
									<a href="#menu-overview" id="menu-overview">Overview</a>
									<a href="#menu-setup" id="menu-setup">Setup / Config</a>
									<hr />
									<?php
									if ( count($modules) > 0 ) {
										foreach ( $modules as $value) {
											echo '<a href="#menu-'.o3_html($value).'" id="menu-'.o3_html($value).'">'.o3_html($value).'</a>';
										}	
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>			
			<div class="page">
				<iframe src="" name="data_frame" id="data_frame" frameborder=""0></iframe>
			</div>
		</div>		
		<div class="navr">	
			<div class="navfooter">
				&nbsp;
			</div>
			<div>
				&nbsp;
			</div>
		</div>
	</div>	

	<div class="navfooterc">
		<p>o3.github.com — Send bug reports to the bug tracker & support questions to zoli_fischer@yahoo.com. </p>
	</div>	

	<div class="push_notification"></div>

	<?php
	} 
	?>	

</body>
</html>