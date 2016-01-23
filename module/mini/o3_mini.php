<?php

/**
 * CSS/JS minify module for O3 Engine
 *
 * Main class file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * CSS/JS minifier class 
 */
class o3_mini {
	
	/** mixed Parent object */
	public $parent = null;
	
	/** string Path to cache CSS dir */
	public $css_cache_dir = O3_MINI_CSS_CACHE_DIR;
	
	/** string Relative URL to cache CSS dir */
	public $css_cache_url = O3_MINI_CSS_CACHE_URL;
	
	/** string Path to cache JS dir */
	public $js_cache_dir = O3_MINI_JS_CACHE_DIR;
	
	/** string Relative URL to cache JS dir */
	public $js_cache_url = O3_MINI_JS_CACHE_URL;
	
	/** boolean Allow minimize */
	public $minimize = O3_MINI_MINIMIZE;

	/** boolean Allow minimize css */
	public $minimize_css = O3_MINI_CSS;

	/** boolean Allow minimize js */
	public $minimize_js = O3_MINI_JS;

	/** integer CSS cache file uniq id */
	public $cache_uniqid_css = 'mini';

	/** integer JS cache file uniq id */
	public $cache_uniqid_js = 'mini';

	/**
	* O3 mini constructor
	*
	* @return void
	*/		
	public function __construct() {
		
	}

	/**
	 * Allow minimize files 	 
	 * @param boolean $value (optional) Default value: true
	 *
	 * @return void
	 */
	public function allow_mini( $value = true ) {
		$this->minimize = $value;
	}

	/**
	 * Allow minimize js files 	 
	 * @param boolean $value (optional) Default value: true
	 *
	 * @return void
	 */
	public function allow_mini_js( $value = true ) {
		$this->minimize_js = $value;
	}

	/**
	 * Allow minimize css files 	 
	 * @param boolean $value (optional) Default value: true
	 *
	 * @return void
	 */
	public function allow_mini_css( $value = true ) {
		$this->minimize_css = $value;
	}

	/*
	* Parse less and compress css
	*/
	public function less_parse( $content ) {

		require_once('o3_less_parser.php');
		$parser = new o3\module\mini\o3_less_parser();
		$parser->parse( $content, '' );
		$content .= $parser->getCss();
		
		//minimize if needed
		if ( $this->minimize && $this->minimize_css ) {
			require_once('o3_css_compressor.php');
			$content = o3\module\mini\o3_css_compressor::process( $content );
		}

		return $content;
	}

	/**
	 * Genarate cache file in the cache folder and return the filename
	 *
	 * string LESS CSS files
	 * @return string Cache filename
	 */		
  	public function less_cache() {
  		$args = func_get_args();
		$buffer = '';
		$cache_name = '';		
		
		$files = array();
		foreach ( $args as $value ) {
			if ( !is_array($value) ) {
				$file = $value;
				//generate temp name
				if ( !file_exists($file) || !is_file($file) || !is_readable($file) ) {
					trigger_error('Unable to open input file: '.$file);
				} else {				
					//store file path
					$files[] = $file;
					
					$cache_name .= filemtime($file).basename($file); 			
				}
			} else {
				foreach ( $value as $value2 ) {
					$file = $value2;
					//generate temp name
					if ( !file_exists($file) || !is_file($file) || !is_readable($file) ) {
						trigger_error('Unable to open input file: '.$file);
					} else {					
						//store file path
						$files[] = $file;
						
						$cache_name .= filemtime($file).basename($file); 	
					}
				}
			}
		}


		
		$cache_name = md5( $cache_name.( $this->minimize && $this->minimize_css ? 1 : 0 ) );
		$cache_file_name = $this->cache_uniqid_css.'-'.$cache_name.'.css';
		if ( file_exists($this->css_cache_dir.'/'.$cache_file_name) && 
			 is_readable($this->css_cache_dir.'/'.$cache_file_name) &&
			 is_file($this->css_cache_dir.'/'.$cache_file_name) ) {
			return $cache_file_name;
		} else {
			//read css contents
			$content = '';
			require_once('o3_less_parser.php');
			foreach ( $files as $value ) {
			    $parser = new o3\module\mini\o3_less_parser();
			    $parser->parseFile( $value, '' );
			    $content .= $parser->getCss()."\n\n";
			}

			//minimize if needed
			if ( $this->minimize && $this->minimize_css ) {
				require_once('o3_css_compressor.php');
				$content = o3\module\mini\o3_css_compressor::process( $content );
			}

			//create cache file				
			file_put_contents( $this->css_cache_dir.'/'.$cache_file_name, $content );

			if ( file_exists($this->css_cache_dir.'/'.$cache_file_name) && 
				 is_readable($this->css_cache_dir.'/'.$cache_file_name) &&
				 is_file($this->css_cache_dir.'/'.$cache_file_name) )
				return $cache_file_name;
		}

		trigger_error( 'Can\'t create CSS cache file.' );
		return false;
	}

  	/**
	 * Genarate cache file in the cache folder and return the filename
	 *
	 * string CSS files
	 * @return string Cache filename
	 */		
  	public function css_cache() {
  		$args = func_get_args();
		$buffer = '';
		$cache_name = '';

		$files = array();
		foreach ( $args as $value ) {
			if ( !is_array($value) ) {
				$file = $value;
				//generate temp name
				if ( !file_exists($file) || !is_file($file) || !is_readable($file) ) {
					trigger_error('Unable to open input file: '.$file);
				} else {				
					//store file path
					$files[] = $file;
					
					$cache_name .= filemtime($file).basename($file); 			
				}
			} else {
				foreach ( $value as $value2 ) {
					$file = $value2;
					//generate temp name
					if ( !file_exists($file) || !is_file($file) || !is_readable($file) ) {
						trigger_error('Unable to open input file: '.$file);
					} else {					
						//store file path
						$files[] = $file;
						
						$cache_name .= filemtime($file).basename($file); 	
					}
				}
			}
		}
		
		$cache_name = md5( $cache_name.( $this->minimize && $this->minimize_css ? 1 : 0 ) );
		$cache_file_name = $this->cache_uniqid_css.'-'.$cache_name.'.css';

		if ( file_exists($this->css_cache_dir.'/'.$cache_file_name) && 
			 is_readable($this->css_cache_dir.'/'.$cache_file_name) &&
			 is_file($this->css_cache_dir.'/'.$cache_file_name) ) {
			return $cache_file_name;
		} else {
			//read css contents
			$content = '';
			foreach ( $files as $value ) {

				switch (strtolower(o3_extension($value))) {
					case 'less':
						require_once('o3_less_parser.php');
						$parser = new o3\module\mini\o3_less_parser();
					    $parser->parseFile( $value, '' );
					    $content .= $parser->getCss()."\n\n";
						break;
					default:
						$content .= file_get_contents( $value )."\n\n";
						break;
				}
				
			}

			//minimize if needed
			if ( $this->minimize && $this->minimize_css ) {
				require_once('o3_css_compressor.php');
				$content = o3\module\mini\o3_css_compressor::process( $content );
			}

			//create cache file				
			file_put_contents( $this->css_cache_dir.'/'.$cache_file_name, $content );

			if ( file_exists($this->css_cache_dir.'/'.$cache_file_name) && 
				 is_readable($this->css_cache_dir.'/'.$cache_file_name) &&
				 is_file($this->css_cache_dir.'/'.$cache_file_name) )
				return $cache_file_name;
		}

		trigger_error( 'Can\'t create CSS cache file.' );
		return false;
	}
	
	/**
	 * Genarate url to cache file 
	 *
	 * string/array CSS files
	 * @return string Cache filename
	 */	
	public function css_cache_url() {
		$args = func_get_args();

		//get caller script path, we need for relaive paths
		$relative_path = o3_get_caller_path();
		if ( $relative_path != '' )
			for ( $i =0; $i < count($args); $i++ ) {
				if ( is_array($args[$i])) {					
					for ( $j =0; $j < count($args[$i]); $j++ ) {
						if ( !realpath($args[$i][$j]) ) 
							$args[$i][$j] = $relative_path.'/'.$args[$i][$j];
					}
				} else {
					if ( !realpath($args[$i]) )
						$args[$i] = $relative_path.'/'.$args[$i];			
				}
			}

  		$cache_file = call_user_func_array( array( $this, 'css_cache' ), $args );
		if ( $cache_file !== false )
			return $this->css_cache_url.'/'.o3_remove_more('/',2,$cache_file);
		return false;
	}
	
	/**
	 * Genarate link tag with cache file 
	 *
	 * string/array CSS files
	 * @return string Link tags of files
	 */		
	public function css_link() {
		$args = func_get_args();

		//get caller script path, we need for relaive paths
		$relative_path = o3_get_caller_path();
		if ( $relative_path != '' )
			for ( $i =0; $i < count($args); $i++ ) {
				if ( is_array($args[$i])) {					
					for ( $j =0; $j < count($args[$i]); $j++ ) {
						if ( !realpath($args[$i][$j]) ) 
							$args[$i][$j] = $relative_path.'/'.$args[$i][$j];
					}
				} else {
					if ( !realpath($args[$i]) )
						$args[$i] = $relative_path.'/'.$args[$i];			
				}
			}

		$cache_file = call_user_func_array( array( $this, 'css_cache' ), $args );
		if ( $cache_file !== false )
			return '<link href="'.$this->css_cache_url.'/'.o3_remove_more('/',2,$cache_file).'" rel="stylesheet" type="text/css" />';
		return ''; 
	}

	/**
	 * Genarate url to cache file 
	 *
	 * string/array LESS CSS files
	 * @return string Cache filename
	 */	
	public function less_cache_url() {
		$args = func_get_args();

		//get caller script path, we need for relaive paths
		$relative_path = o3_get_caller_path();
		if ( $relative_path != '' )
			for ( $i =0; $i < count($args); $i++ ) {
				if ( is_array($args[$i])) {					
					for ( $j =0; $j < count($args[$i]); $j++ ) {
						if ( !realpath($args[$i][$j]) ) 
							$args[$i][$j] = $relative_path.'/'.$args[$i][$j];
					}
				} else {
					if ( !realpath($args[$i]) )
						$args[$i] = $relative_path.'/'.$args[$i];			
				}
			}

  		$cache_file = call_user_func_array( array( $this, 'less_cache' ), $args );
		if ( $cache_file !== false )
			return $this->css_cache_url.'/'.o3_remove_more('/',2,$cache_file);
		return false;
	}

	/**
	 * Genarate link tag with cache file 
	 *
	 * string/array LESS CSS files
	 * @return string Link tags of files
	 */		
	public function less_link() {
		$args = func_get_args();

		//get caller script path, we need for relaive paths
		$relative_path = o3_get_caller_path();
		if ( $relative_path != '' )
			for ( $i =0; $i < count($args); $i++ ) {
				if ( is_array($args[$i])) {					
					for ( $j =0; $j < count($args[$i]); $j++ ) {
						if ( !realpath($args[$i][$j]) ) 
							$args[$i][$j] = $relative_path.'/'.$args[$i][$j];
					}
				} else {
					if ( !realpath($args[$i]) )
						$args[$i] = $relative_path.'/'.$args[$i];			
				}
			}

		$cache_file = call_user_func_array( array( $this, 'less_cache' ), $args );
		if ( $cache_file !== false )
			return '<link href="'.$this->css_cache_url.'/'.o3_remove_more('/',2,$cache_file).'" rel="stylesheet" type="text/css" />';
		return ''; 
	}

	/**
	 * Get list of css cache files
	 * @return array
	 */
	public function get_css_cache_files() {
		$return = array();
		$css_files = glob(preg_replace('/(\*|\?|\[)/', '[$1]', $this->css_cache_dir.'/').'*.css');
		if ( count($css_files) > 0 )
			foreach ( $css_files as $filename ) {
				$basename = basename($filename);
				$_ = explode( '-', $basename );
				$index = $_[0];
				if ( $index == $this->cache_uniqid_css ) {
					$time = intval($_[1]);
					$md5 = $_[2];
					if ( time() <= $time ) {
						$return[] = $filename;
					} else {
						o3_unlink( $filename );
					}
				}
			}			
		return $return;
   	}

   	/**
	 * Get list of js cache files
	 * @return array
	 */
	public function get_js_cache_files() {
		$return = array();
		$js_files = glob(preg_replace('/(\*|\?|\[)/', '[$1]', $this->js_cache_dir.'/').'*.js');
		if ( count($js_files) > 0 )
			foreach ( $js_files as $filename ) {
				$basename = basename($filename);
				$_ = explode( '-', $basename );
				$index = $_[0];
				if ( $index == $this->cache_uniqid_js ) {
					$time = intval($_[1]);
					$md5 = $_[2];
					if ( time() <= $time ) {
						$return[] = $filename;
					} else {
						o3_unlink( $filename );
					}
				}
			}			
		return $return;
   	}

   	/**
	 * Delete all JS cache files
	 * @return void
	 */
	public function clear_all_js() {
		$return = array();
		$js_files = glob(preg_replace('/(\*|\?|\[)/', '[$1]', $this->js_cache_dir.'/').'*.js');
		if ( count($js_files) > 0 )
			foreach ( $js_files as $filename ) {
				$basename = basename($filename);
				$_ = explode( '-', $basename );
				$index = $_[0];
				if ( $index == $this->cache_uniqid_js )
					o3_unlink( $filename );				
			}
   	}

	/**
	 * Delete all CSS cache files
	 * @return void
	 */
	public function clear_all_css() {
		$return = array();
		$css_files = glob(preg_replace('/(\*|\?|\[)/', '[$1]', $this->css_cache_dir.'/').'*.css');
		if ( count($css_files) > 0 )
			foreach ( $css_files as $filename ) {
				$basename = basename($filename);
				$_ = explode( '-', $basename );
				$index = $_[0];
				if ( $index == $this->cache_uniqid_css )
					o3_unlink( $filename );				
			}
   	}

	/**
	 * Genarate cache file in the cache folder and return the filename
	 *
	 * string JS files
	 * @return string Cache filename
	 */		
 	public function js_cache() {
 		$args = func_get_args();
		$buffer = '';
		$cache_name = '';
				
		$files = array();
		foreach ( $args as $value ) {
			if ( !is_array($value) ) {
				$file = $value;
				//generate temp name
				if ( !file_exists($file) || !is_file($file) || !is_readable($file) ) {
					trigger_error('Unable to open input file: '.$file);
				} else {				
					//store file path
					$files[] = $file;
					
					$cache_name .= filemtime($file).basename($file); 			
				}
			} else {
				foreach ( $value as $value2 ) {
					$file = $value2;
					//generate temp name
					if ( !file_exists($file) || !is_file($file) || !is_readable($file) ) {
						trigger_error('Unable to open input file: '.$file);
					} else {					
						//store file path
						$files[] = $file;
						
						$cache_name .= filemtime($file).basename($file); 	
					}
				}
			}
		}
		
		$cache_name = md5( $cache_name.( $this->minimize && $this->minimize_js ? 1 : 0 ) );
		$cache_file_name = $this->cache_uniqid_js.'-'.$cache_name.'.js';
		if ( file_exists($this->js_cache_dir.'/'.$cache_file_name) && 
			 is_readable($this->js_cache_dir.'/'.$cache_file_name) &&
			 is_file($this->js_cache_dir.'/'.$cache_file_name) ) {
			return $cache_file_name;
		} else {
			
			//read js contents
			$content = '';
			foreach ( $files as $value ) {
				$content .= file_get_contents( $value )."\n\n";
			}

			//minimize if needed
			if ( $this->minimize && $this->minimize_js ) {
				require_once('o3_js_compressor.php');
				$content = o3\module\mini\o3_js_compressor::process( $content );
			}

			//create cache file				
			file_put_contents( $this->js_cache_dir.'/'.$cache_file_name, $content );

			if ( file_exists($this->js_cache_dir.'/'.$cache_file_name) && 
				 is_readable($this->js_cache_dir.'/'.$cache_file_name) &&
				 is_file($this->js_cache_dir.'/'.$cache_file_name) )
				return $cache_file_name;

		}

		trigger_error( 'Can\'t create js cache file.' );
		return false;
		
	}
	
	/**
	 * Genarate url to cache file 
	 *
	 * string/array JS files
	 * @return string Cache filename
	 */	
	public function js_cache_url() {
		$args = func_get_args();

		//get caller script path, we need for relaive paths
		$relative_path = o3_get_caller_path();
		if ( $relative_path != '' )
			for ( $i =0; $i < count($args); $i++ ) {
				if ( is_array($args[$i])) {					
					for ( $j =0; $j < count($args[$i]); $j++ ) {
						if ( !realpath($args[$i][$j]) ) 
							$args[$i][$j] = $relative_path.'/'.$args[$i][$j];
					}
				} else {
					if ( !realpath($args[$i]) )
						$args[$i] = $relative_path.'/'.$args[$i];			
				}
			}

  		$cache_file = call_user_func_array( array( $this, 'js_cache' ), $args );
		if ( $cache_file !== false )
			return $this->js_cache_url.'/'.o3_remove_more('/',2,$cache_file);
		return false;
	}
	
	/**
	 * Genarate link tag with cache file 
	 *
	 * string JS files
	 * @return string Link tags of files
	 */		
	public function js_script() {
		$args = func_get_args();

		//get caller script path, we need for relaive paths
		$relative_path = o3_get_caller_path();
		if ( $relative_path != '' )
			for ( $i =0; $i < count($args); $i++ ) {
				if ( is_array($args[$i])) {					
					for ( $j =0; $j < count($args[$i]); $j++ ) {
						if ( !realpath($args[$i][$j]) ) 
							$args[$i][$j] = $relative_path.'/'.$args[$i][$j];
					}
				} else {
					if ( !realpath($args[$i]) )
						$args[$i] = $relative_path.'/'.$args[$i];			
				}
			}
			
		$cache_file = call_user_func_array( array( $this, 'js_cache' ), $args );
		if ( $cache_file !== false )
			return '<script src="'.$this->js_cache_url.'/'.o3_remove_more('/',2,$cache_file).'" type="text/javascript"></script>';
		return ''; 
	}
  
  	/**
	 * Function sets the parent object
	 *
	 * @param object $parent Parent object
	 *
	 * @return object
	 */	
	public function set_parent( $parent ) {
		return $this->parent = &$parent;
	}

	/******** Deprecated ********/

	/** integer CSS cache file max lifetime in seconds */
	public $lifetime_css = O3_MINI_CSS_CACHE_LIFETIME; //2 weeks
	
	/** integer JS cache file max lifetime in seconds */
	public $lifetime_js = O3_MINI_JS_CACHE_LIFETIME; //2 weeks

	/**
	 * Set js cache file max lifetime in seconds	 
	 * @param int $value (optional) Default value: 1209600, 2 weeks
	 *
	 * @return void
	 */
	public function set_js_cache_lifetime( $value = 1209600 ) {
		$this->lifetime_js = $value;
	}

	/**
	 * Set css cache file max lifetime in seconds	 
	 * @param int $value (optional) Default value: 1209600, 2 weeks
	 *
	 * @return void
	 */
	public function set_css_cache_lifetime( $value = 1209600 ) {
		$this->lifetime_css = $value;
	}
     
}

?>