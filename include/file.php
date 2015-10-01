<?php 

/**
 * O3 Engine file/folder handler
 *
 * Functions for handling files and folders.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
/**
* Read sub folders and files in a folder with details.
*
* Index of returned items: <br>
* boolean <b>file</b> True if is a file else false <br>
*	string <b>name</b> Name with extension <br>
* string <b>filename</b> Name without extension <br>
* string <b>extension</b> <br>
* string <b>path</b> Full path to the file/folder <br>
* int <b>size</b> Total size of the file/folder with subfolders in bytes  <br>
* int <b>mdate</b> Timestamp when the content of the file/folder was changed	
*
* @example example.php
* <b>//Read and display content of sample</b><br>
* $files = o3_read_path( '/sample' ); <br>
* if ( count() ) { <br>
*		foreach ( $files as $value ) <br>
*			echo ( $value['file'] ? 'File: ' : 'Folder: ' ).$value['filename']; <br>
*	}
*
* @param string $dir Path to the folder	 
*
* @return array[]
*/	
function o3_read_path( $dir ) {

	$relative_path = o3_get_caller_path();
	if ( $relative_path != '' && !realpath($dir) )
		$dir = $relative_path.'/'.$dir;

	if ( !is_readable($dir) || !is_dir($dir) )
	    return false;

	$a = array();
	if ( $handle = opendir($dir) ) {
		while (false !== ($file = readdir($handle))) {
			if ($file!='..' && $file!='.') {
				$aux['file'] = !is_dir($dir."/".$file);				
				$aux['name'] = $file;
				$pathinfo = pathinfo($aux['name']);
				$aux['filename'] = $pathinfo['filename'];
				$aux['extension'] = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
				$aux['path'] = $dir."/".$file;
				$aux['size'] = $aux['file'] ? filesize($dir."/".$file) : sprintf("%u",o3_path_size($dir."/".$file));
				$aux['mdate']=filemtime($dir."/".$file);								
				$a[] = $aux;
			}
		}
		closedir($handle);
	} else {		
		trigger_error( 'Can\'t read content of directory '.o3_html($dir).'.' );
		return false;
	}
	return $a;
}

/**
* Get the size of a folder in bytes. The total size with subfolders and files.
*
* @param string $path Path to the folder
*
* @return int
*/	
function o3_path_size( $path ) {
	
	$relative_path = o3_get_caller_path();
	if ( $relative_path != '' && !realpath($path) )
		$path = $relative_path.'/'.$path;

	if ( !is_readable($path) || !is_dir($path) )
	    return false;
	
	$d_stack[] = $path;
	$size = 0;
	
	do {
	    $path = array_shift($d_stack);
	    if ($handle = opendir($path)) {
	    	while (false !== ($file = readdir($handle))) {
	        if ($file != '.' && $file != '..' && is_readable($path . "/" . $file)) {
	            if (is_dir($path . "/" . $file)) {
	                $d_stack[] = $path . "/" . $file;
	            }
	            $size += filesize($path . "/" . $file);
	        }	
	      }
	    } else {
	    	trigger_error( 'Can\'t read content of directory '.o3_html($path).'.' );
	    }
	    closedir($handle);
	} while (count($d_stack) > 0);
	
	return $size;
}

/**
* Get the extension of a file.
*
* @param string $path Path to the file
*
* @return string
*/	
function o3_extension( $path ) {
	preg_match('/\.[^\.]+$/i',$path,$ext);
	return isset($ext[0]) ? str_replace('.', '', $ext[0]) : '';
}

/**
* Get the extension of a file.
*
* @param string $path Path to the file
* @param string $filename Output file name
* @param string $mime Output conten type
* @param string $disposition inline or attachment Defaut: inline
*
* @return boolean
*/	
function o3_output_file( $path, $filename, $mime = 'application/octet-stream', $disposition = 'inline' ) { 
	
	if ( !file_exists($path) )
		return false;
	
	$fh = @fopen( $path, 'rb' );	
	if( !$fh )
		return false;
	
	header("Content-Type: ".$mime);	
	header('Content-Disposition: '.$disposition.'; filename="'.$filename.'"');
	header('Cache-Control: public, must-revalidate, max-age=0, post-check=0, pre-check=0');
	header('Pragma: no-cache');  
	header('Content-Length: '.filesize($path));
	header("Last-Modified: ".date( 'r', filemtime($path) ));
	
	while(!feof($fh)) {
		print(@fread($fh, 1024*8));
		ob_flush();
		flush();
	}

	return true;

	/*
	if ( !file_exists($path) )
		return false;
	$filesize = filesize($path);		
	$time = date( 'r', filemtime($path) );

	$fh = @fopen( $path, 'rb' );	
	if( !$fh )
		return false;

	$begin = 0;
	$end = $size;

	if(isset($_SERVER['HTTP_RANGE'])) { 
		if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) { 
			$begin = intval($matches[0]);
			if( !empty($matches[1]) )
				$end = intval($matches[1]);
		}
	}

	if ( $begin > 0 || $end < $size )
		header('HTTP/1.0 206 Partial Content');
	else
		header('HTTP/1.0 200 OK');
 
	header("Content-Type: ".$mime);
	header('Cache-Control: public, must-revalidate, max-age=0');
	header('Pragma: no-cache');  
	header('Accept-Ranges: bytes');
	header('Content-Length: '.( $end - $begin ));
	header("Content-Range: bytes $begin - $end / $size");
	header('Content-Disposition: inline; filename="'.$filename.'"');
	header("Content-Transfer-Encoding: binary\n");
	header("Last-Modified: ".$time);
	header('Connection: close');  
	 
	$cur=$begin;
	fseek( $fm, $begin, 0 );

	while ( !feof($fm) && $cur < $end && ( connection_status() == 0 ) ) {
		print fread( $fm, min( 1024 * 16, $end - $cur ) );
		$cur += 1024 * 16;
	}
	*/
}

/*append & create*/

/**
* Write into a file.
* The default mode is 'w' - open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length. If the file does not exist, attempt to create it. 
*
* @param string $file Path to the file. Same as first parameter $filename in http://www.php.net/manual/en/function.fopen.php
* @param string $buffer (optional) String that is to be written	
* @param string $flag (optional) The mode parameter specifies the type of access you require to the stream. Same as first parameter $mode in http://www.php.net/manual/en/function.fopen.php
*
* @return string
*/
function o3_write_file( $file, $buffer = "", $flag = 'w' ) {	
	$relative_path = o3_get_caller_path();
	if ( $relative_path != '' && !realpath(dirname($file)) )
		$file = $relative_path.'/'.$file;	
	$error = true;
	$h = fopen( $file, $flag );
	if ( $h ) {		
		$error = fwrite( $h, $buffer ) === false;
		fclose( $h );
	}		
	if ( $error ) 
		trigger_error( 'Can\'t open file '.o3_html($file).' for write.' );
	return !$error;	
}

/**
* Write into the top of a file
* The default mode is 'w' - open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length. If the file does not exist, attempt to create it. 
*
* @param string $file Path to the file. Same as first parameter $filename in http://www.php.net/manual/en/function.fopen.php
* @param string $buffer (optional) String that is to be written		
*
* @return string
*/
function o3_write2top_file( $file, $buffer = "" ) {
	$relative_path = o3_get_caller_path();
	if ( $relative_path != '' && !realpath(dirname($file)) )
		$file = $relative_path.'/'.$file;
	$contents = file_get_contents( $file );
	if ( $contents !== false ) {
		$contents = $buffer.$contents;
		return o3_write_file( $file, $contents, 'w' );
	}
	trigger_error( 'Can\'t read content of '.o3_html($file).'.' );	
	return false;
}


/**
* Delete a file.
*
* @param string $file Path to the file.
*
* @return boolean
*/
function o3_unlink( $file ) {	
	//get caller script path, we need for relaive paths
	$relative_path = o3_get_caller_path();
	if ( $relative_path != '' && !realpath(dirname($file)) )
		$file = $relative_path.'/'.$file;
	if ( file_exists( $file ) && is_file($file) && is_writable($file) )
		return unlink( $file );
	return true;
}

/*
* Create cache file name
*/
function o3_cache_file( $filename = '' ) {
	$ext = o3_extension($filename);
	if ( $filename == '' || $filename == '.'.$ext ) {
		$file = md5(o3_micro_time());
		while ( file_exists(O3_CACHE_DIR.'/'.$file.( $ext != '' ? '.'.$ext : '' )) )
			$file = md5(o3_micro_time());
	} else {
		$file = basename($filename,'.'.$ext);
	}
	return O3_CACHE_DIR.'/'.$file.( $ext != '' ? '.'.$ext : '' );
}

/*
* Create temporary cache file name
* @param string $filename
* @param Lifetime of the file. Default: 1800 (30min)
*/
function o3_temp_cache_file( $filename = '', $lifetime = 1800 ) {
	$ext = o3_extension($filename);
	if ( $filename == '' ) {
		$file = md5(o3_micro_time());
		while ( file_exists(O3_CACHE_DIR.'/'.$file.( $ext != '' ? '.'.$ext : '' )) )
			$file = md5(o3_micro_time());
	} else {
		$file = basename($filename,'.'.$ext);
	}
	return O3_CACHE_DIR.'/'.(time()+$lifetime).'-'.$file.( $ext != '' ? '.'.$ext : '' );
}

/*
* Create url from cache path
*/
function o3_cache_url( $filepath ) {
	$filename = basename($filepath);
	return O3_CACHE_URL.'/'.$filename;
}

/**
* Get function caller script path
* @return string/boolean False if unable to find path else path name
*/
function o3_get_caller_path() {
	$trace = debug_backtrace();
	/*
	$debug_backtrace = false;
	for ($i = 0; $i < count($trace); ++$i) {
        if ( 'debug' == $trace[$i]['function'] ) {
            if (isset($trace[$i + 1]['class'])) {
                $debug_backtrace = array(
                    'class' => $trace[$i + 1]['class'],
                    'line' => $trace[$i]['line'],
                );
            }
            $debug_backtrace = array(
                'file' => $trace[$i]['file'],
                'line' => $trace[$i]['line'],
            );
        }
    }
    if ( $debug_backtrace === false )
    	$debug_backtrace = $trace[0];
   	*/
    if ( isset($trace[1]) ) {
		return isset($trace[1]['file']) ? str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname($trace[1]['file']))) : '';		
    }
    return false;
}

/*
* Get/set max post size allowed by the server in bytes
*/
function o3_post_max_size() {
	$args = func_get_args();
	if ( count($args) > 0 ) {
		ini_set( 'post_max_size', $args[0] );
	} else { 
	    $mul = substr(ini_get('post_max_size'), -1);
	    $mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
	    return $mul * intval(ini_get('post_max_size'));
	}
}

/*
* Get/set max upload size allowed by the server in bytes
*/
function o3_upload_max_filesize() {
	$args = func_get_args();
	if ( count($args) > 0 ) {
		ini_set( 'upload_max_filesize', $args[0] );
	} else { 
	    $mul = substr(ini_get('upload_max_filesize'), -1);
	    $mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
	    return $mul * intval(ini_get('upload_max_filesize'));
	}
}
	
/*
* Get max allowed upload size combine with post and upload max size
*/
function o3_upload_max_size() {
	return min( o3_post_max_size(), o3_upload_max_filesize() );
}

/*
* Get content of URL
*
* @param string $url
* @param int $timeout
*
* @return string/boolean Content of URL or false if URL not found
*/
function o3_url_get_contents( $url, $timeout = 5 ) {	
	$result = false;

	//check if curl_init available
    if ( is_callable('curl_init') ) {
        try {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                    
               
            $result = @curl_exec($ch);
               
            curl_close($ch);
        } catch (Exception $e) {
        }
    } else {
    	$context = stream_context_create( 
	        			array( 
	        				'http'=> array( 
	        					'timeout' => $timeout,
	        				), 
	        				'https'=> array(
	        					'timeout' => $timeout,
	        				),        			
		        			'ssl' => array(
						        'verify_peer' => false,
						    )
		        		)
	        		);        	
        $result = @file_get_contents( $url, false, $context );
    }

    return $result;
}

?>