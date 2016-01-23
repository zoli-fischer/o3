<?php 

/**
 * O3 Engine image
 *
 * Functions for handling image/picture files
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

define('O3_IMAGE_IGNORE_ASPECT',1);
define('O3_IMAGE_FLATTEN',2);
define('O3_IMAGE_CLIP',4);
define('O3_IMAGE_SHRINK_LARGER',8);
define('O3_IMAGE_ENLARGE_SMALLER',16);
define('O3_IMAGE_FILL_AREA',32);
define('O3_IMAGE_OPTIMIZE',64);
define('O3_IMAGE_CROP_CENTER',128);


//standard dpi for web based picture
define('O3_IMAGE_WEB_DPI',72);

/*
* Check if image magic is installed on the system
*/
function is_imagic() {
	exec("\"".O3_IMAGE_MAGIC."\" -version", $out, $rcode);
	return $rcode === 0;
}

/*
* Resize an image
*
* @param strin $source 
* @param string $destination If empty string cache file created
* @param integer $width
* @param integer $height
* @param integer $quality 1 - 100
* @param integer $flags - O3_IMAGE_IGNORE_ASPECT, O3_IMAGE_FLATTEN, O3_IMAGE_CLIP, O3_IMAGE_MATTE, O3_IMAGE_SHRINK_LARGER, O3_IMAGE_ENLARGE_SMALLER, O3_IMAGE_FILL_AREA, O3_IMAGE_OPTIMIZE, O3_IMAGE_CROP_CENTER
* @param integer $dpi = 72
* @param string $background Hex code for background color
*/
function o3_image_resize( $source, $destination = '', $width = 1024, $height = 768, $flags = null, $quality = 90, $dpi = O3_IMAGE_WEB_DPI, $background = '' ) {
	//set default
	$flags = $flags === null ? 0 : $flags;

	//get caller script path, we need for relaive paths
	$relative_path = o3_get_caller_path();

	//check for source path
	if ( !realpath(dirname($source)) ) {		
		if ( $relative_path != '' )		
			$source = $relative_path.'/'.$source;
	}

	if ( file_exists($source) && is_readable($source) ) {		
		if ( $destination == '' ) {
			$destination = o3_cache_file( 'image-'.$width.'x'.$height.'x'.$dpi.'x'.$quality.'-'.strtolower($flags.$dpi.$background.filesize($source).filemtime($source).'-'.basename($source)) );
		} else {
			//check for destination path
			if ( !realpath(dirname($destination)) ) {		
				if ( $relative_path != '' )		
					$destination = $relative_path.'/'.$destination;
			}
		}		
		
		if ( !file_exists($destination) ) {			
			if ( is_imagic() ) {						
				
				$size_flag = '';			
				if ( $size_flag == '' )
					$size_flag = ( $flags & O3_IMAGE_SHRINK_LARGER ? '>' : $size_flag );
				if ( $size_flag == '' )
					$size_flag = ( $flags & O3_IMAGE_ENLARGE_SMALLER ? '<' : $size_flag );
				if ( $size_flag == '' )
					$size_flag = ( $flags & O3_IMAGE_IGNORE_ASPECT ? '!' : $size_flag );
				if ( $size_flag == '' )
					$size_flag = ( $flags & O3_IMAGE_FILL_AREA ? '^' : $size_flag );
				if ( $flags & O3_IMAGE_CROP_CENTER )
					$size_flag = '^';

				$command = "\"".O3_IMAGE_MAGIC."\" \"".$source."\" -profile \"USWebCoatedSWOP.icc\" ".( $flags & O3_IMAGE_FLATTEN ? '-flatten' : '' )." ".( $flags & O3_IMAGE_CLIP ? '-clip' : '')." ".( $background !== '' ? '-background '.$background : '')." -quality ".intval( ( $flags & O3_IMAGE_OPTIMIZE ) ? 100 : $quality )."% -density ".$dpi." ".( $width > 0 && $height > 0 ? " -resize \"".$width."x".$height.$size_flag."\"" : "" )." ".( ( $flags & O3_IMAGE_CROP_CENTER ) && $width > 0 && $height > 0 ?  " -gravity center -crop \"".$width."x".$height."+0+0\" " : "" )." -profile \"sRGB.icc\" \"".$destination."\"";								
				exec( $command, $out, $rcode );
				if ( file_exists($destination) ) {

					//optimize image if needed
					if ( $flags & O3_IMAGE_OPTIMIZE )
						o3_image_optimize( $destination, null, $quality );

					return $destination;				
				}
			} else {
				//todo
			}
		} else {
			return $destination;
		}
	}
	return false;
}

/*
* Resize an image and return chache url
*
* @param strin $source 
* @param integer $width
* @param integer $height
* @param integer $quality 1 - 100
* @param integer $flags - O3_IMAGE_IGNORE_ASPECT, O3_IMAGE_FLATTEN, O3_IMAGE_CLIP, O3_IMAGE_MATTE, O3_IMAGE_SHRINK_LARGER, O3_IMAGE_ENLARGE_SMALLER, O3_IMAGE_FILL_AREA, O3_IMAGE_OPTIMIZE, O3_IMAGE_CROP_CENTER
* @param integer $dpi = 72
* @param string $background Hex code for background color
*/
function o3_image_resize_cache_url( $source, $width = 1024, $height = 768, $flags = null, $quality = 90, $dpi = O3_IMAGE_WEB_DPI, $background = '' ) {
	//get caller script path, we need for relaive paths
	$relative_path = o3_get_caller_path();

	//check for source path
	if ( !realpath(dirname($source)) ) {		
		if ( $relative_path != '' )		
			$source = $relative_path.'/'.$source;
	}

	if ( file_exists($source) && is_readable($source) ) {		
		$destination = o3_cache_file( 'image-'.$width.'x'.$height.'x'.$dpi.'x'.$quality.'-'.strtolower($flags.$dpi.$background.filesize($source).filemtime($source).'-'.basename($source)) );
		if ( !file_exists($destination) ) {
			$destination = o3_image_resize( $source, $destination, $width, $height, $flags, $quality, $dpi, $background );
			if ( file_exists($destination) ) {
				return O3_CACHE_URL.'/'.basename($destination);
			}
		} else {
			return O3_CACHE_URL.'/'.basename($destination);
		}
	}

	return false;
}

/*
* Check if image magic is installed on the system
*/
function is_pngcrush() {
	exec("\"".O3_IMAGE_PNGCRUSH."\" -version", $out, $rcode);
	return $rcode === 0;
}

/*
* Check if image magic is installed on the system
*/
function is_jpegoptim() {	
	exec("\"".O3_IMAGE_JPEGOPTIM."\" -V", $out, $rcode);
	return $rcode === 0;
}

/*
* Optimize image for web
* Optimized images needed for high speed score in google speed insight
*
* @param string $source Path to image to optimize
* @param string $destination Optimized image path. If omited the soruce will be the target.
* @param number $quality 0 - 100, quality of lossy image, 0 - keep original
* @return boolean true if the image was optimized
*/
function o3_image_optimize( $src, $target = null, $quality = 90 ) {
	$target = $target === null ? $src : $target;

	//get caller script path, we need for relaive paths
	$relative_path = o3_get_caller_path();

	//check for source path
	if ( !realpath(dirname($src)) ) {		
		if ( $relative_path != '' )		
			$src = $relative_path.'/'.$src;
	}
	
	//check for source path
	if ( !realpath(dirname($target)) ) {		
		if ( $relative_path != '' )		
			$target = $relative_path.'/'.$target;
	}

	$extension = strtolower(o3_extension($src)); 
	switch ( $extension ) {
		case 'png':
			if ( is_pngcrush() ) {
				$filename = basename($src);
				//create temp file
				exec('"'.O3_IMAGE_PNGCRUSH.'" -rem alla -nofilecheck -reduce -m 7 "'.$src.'" "'.O3_TEMP_DIR.'/'.$filename.'"');
				if ( file_exists(O3_TEMP_DIR.'/'.$filename) && filesize(O3_TEMP_DIR.'/'.$filename) > 0 ) {
					//remove target if exists
					o3_unlink($target);
					//copy temp file to target
					copy( O3_TEMP_DIR.'/'.$filename, $target );
					//remove temp file
					o3_unlink(O3_TEMP_DIR.'/'.$filename);
					return true;
				}
			}
			break;
		case 'jpg':
		case 'jpeg':
			if ( is_jpegoptim() ) {
				$filename = basename($src);
				//create temp file
				exec('"'.O3_IMAGE_JPEGOPTIM.'" --strip-all '.( $quality > 0 ? '--m='.intval($quality) : '' ).' -f -o -d "'.O3_TEMP_DIR.'" "'.$src.'"');
				if ( file_exists(O3_TEMP_DIR.'/'.$filename) && filesize(O3_TEMP_DIR.'/'.$filename) > 0 ) {
					//remove target if exists
					o3_unlink($target);
					//copy temp file to target
					copy( O3_TEMP_DIR.'/'.$filename, $target );
					//remove temp file
					o3_unlink(O3_TEMP_DIR.'/'.$filename);
					return true;
				}
			}
			break;
	}
	return false;
}

?>