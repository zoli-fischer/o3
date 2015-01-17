<?php

//load o3 module
$o3->module( 'mini' );			
$o3->load();

//mime type list
$extensions = array( 'css' => 'text/css', 'js' => 'application/javascript' );

//get file from md5 index
function get_file_from_md5( $index ) {
	global $o3;
	$file_found = '';

	$css_cache_files = $o3->mini->get_css_cache_files();
	foreach ( $css_cache_files as $key => $value )
		if ( md5($value) == $index ) {
			$file_found = $value;
			break;
		}

	if ( $file_found == '' )
		$js_cache_files = $o3->mini->get_js_cache_files();
		foreach ( $js_cache_files as $key => $value )
			if ( md5($value) == $index ) {
				$file_found = $value;
				break;
			}

	return $file_found;
}

$open = o3_get('open','');
if ( $open != '' ) {
	
	//search for file
	$file_found = get_file_from_md5( $open );

	?>
	<link  rel="stylesheet" type="text/css" href="<?php echo O3_URL; ?>/resource/js/lib/syntaxhighlighter-latest/styles/shCore.css" />
	<link  rel="stylesheet" type="text/css" href="<?php echo O3_URL; ?>/resource/js/lib/syntaxhighlighter-latest/styles/shThemeDefault.css" />

	<script type="text/javascript" src="<?php echo O3_URL; ?>/resource/js/lib/syntaxhighlighter-latest/scripts/shCore.js"></script>
	<script type="text/javascript" src="<?php echo O3_URL; ?>/resource/js/lib/syntaxhighlighter-latest/scripts/shBrushJScript.js"></script>
	<script type="text/javascript" src="<?php echo O3_URL; ?>/resource/js/lib/syntaxhighlighter-latest/scripts/shBrushCss.js"></script>
	<?php

	if ( $file_found != '' ) {
		?>

		<div class="info_box">
			Basename: <?php echo o3_html(basename($file_found)); ?><br>
			Path: <?php echo o3_html(dirname(realpath($file_found))); ?><br>
			Size: <?php echo o3_html(filesize($file_found)); ?><br>
			Content type: <?php echo o3_html($extensions[o3_extension($file_found)]); ?>
		</div>

		<pre class="brush: <?php echo o3_html(o3_extension($file_found)); ?>"><?php echo o3_html(file_get_contents($file_found)); ?></pre>

		<script type="text/javascript">
		     SyntaxHighlighter.all()
		</script>
		<?php
	} else {
		echo '<p>Error. File not found.</p>';
	}

} else {

	$cmd = o3_post('cmd','');
	$index = o3_post('index','');
	if ( $cmd == 'delete' ) {
		
		//search for file
		$file_found = get_file_from_md5( $index );

		if ( $file_found != '' && o3_unlink($file_found) ) {
			o3\admin\functions\add_notification_msg('Cache file is removed');
		} else {
			o3\admin\functions\add_notification_msg('Error occurred, cache file was not removed');
		}		

	} else if ( $cmd == 'clear' ) {

		//clear cache
		$o3->mini->clear_all_js();
		$o3->mini->clear_all_css();

		o3\admin\functions\add_notification_msg('Mini cache is cleared');

	}

	?>

	<script type="text/javascript">
		
		(function($) {
			$(document).ready(function () {});
		})(jQuery); 

		function clearCache() {
			var updateform = $('.update-form');
			if ( confirm('Are you sure you want to remove all the mini cache files?') ) {
				updateform.find('input[name=cmd]').val('clear');
				updateform.find('input[name=index]').val(-1);
				updateform.submit();
			}
		}

		function openFile( file ) {
			window.open( window.location+'&open='+escape(file) );	
		}

		function removeFile( index ) {
			index = typeof index == 'undefined' ? '' : index;
			var updateform = $('.update-form');
			if ( confirm( 'Are you sure you want to remove this cache file?' ) ) {
				updateform.find('input[name=cmd]').val('delete');
				updateform.find('input[name=index]').val(index);
				updateform.submit();
			}
		}

	</script>

	<?php
		
	$configData = array();

	$configData[] = array( 'name' => 'O3_MINI_MINIMIZE',
						   'value' => O3_MINI_MINIMIZE ? 'TRUE' : 'FALSE',
						   'description' => 'Allowing to compress JS and CSS files. May lead to high CPU usage but reduces the cache size.',
						   'status' => '',
						   'status_type' => 0 );

	$configData[] = array( 'name' => 'O3_MINI_JS',
						   'value' => O3_MINI_JS ? 'TRUE' : 'FALSE',
						   'description' => 'Allowing to compress JS files. May lead to high CPU usage but reduces the cache size.',
						   'status' => !O3_MINI_MINIMIZE && O3_MINI_JS ? 'O3_MINI_MINIMIZE overrides to FALSE' : '',
						   'status_type' => !O3_MINI_MINIMIZE && O3_MINI_JS ? 2 : 0 );

	$configData[] = array( 'name' => 'O3_MINI_CSS',
						   'value' => O3_MINI_CSS ? 'TRUE' : 'FALSE',
						   'description' => 'Allowing to compress CSS files. May lead to high CPU usage but reduces the cache size.',
						   'status' => !O3_MINI_MINIMIZE && O3_MINI_CSS ? 'O3_MINI_MINIMIZE overrides to FALSE' : '',
						   'status_type' => !O3_MINI_MINIMIZE && O3_MINI_CSS ? 2 : 0 );

	$is_writable_O3_MINI_JS_CACHE_DIR = is_writable( O3_MINI_JS_CACHE_DIR );
	$configData[] = array( 'name' => 'O3_MINI_JS_CACHE_DIR',
						   'value' => O3_MINI_JS_CACHE_DIR,
						   'description' => 'Directory for JS cache files',
						   'status' => $is_writable_O3_MINI_JS_CACHE_DIR ? 'Writable' : 'Not writable',
						   'status_type' => $is_writable_O3_MINI_JS_CACHE_DIR ? 1 : 3 );

	$url_exists_O3_MINI_JS_CACHE_DIR = o3_url_exists( O3_MINI_JS_CACHE_URL ) || o3_get_host().'/'.o3_url_exists( O3_MINI_JS_CACHE_URL );
	$configData[] = array( 'name' => 'O3_MINI_JS_CACHE_URL',
						   'value' => O3_MINI_JS_CACHE_URL,
						   'description' => 'URL to JS cache directory',
						   'status' => $url_exists_O3_MINI_JS_CACHE_DIR ? 'OK' : '404 Not Found',
						   'status_type' => $url_exists_O3_MINI_JS_CACHE_DIR ? 1 : 3 );

	$is_writable_O3_MINI_CSS_CACHE_DIR = is_writable( O3_MINI_CSS_CACHE_DIR );
	$configData[] = array( 'name' => 'O3_MINI_CSS_CACHE_DIR',
						   'value' => O3_MINI_CSS_CACHE_DIR,
						   'description' => 'Directory for CSS cache files',
						   'status' => $is_writable_O3_MINI_CSS_CACHE_DIR ? 'Writable' : 'Not writable',
						   'status_type' => $is_writable_O3_MINI_CSS_CACHE_DIR ? 1 : 3 );

	$url_exists_O3_MINI_CSS_CACHE_DIR = o3_url_exists( O3_MINI_CSS_CACHE_URL ) || o3_get_host().'/'.o3_url_exists( O3_MINI_CSS_CACHE_URL );
	$configData[] = array( 'name' => 'O3_MINI_CSS_CACHE_URL',
						   'value' => O3_MINI_CSS_CACHE_URL,
						   'description' => 'URL to CSS cache directory',
						   'status' => $url_exists_O3_MINI_CSS_CACHE_DIR ? 'OK' : '404 Not Found',
						   'status_type' => $url_exists_O3_MINI_CSS_CACHE_DIR ? 1 : 3 );

	$configData[] = array( 'name' => 'O3_MINI_JS_CACHE_LIFETIME',
						   'value' => O3_MINI_JS_CACHE_LIFETIME,
						   'description' => 'Max. lifetime for JS cache files in seconds',
						   'status' => O3_MINI_JS_CACHE_LIFETIME < 172800 ? 'Min. recomanded value is 172800' : 'OK',
						   'status_type' => O3_MINI_JS_CACHE_LIFETIME >= 172800 ? 1 : 3 );

	$configData[] = array( 'name' => 'O3_MINI_CSS_CACHE_LIFETIME',
						   'value' => O3_MINI_CSS_CACHE_LIFETIME,
						   'description' => 'Max. lifetime for CSS cache files in seconds',
						   'status' => O3_MINI_CSS_CACHE_LIFETIME < 172800 ? 'Min. recomanded value is 172800' : 'OK',
						   'status_type' => O3_MINI_CSS_CACHE_LIFETIME >= 172800 ? 1 : 3 );

	$css_cache_files = $o3->mini->get_css_cache_files();
	$js_cache_files = $o3->mini->get_js_cache_files();
	$cache_files = count($css_cache_files) + count($js_cache_files);

	?>

	<div class="padding10">
		<h1>Mini</h1>

		<?php echo o3\admin\functions\generateConfigTable( $configData ); ?>

		<form action="" method="post" class="update-form">
			<input type="hidden" value="" name="cmd">
			<input type="hidden" value="<?php echo $index;?>" name="index">
			
			<div class="info_box">
				<?php
				if ( $cache_files > 0 ) {
				?>
					No. of cache files: <?php echo $cache_files; ?><br><a href="javascript:clearCache()">Click here to clear cache</a>
				<?php
				} else {
				?>
					There is no cache files.
				<?php
				}
				?>
			</div>

			<?php
			if ( count($css_cache_files) > 0 || count($js_cache_files) > 0 ) {
			?>
				<table class="main_table">
				<tr>
					<th>
						basename
					</th> 
					<th>
						content type
					</th>
				</tr>
				<tr>
				<?php
				if ( count( $css_cache_files ) > 0 ) {
					foreach ( $css_cache_files as $key => $value ) {
						?>
						<tr>
							<td>
								<span><?php echo o3_html(basename($value));?></span>
								<a href="javascript:openFile('<?php echo md5($value);?>')" title="open" class="open-icon icon"></a>
								<a href="javascript:removeFile('<?php echo md5($value); ?>')" title="remove" class="remove-icon icon"></a>
							</td>
							<td>
								<?php echo o3_html($extensions[o3_extension($value)]);?>
							</td>
						</tr>						
						<?php
					}
				}
				if ( count( $js_cache_files ) > 0 ) {
					foreach ( $js_cache_files as $key => $value ) {
						?>
						<tr>
							<td>
								<span><?php echo o3_html(basename($value));?></span>
								<a href="javascript:openFile('<?php echo md5($value);?>')" title="open" class="open-icon icon"></a>
								<a href="javascript:removeFile('<?php echo md5($value); ?>')" title="remove" class="remove-icon icon"></a>
							</td>
							<td>
								<?php echo o3_html($extensions[o3_extension($value)]);?>
							</td>
						</tr>						
						<?php
					}
				}
				?>
				</tr>
				</table>
			<?php 
			}
			?>
				
				

		</form>
	</div>
<?php 
}
?>