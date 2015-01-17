<?php

//load o3 module
$o3->module( 'email' );			
$o3->load();

/** get emails */
function get_emails() {
	$return = array();
	$files = o3_read_path( O3_EMAIL_DIR );
	foreach ( $files as $key => $value ) {
		if ( $value['file'] == 1 && $value['extension'] == 'json' ) {
			$return[] = $value['filename'];
		}
	}
	return $return;
}

$cmd = o3_post('cmd','');
$index = o3_post('index','');
if ( $cmd == 'save' ) {
	
	$index_new = o3_post('index_new','');
	$subject = o3_post('subject','');
	$description = o3_post('description','');
	$header = o3_post('header','');
	$footer = o3_post('footer','');
	$css = o3_post('css','');
	$html_body = o3_post('html_body','');
	$content_type = o3_post('content_type','');
	
	$data = array( 'index' => $index_new,
				 'subject' => $subject,
				 'description' => $description,
				 'header' => $header,
				 'footer' => $footer,
				 'css' => $css,
				 'body' => $html_body,
				 'content_type' => $content_type );
	
	$index = $index == '' ? $index_new : $index;
	$file = O3_EMAIL_DIR.'/'.$index.'.json';
	$new_file = O3_EMAIL_DIR.'/'.$index_new.'.json';
	o3_write_file( $file, json_encode($data), 'w' );
	rename( $file, $new_file);
	$index = $index_new;
	$cmd = 'edit';
	
	o3\admin\functions\add_notification_msg('Email template is saved');

} else if ( $cmd == 'delete' ) {
	
	$o3->email->remove( $index );

	o3\admin\functions\add_notification_msg('Email template is removed');
	
}

?>

<script type="text/javascript">
	
	(function($) {
		$(document).ready(function () {});
	})(jQuery); 

	function editEmail( index ) {
		index = typeof index == 'undefined' ? '' : index;
		var updateform = $('.update-form');
		updateform.find('input[name=cmd]').val('edit');
		updateform.find('input[name=index]').val(index);
		updateform.submit();
	}	
	
	function removeEmail( index ) {
		index = typeof index == 'undefined' ? '' : index;
		var updateform = $('.update-form');
		if ( confirm( 'Are you sure you want to remove this email?' ) ) {
			updateform.find('input[name=cmd]').val('delete');
			updateform.find('input[name=index]').val(index);
			updateform.submit();
		}
	}	
	
	function cancelEmail() {
		window.location = 'index.php?load=email';
	}		
	
	function saveEmail() {
		var updateform = $('.update-form'),
				index_new = updateform.find('input[name=index_new]');
		if ( $.trim(index_new.val()).length == 0 ) {
			alert('Index is missing.');		
			index_new.focus();
			return;
		}
		updateform.find('input[name=cmd]').val('save');			
		updateform.submit();
	}
								
</script>

<?php
	
$emails = get_emails();

$configData = array();

//check if folder writable
$is_writable_O3_EMAIL_DIR = is_writable( O3_EMAIL_DIR );
$configData[] = array( 'name' => 'O3_EMAIL_DIR',
					   'value' => O3_EMAIL_DIR,
					   'description' => 'Directory containing JSON email template files',
					   'status' => $is_writable_O3_EMAIL_DIR ? 'Writable' : 'Not writable',
					   'status_type' => $is_writable_O3_EMAIL_DIR ? 1 : 3 );

$configData[] = array( 'name' => 'O3_EMAIL_SEND_FROM',
					   'value' => O3_EMAIL_SEND_FROM,
					   'description' => 'Email sender address for outgoing emails',
					   'status' => strlen(trim(O3_EMAIL_SEND_FROM)) == 0 ? 'Empty' : 'OK',
					   'status_type' => strlen(trim(O3_EMAIL_SEND_FROM)) == 0 ? 2 : 1 );

$configData[] = array( 'name' => 'O3_EMAIL_TEST_ADDRESS',
					   'value' => O3_EMAIL_TEST_ADDRESS,
					   'description' => 'All emails are sent to this address in test mode',
					   'status' => strlen(trim(O3_EMAIL_TEST_ADDRESS)) == 0 ? 'Empty' : 'OK',
					   'status_type' => strlen(trim(O3_EMAIL_TEST_ADDRESS)) == 0 ? 2 : 1 );

$configData[] = array( 'name' => 'O3_EMAIL_TEST_MODE',
					   'value' => O3_EMAIL_TEST_MODE ? 'TRUE' : 'FALSE',
					   'description' => 'All emails are sent to this address in test mode',
					   'status' => O3_EMAIL_TEST_MODE ? 'TEST' : 'LIVE MODE',
					   'status_type' => O3_EMAIL_TEST_MODE ? 2 : 1 );
?>

<div class="padding10">
	<h1>Email</h1>

	<?php echo o3\admin\functions\generateConfigTable( $configData ); ?>

	<form action="" method="post" class="update-form">
		<input type="hidden" value="" name="cmd">
		<input type="hidden" value="<?php echo $index;?>" name="index">
		

			<?php
			if ( $cmd == 'edit' ) {
				
				$email_data = array();
				//select email data
				if ( $index > '' ) 
					$email_data = $o3->email->get( $index );	
				
				$emails = $o3->email->get_all();
				
			?>
												
				<div class="button_list">
					<input type="button" class="submit" onclick="saveEmail()" value="+ <?php echo $index==''?'Create':'Save changes'?>">
					
					<input type="button" onclick="cancelEmail()" value="&laquo; <?php echo $index==''?'Cancel':'Back'?>">
				</div>

				<div class="data-form">

					<div class="data-form-row">
						<label for="index_new">index</label>
						<input type="text" name="index_new" id="index_new" value="<?php echo o3_html(isset($email_data['index'])?$email_data['index']:'')?>">
					</div>

					<div class="data-form-row">
						<label for="subject" >subject</label>
						<input type="text" name="subject" id="subject" value="<?php echo o3_html(isset($email_data['subject'])?$email_data['subject']:'')?>">
					</div>

					<div class="data-form-row">
						<label for="header">header template</label>
						<select name="header" id="header">
							<option value="">none</option>
							<?php
							if ( count( $emails ) > 0 ) {
								foreach ( $emails as $key => $value ) {
									echo '<option value="'.o3_html($value['index']).'" '.( isset($email_data['header']) && $value['index'] == $email_data['header'] ? 'selected' : '' ).'>'.o3_html($value['index']).'</option>';
								}
							}
							?>
						</select>
					</div>

					<div class="data-form-row">
						<label for="footer">header template</label>
						<select name="footer" id="footer">
							<option value="">none</option>
							<?php
							if ( count( $emails ) > 0 ) {
								foreach ( $emails as $key => $value ) {
									echo '<option value="'.o3_html($value['index']).'" '.( isset($email_data['footer']) && $value['index'] == $email_data['footer'] ? 'selected' : '' ).'>'.o3_html($value['index']).'</option>';
								}
							}
							?>
						</select>
					</div>

					<div class="data-form-row">
						<label for="content_type">content type</label>
						<select name="content_type" id="content_type">
							<option value="text/html">text/html</option>
							<option value="text/plain" <?php echo o3_html(isset($email_data['content_type'])&&$email_data['content_type']=='text/plain'?'selected':'')?>>text/plain</option>
						</select>
					</div>

					<div class="data-form-row">
						<label for="description">description</label>
						<textarea name="description" id="description"><?php echo o3_html(isset($email_data['description'])?$email_data['description']:'')?></textarea>				
					</div>

					<div class="data-form-row">
						<label for="css">css styles</label>
						<textarea class="medium" name="css" id="css"><?php echo o3_html(isset($email_data['css'])?$email_data['css']:'')?></textarea>				
					</div>

					<div class="data-form-row">
						<label for="html_body">html body</label>
						<textarea class="big" name="html_body" id="html_body"><?php echo o3_html(isset($email_data['body'])?$email_data['body']:'')?></textarea>				
					</div>

				</div>
 
				
			<?php								
			} else {									
										
			?>
				
				<div class="button_list">
					<input type="button" onclick="editEmail()" value="+ New email">
				</div>

				<?php
				$emails = $o3->email->get_all();

				if ( count( $emails ) > 0 ) {
				?>

					<table class="main_table">
					<tr>
						<th>
							index
						</th>
						<th>
							subject
						</th>
						<th>
							description
						</th>
						<th>
							header
						</th>
						<th>
							footer
						</th>
						<th>
							content type
						</th>
					</tr>
					<tr>
					<?php
					if ( count( $emails ) > 0 ) {
						foreach ( $emails as $key => $value ) {
							?>
							<tr>
								<td>
									<span><?php echo o3_html(isset($value['index'])?$value['index']:'');?></span>
									<a href="javascript:editEmail('<?php echo o3_html(isset($value['index'])?$value['index']:'');?>')" title="edit" class="edit-icon icon"></a>
									<a href="javascript:removeEmail('<?php echo o3_html(isset($value['index'])?$value['index']:'');?>')" title="remove" class="remove-icon icon"></a>
								</td>
								<td>
									<?php echo o3_html(isset($value['subject'])?$value['subject']:'');?>
								</td>
								<td>
									<?php echo o3_html(isset($value['description'])?$value['description']:'');?>
								</td>
								<td>
									<?php echo o3_html(isset($value['header'])?$value['header']:'');?>
								</td>
								<td>
									<?php echo o3_html(isset($value['footer'])?$value['footer']:'');?>
								</td>
								<td>
									<?php echo o3_html(isset($value['content_type'])?$value['content_type']:'');?>
								</td>
							</tr>						
							<?php
						}
					}
					?>
					</tr>
					</table>
				<?php } else { ?>
				
					<div class="info_box">
						There is no emails to show. <a href="javascript:editEmail()">Click here to add one.</a>
					</div>				

				<?php } 
									
			}
			?>
			
			

	</form>
</div>