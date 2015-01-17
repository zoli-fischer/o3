<?php

/**
 * O3 Engine language module
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

//load o3 module
$o3->module('lang');
$o3->load();

//get languages
function get_languages() {
	$return = array();
	$files = o3_read_path( O3_LANG_DIR );
	foreach ( $files as $key => $value ) {
		if ( $value['file'] == 1 && $value['extension'] == 'json' && !preg_match( "/[^a-z0-9]/", $value['filename'] ) ) {
			$return[] = $value['filename'];
		}
	}
	return $return;
}

//get collections
function get_collections( $languages ) {
	$return = array();
	if ( count($languages) > 0 ) {
		$files = o3_read_path( O3_LANG_DIR );
		foreach ( $files as $key => $value ) {
			if ( $value['file'] == 1 && $value['extension'] == 'json' ) {
				$exp = explode('-',$value['filename'],2);				
				if ( count($exp) == 2 && !in_array( $exp[1], $return ) && in_array( $exp[0], $languages ) && !preg_match( "/[^a-z0-9-_!]/", $exp[1] ) ) {
					$return[] = $exp[1];								
				}
			}
		}
	}
	return $return;
}

$collection = o3_request('collection','general');
$collection = $collection == '' ? 'general' : $collection;
$cmd = o3_post('cmd','');
if ( $cmd == 'udt' ) {
	
	$languages = get_languages();
	$collections = get_collections( $languages );
	
	$error = array();
	$files = array();
	
	$o3_lang = new o3_lang();
	$o3_lang->set_dir( O3_LANG_DIR );
	foreach ( $languages as $key => $value ) {
		$o3_lang->current( $value );
		$o3_lang->load( $collection == 'general' ? '' : $collection );
	}
	
	$max_plural_form = 1;
	foreach ( $o3_lang->list as $key => $value ) {
		foreach ( $value as $key_ => $value_ ) {										
			
			//check language prular form 
			$max_plural_form = max( $max_plural_form, isset($value_['o3_lang_plural_rule_id']) && isset($o3->lang->plural_rules[$value_['o3_lang_plural_rule_id'][0]]) ? $o3->lang->plural_rules[$value_['o3_lang_plural_rule_id'][0]]['form'] : 0 );			
		}
	}									 			
						
	$indexes = o3_post('index');					
	$plurals = o3_post('plural');
 
	foreach ( $languages as $key => $value ) {		
		$LANG = array();
		
		for ( $i = 0; $i < $max_plural_form; $i++ ) {
			$values = o3_post('value_'.$value.'_'.$i);				
			foreach ( $indexes as $key_ => $value_ ) {
				if ( strlen(trim($value_)) > 0 ) 
					$LANG[trim($value_)][$i] = $values[$key_];
			}			
		} 
		
		foreach ( $indexes as $key_ => $value_ ) {
			if ( strlen(trim($value_)) > 0 && is_array($plurals) ) { 
				$LANG[trim($value_)]['plural'] = in_array( $key_, $plurals ) ? 1 : 0;
			}
		}
		
		$file = O3_LANG_DIR.'/'.$value.( $collection == '' || $collection == 'general' ? '' : '-'.$collection ).'.json';
		$exists = file_exists($file);
		$content = $exists ? file_get_contents($file) : '';
		$files[] = array( 'file' => $file, 'data' => $LANG, 'exists' => $exists, 'content' => $content );
	}  
	if ( count($files) > 0 ) {
		foreach ( $files as $key => $value ) { 
			o3_write_file( $value['file'], json_encode($value['data']), 'w' );
		}
	} 

	o3\admin\functions\add_notification_msg('Changes are saved');
	
	
}
 
?>
<script type="text/javascript">
	var changed = false;
	
	(function($) {
		$(document).ready(function () { });
	})(jQuery); 
	
	function update() {
		var updateform = $('.update-form');
		updateform.find('input[name=cmd]').val('udt');		
		return true;
	}
	
	function removeIndex( index ) {
		$('.lang_row_'+index).css('display','none');
		$('.restore_lang_row_'+index).css('display','table-row');		
		/*
		$('.lang_row_'+index).css('height',50).find('.header,.vspacer10,.spacer10').css('opacity',0);
		setTimeout( function() { 
					$('.lang_row_'+index).find('.header,.vspacer10,.spacer10').css('display','none');
					$('.lang_row_'+index).find('.restore').css('display','block'); 
				}, 500 );
		$('.lang_row_'+index+' .restore').css( { 'top': '0px', 'opacity': 1 } ); 
		*/
		var old_val = $('.lang_row_'+index).find('input').val();	
		$('.lang_row_'+index).find('input').attr('old-val',old_val).val('');				
	}
	
	function unremoveIndex( index ) {
		$('.restore_lang_row_'+index).css('display','none');		
		$('.lang_row_'+index).css('display','table-row');		
		/*
		$('.lang_row_'+index).find('.header,.vspacer10,.spacer10').css('display','block');
		$('.lang_row_'+index).css('height','130px').find('.header,.vspacer10,.spacer10').css('opacity',1);
		$('.lang_row_'+index+' .restore').css( { 'top': '-40px', 'opacity': 0 } ); 	
		*/
		var old_val = $('.lang_row_'+index).find('input').attr('old-val');	
		$('.lang_row_'+index).find('input').val(old_val);				
	}
	
	function changeCollection( value ) {
		if ( value == '+' ) {
			alert("Please create an empty .json file in the language folder with\nthe name of the collection and refresh this page.");
		} else {
			if ( !changed || confirm('You may lose some changes. Are you sure you want to continue?') )
				window.location='index.php?load=lang&collection='+value;
		}
	}
	
	function newLanguage() {
		alert("Please create an empty .json file in the language folder with\nthe name of the language and refresh this page.");
	}
					
	function showPlural( checked, index ) {
		if ( checked ) {
			$('.plural_'+index+'_data').removeClass('nodisp');
		} else {
			$('.plural_'+index+'_data').addClass('nodisp');
		}
	}
								
</script>
<?php	

$languages = get_languages();
$collections = get_collections( $languages );

//create config display data
$configData = array();

//check if folder writable
$is_writable_O3_LANG_DIR = is_writable( O3_LANG_DIR );
$configData[] = array( 'name' => 'O3_LANG_DIR',
						   'value' => O3_LANG_DIR,
						   'description' => 'Directory containing JSON language files',
						   'status' => $is_writable_O3_LANG_DIR ? 'Writable' : 'Not writable',
						   'status_type' => $is_writable_O3_LANG_DIR ? 1 : 3 );

$configData[] = array( 'name' => 'O3_LANG_PLURAL_RULE_ID_INDEX', 
						   'value' => O3_LANG_PLURAL_RULE_ID_INDEX,
						   'description' => 'Default language index for plural rule',
						   'status' => '',
						   'status_type' => 0 );

$configData[] = array( 'name' => 'O3_LANG_DISPLAY_NAME_INDEX', 
						   'value' => O3_LANG_DISPLAY_NAME_INDEX,
						   'description' => 'Default language index for displaying language name',
						   'status' => '',
						   'status_type' => 0 );

?>
<div class="padding10">
	<h1>Lang</h1>

	<?php echo o3\admin\functions\generateConfigTable( $configData ); ?>

	<form action="index.php?load=lang&collection=<?php echo o3_html($collection)?>" method="post" class="update-form" onsubmit="return update()">
		<input type="hidden" value="" name="cmd">
	
		<div class="button_list">
			<input type="submit" value="+ Save changes">

			<input type="button" onclick="newLanguage()" value="+ New language">

		</div>

		<table class="main_table">
		<tr>
			<th class="w160px">
				<select id="collection" onchange="changeCollection(this.value)" class="w160px minw160px">
					<option value="">collection</option>
					<option value="general" <?php echo $collection == 'general' ? 'selected' : ''; ?>>general</option>
					<?php 
					foreach ( $collections as $key => $value ) {
						echo '<option value="'.o3_html($value).'" '.( $collection == $value ? 'selected' : '' ).'>'.o3_html($value).'</option>';
					}
					?>
					<option value="+">+ new collection</option>
				</select>
			</th>
			<th class="w160px">
				index
			</th>
			<?php 
			$o3_lang = new o3_lang();
			$o3_lang->set_dir( O3_LANG_DIR );
			foreach ( $languages as $key => $value ) {
				$o3_lang->current( $value );
				$o3_lang->load( $collection == 'general' ? '' : $collection );
				
			?>
				<th class="w160px">
					<?php echo o3_html($value); ?>
				</th>
			<?php
			}	

			$indexes = array();								
			$max_plural_form = 1;								
			foreach ( $o3_lang->list as $key => $value ) {
				foreach ( $value as $key_ => $value_ ) {										
					
					//check language prular form 
					$max_plural_form = max( $max_plural_form, isset($value_['o3_lang_plural_rule_id']) && isset($o3->lang->plural_rules[$value_['o3_lang_plural_rule_id'][0]]) ? $o3->lang->plural_rules[$value_['o3_lang_plural_rule_id'][0]]['form'] : 0 );			
												
					if ( $collection == $key_ || ( $collection == 'general' && $key == $key_ ) ) {
						foreach ( $value_ as $key__ => $value__ ) {								
							if ( !isset( $indexes[$key__] ) )
								$indexes[$key__] = array( 'index' => $key__, 'plural' => ( isset($value__['plural']) ? $value__['plural'] : '' ) );
						}
					}
				}
			}

			?>
			<th>&nbsp;</th>
		</tr>

		<tr>
			<td>
				<?php echo o3_html($collection); ?>
			</td>
			<td>
				<input type="text" name="index[]" value="" placeholder="New index">				
				
				<input type="checkbox" value="0" name="plural[]" id="plural_" onchange="showPlural( this.checked, '' )">
				<label for="plural_">Plural rule</label>				
			</td>	
			<?php 
			foreach ( $languages as $key => $value_ ) {
				?>
				<td class="lang_col_<?php echo $value_; ?>">
					<textarea name="value_<?php echo $value_?>_0[]" placeholder="New text"></textarea> 					
					<?php
					if ( $max_plural_form > 1 ) {
						for ( $i = 1; $i < $max_plural_form; $i++ ) {
					?>
						<textarea class="plural__data nodisp" name="value_<?php echo $value_?>_<?php echo $i?>[]" placeholder="New text"></textarea> 						
					<?php
						}
					}
					?>
				</td>									
			<?php
			}
			?>
			<td>&nbsp;</td>
		</tr>	
		
		<?php

		$j = 0; 
		foreach ( $indexes as $key => $value ) { 
		?>		
		<tr class="nodisp restore_lang_row_<?php echo $j; ?>">
			<td>				
				index removed
				<a href="javascript:unremoveIndex(<?php echo $j; ?>)" class="tool-icon" style="top: 0px;">undo</a>				
			</td>
		</tr>	
		<tr class="nodisp"><td></td></tr>					
		<tr class="lang_row_<?php echo $j; ?>">
			<td>
				<span><?php echo o3_html($collection); ?></span>
				<a href="javascript:removeIndex(<?php echo $j; ?>)" title="remove" class="remove-icon icon"></a>			
			</td>					
			<td>
				<input type="text" name="index[]" value="<?php echo o3_html($key); ?>">
				 
				 <input type="checkbox" value="<?php echo ($j+1);?>" <?php echo $value['plural']?'checked':''?> name="plural[]" id="plural_<?php echo $j;?>" onchange="showPlural( this.checked , '<?php echo $j;?>')">
				 <label for="plural_<?php echo $j;?>">Plural rule</label>
				 
			</td>					
			<?php
			foreach ( $languages as $key_ => $value_ ) {  									
				?>
				<td class="lang_col_<?php echo $value_; ?>">
					<textarea name="value_<?php echo $value_?>_0[]"><?php echo o3_html($o3_lang->list[$value_][$collection=='general'?$value_:$collection][$key][0]); ?></textarea> 					
					<?php
					if ( $max_plural_form > 1 ) {
						for ( $i = 1; $i < $max_plural_form; $i++ ) {
					?>						
						<textarea class="plural_<?php echo $j?>_data <?php echo $value['plural']?'':'nodisp'?>" name="value_<?php echo $value_?>_<?php echo $i?>[]"><?php echo o3_html($o3_lang->list[$value_][$collection=='general'?$value_:$collection][$key][$i]); ?></textarea> 						
					<?php
						}
					}
					?>
				</td>							
		<?php
			}		
		?>
			<td>&nbsp;</td>
		</tr>
		<?php
			$j++;
		} 
		?>



		</table>

	 
	</form>
</div>