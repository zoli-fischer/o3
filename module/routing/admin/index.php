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

?>

<div class="padding10">
	<h1>Routing</h1>

	<?php echo o3\admin\functions\generateConfigTable( $configData ); ?>

	<form action="" method="post" class="update-form">
		<input type="hidden" value="" name="cmd">
		<input type="hidden" value="<?php echo $index;?>" name="index">

	</form>
</div>