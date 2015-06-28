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
* @param integer $flags - O3_IMAGE_IGNORE_ASPECT, O3_IMAGE_FLATTEN, O3_IMAGE_CLIP, O3_IMAGE_MATTE, O3_IMAGE_SHRINK_LARGER, O3_IMAGE_ENLARGE_SMALLER, O3_IMAGE_FILL_AREA
* @param integer $dpi = 72
* @param string $background Hex code for background color
*/
function o3_image_resize( $source, $destination = '', $width = 1024, $height = 768, $flags = null, $dpi = O3_IMAGE_WEB_DPI, $background = '' ) {
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
			$destination = o3_cache_file( 'image-'.$width.'x'.$height.'-'.strtolower($flags.$dpi.$background.filesize($source).filemtime($source).'-'.basename($source)) );
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

				$command = "\"".O3_IMAGE_MAGIC."\" \"".$source."\" -profile \"USWebCoatedSWOP.icc\" ".( $flags & O3_IMAGE_FLATTEN ? '-flatten' : '' )." ".( $flags & O3_IMAGE_CLIP ? '-clip' : '')." ".( $background !== '' ? '-background '.$background : '')." -quality  90% -density ".$dpi." -resize \"".$width."x".$height.$size_flag."\" -profile \"sRGB.icc\" \"".$destination."\"";
				exec( $command, $out, $rcode );
				if ( file_exists($destination) )
					return $destination;
			} else {
				//todo
			}
		} else {
			return $destination;
		}
	}
	return false;
}


?>