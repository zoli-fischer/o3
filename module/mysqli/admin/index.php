<?php

//load o3 module
$o3->module( 'mysqli' );			
$o3->load();

$cmd = o3_post('cmd','');
$index = o3_post('index','');

?>
<script type="text/javascript">
	
	(function($) {
		$(document).ready(function () {});
	})(jQuery); 

</script>

<?php
	
$configData = array();

$link = mysqli_connect( O3_MYSQLI_HOST, O3_MYSQLI_USER, O3_MYSQLI_PASSWORD );
$link_error_no = mysqli_connect_errno();
$db_select = mysqli_select_db( $link, O3_MYSQLI_DB );

$configData[] = array( 'name' => 'O3_MYSQLI_HOST',
					   'value' => O3_MYSQLI_HOST,
					   'description' => 'MySQL server host',
					   'status' => $link ? ( O3_MYSQLI_HOST != '' ? 'OK' : 'Host is empty' ) : 'Host not found',
					   'status_type' => $link ? ( O3_MYSQLI_HOST != '' ? 1 : 3 ) : 3  );

$configData[] = array( 'name' => 'O3_MYSQLI_USER',
					   'value' => O3_MYSQLI_USER,
					   'description' => 'MySQL server login username',
					   'status' => $link ? ( O3_MYSQLI_USER != '' ? 'OK' : 'Username is empty' ) : '',
					   'status_type' => $link ? ( O3_MYSQLI_USER != '' ? 1 : 3 ) : 0 );

$configData[] = array( 'name' => 'O3_MYSQLI_PASSWORD',
					   'value' => O3_MYSQLI_PASSWORD != '' ? '********' : '',
					   'description' => 'MySQL server login password',
					   'status' => $link ? ( O3_MYSQLI_PASSWORD != '' ? 'OK' : 'Password is empty') : '',
					   'status_type' => $link ? ( O3_MYSQLI_PASSWORD != '' ? 1 : 2 ) : 0 );

$configData[] = array( 'name' => 'O3_MYSQLI_AUTOCONNECT',
					   'value' => O3_MYSQLI_AUTOCONNECT ? 'TRUE' : 'FALSE',
					   'description' => 'Allow connection to MySQL server to be created at initialization',
					   'status' => '',
					   'status_type' => 0 );

$configData[] = array( 'name' => 'O3_MYSQLI_DB',
					   'value' => O3_MYSQLI_DB,
					   'description' => "At initialization this database is selected if not empty.\nRequires O3_MYSQLI_AUTOCONNECT to be TRUE.",
					   'status' => O3_MYSQLI_DB == '' ? '' : ( $db_select ? 'OK' : 'Database not found' ),
					   'status_type' => O3_MYSQLI_DB == '' ? 0 : ( $db_select ? 1 : ( O3_MYSQLI_AUTOCONNECT ? 3 : 2 ) ) );

$configData[] = array( 'name' => 'O3_MYSQLI_PORT',
					   'value' => O3_MYSQLI_PORT,
					   'description' => "MySQL server port",
					   'status' => '',
					   'status_type' => 0 );

$configData[] = array( 'name' => 'O3_MYSQLI_SOCKET',
					   'value' => O3_MYSQLI_SOCKET,
					   'description' => "MySQL server socket",
					   'status' => '',
					   'status_type' => 0 );

$configData[] = array( 'name' => 'O3_MYSQLI_DEBUG_QUERIES',
					   'value' => O3_MYSQLI_DEBUG_QUERIES ? 'TRUE' : 'FALSE',
					   'description' => "Show performed mysqli queries in debug console if debugging allowed",
					   'status' => O3_MYSQLI_DEBUG_QUERIES ? 'High risk of security vulnerability' : '',
					   'status_type' => O3_MYSQLI_DEBUG_QUERIES ? 3 : 0 );

?>

<div class="padding10">
	<h1>Mysqli</h1>

	<?php echo o3\admin\functions\generateConfigTable( $configData ); ?>

	<form action="" method="post" class="update-form">
		<input type="hidden" value="" name="cmd">
		<input type="hidden" value="<?php echo $index;?>" name="index">
		
		<div class="info_box">
			<?php

			if ( O3_MYSQLI_HOST == '' ) {
				?>
				Unable to connect, host is empty. 
				<?php
			} else if ( $link ) {
				?>
				Connected successufuly to <b><?php echo o3_html(O3_MYSQLI_HOST) ?></b> with username 
				<b><?php echo o3_html(O3_MYSQLI_USER) ?></b> and 
				<?php
				if ( O3_MYSQLI_PASSWORD != '' ) {
					?> 
						with password ********. 
					<?php
				} else {
					?> 
						without password.
					<?php
				}				
			} else {
				?>
				Unable to connect to <b><?php echo o3_html(O3_MYSQLI_HOST) ?></b> with username 
				<b><?php echo o3_html(O3_MYSQLI_USER) ?></b> and 
				<?php
				if ( O3_MYSQLI_PASSWORD != '' ) {
					?> 
						with password ********.  
					<?php
				} else {
					?> 
						without password. 
					<?php
				}
				?>
				Error no. <?php echo $link_error_no;								
			}

			?>
		</div>

	</form>
</div>