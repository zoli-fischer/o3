<?php

//load o3 module
$o3->module( 'template' );			
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

//check if folder writable
$is_writable_O3_TEMPLATE_DIR = is_readable( O3_TEMPLATE_DIR );
$configData[] = array( 'name' => 'O3_TEMPLATE_DIR',
					   'value' => O3_TEMPLATE_DIR,
					   'description' => 'The location of the directory with template files',
					   'status' => $is_writable_O3_TEMPLATE_DIR ? 'Readable' : 'Not readable',
					   'status_type' => $is_writable_O3_TEMPLATE_DIR ? 1 : 3 );

$is_writable_O3_TEMPLATE_CONTROLLER_DIR = is_readable( O3_TEMPLATE_CONTROLLER_DIR );
$configData[] = array( 'name' => 'O3_TEMPLATE_CONTROLLER_DIR',
					   'value' => O3_TEMPLATE_CONTROLLER_DIR,
					   'description' => 'The location of the directory with template controller files',
					   'status' => $is_writable_O3_TEMPLATE_CONTROLLER_DIR ? 'Readable' : 'Not readable',
					   'status_type' => $is_writable_O3_TEMPLATE_CONTROLLER_DIR ? 1 : 3 );

$is_writable_O3_TEMPLATE_VIEW_DIR = is_readable( O3_TEMPLATE_VIEW_DIR );
$configData[] = array( 'name' => 'O3_TEMPLATE_VIEW_DIR',
					   'value' => O3_TEMPLATE_VIEW_DIR,
					   'description' => 'The location of the directory with view files',
					   'status' => $is_writable_O3_TEMPLATE_VIEW_DIR ? 'Readable' : 'Not readable',
					   'status_type' => $is_writable_O3_TEMPLATE_VIEW_DIR ? 1 : 3 );

$is_writable_O3_TEMPLATE_CONTROLLER_DIR = is_readable( O3_TEMPLATE_CONTROLLER_DIR );
$configData[] = array( 'name' => 'O3_TEMPLATE_CONTROLLER_DIR',
					   'value' => O3_TEMPLATE_CONTROLLER_DIR,
					   'description' => 'The location of the directory with controller files',
					   'status' => $is_writable_O3_TEMPLATE_CONTROLLER_DIR ? 'Readable' : 'Not readable',
					   'status_type' => $is_writable_O3_TEMPLATE_CONTROLLER_DIR ? 1 : 3 );

?>

<div class="padding10">
	<h1>Template</h1>

	<?php echo o3\admin\functions\generateConfigTable( $configData ); ?>

	<form action="" method="post" class="update-form">
		<input type="hidden" value="" name="cmd">
		<input type="hidden" value="<?php echo $index;?>" name="index">

	</form>
</div>