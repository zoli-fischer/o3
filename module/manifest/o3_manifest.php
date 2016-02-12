<?php

/**
 * HTML5 cache manifest handle module for O3 Engine
 *
 * Main class file
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * HTML5 cache manifest handle class 
 */
class o3_manifest {
	
	/** mixed Parent object */
	public $parent = null;
	
	/** string Path to cache dir */
	public $manifest_cache_dir = O3_MANIFEST_CACHE_DIR;
	
	/** string Relative URL to cache dir */
	public $manifest_cache_url = O3_MANIFEST_CACHE_URL;
	
	/** boolean Allow manigest generation */
	public $allowed = O3_MANIFEST_ALLOWED;
	
	/** integer Cache file max lifetime in seconds */
	public $lifetime_manifest = O3_MANIFEST_CACHE_LIFETIME;
	
	/** integer Cache file uniq id */
	public $cache_uniqid_manifest = 'manifest';

	/** array Cache file data */
	public $manifest = array(
			'comment' => array(),
			'cache' => array(),
			'network' => array(),
			'fallback' => array()
		);

	/**
	* O3 mini constructor
	*
	* @return void
	*/		
	public function __construct() {
		
	}

	/**
	* Add entry to comment part of the manifest file
	* @param string $entry
	*
	* @return void
	*/	
	public function	comment( $entry ) {
		if ( trim($entry) != '' && !isset($this->manifest['cache'][$entry]) ) {
			$this->manifest['comment'][$entry] = $entry;
		}
	}

	/**
	* Add entry to cache part of the manifest file
	* @param string $entry
	*
	* @return void
	*/	
	public function	cache( $entry ) {
		if ( trim($entry) != '' && !isset($this->manifest['cache'][$entry]) ) {
			$this->manifest['cache'][$entry] = $entry;
		}
	}

	/**
	 * Allow manigest generation
	 * @param boolean $value (optional) Default value: true
	 *
	 * @return void
	 */
	public function allow( $value = true ) {
		$this->allowed = $value;
	}

	/**
	 * Manifest cache file max lifetime in seconds	 
	 * @param int $value (optional) Default value: 1209600, 2 weeks
	 *
	 * @return void
	 */
	public function set_cache_lifetime( $value = 1209600 ) {
		$this->lifetime_manifest = $value;
	}

	/*
	* Remove all manifest appcache files
	*/
	public function unlink_all_manifest() {
		global $o3;
		//get files
		$files = glob(preg_replace('/(\*|\?|\[)/', '[$1]', $this->manifest_cache_dir.'/').'*.appcache');		
		if ( count($files) > 0 )
			foreach ( $files as $filename ) {
				/*
				$basename = basename($filename);
				$_ = explode( '-', $basename );							
				if ( count($_) == 3 && $_[0] == $this->cache_uniqid_manifest ) {					
				*/
					o3_unlink($filename);
				//}
			}
	}	

	/*
	* Create manifest cache file
	*/
	public function create_manifest( $uri = null ) {
		$time = time();		
		$uri = $uri === null ? $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : $uri;

		$buffer = "CACHE MANIFEST\n";
		$buffer .= "# ".$uri."\n";
		
		//Add comments
		if ( count($this->manifest['comment']) )
			foreach ( $this->manifest['comment'] as $key => $value)
				$buffer .= '#'.$value."\n";

		//Add explicitly cached files
		if ( count($this->manifest['cache']) ) {
			
			$buffer .= "\nCACHE:\n";

			foreach ( $this->manifest['cache'] as $key => $value) {
				$buffer .= str_replace( ' ', '%20', $value )."\n";				
			}
		}

		$buffer .= "\n\nNETWORK:\n*\n\n";

		//generate cache name
		$cache_name = md5($uri.$buffer);
		$cache_file_name = $cache_name.'.appcache';		
		//$files = glob(preg_replace('/(\*|\?|\[)/', '[$1]', $this->manifest_cache_dir.'/').'*.appcache');

		//check if exits
		if ( file_exists($this->manifest_cache_dir.'/'.$cache_file_name) ) {
			$content = file_get_contents($this->manifest_cache_dir.'/'.$cache_file_name);
			if ( $content != $buffer ) 
				$cache_file_name = '';
		} else {		
			$cache_file_name = '';
		};

		/*
		$versions = array();
		if ( count($files) > 0 ) {
			foreach ( $files as $filename ) {
				$basename = basename($filename);
				$_ = explode( '-', $basename );
				if ( count($_) == 3 && $_[0] == $this->cache_uniqid_manifest && $_[2] == $cache_name.'.appcache' )
					$versions[] = array( 'version' => $_[1], 'path' => $filename );				
			}
			//found file with newest version and remove rest			
			if ( count($versions) ) {				
				//get current version
				foreach ( $versions as $key => $value )
					$max_version = max( $max_version, intval($value['version']) );
				
				//remove old versions
				foreach ( $versions as $key => $value ) {
					if ( $max_version == intval($value['version']) ) {
						//check if content changed						
						if ( md5(file_get_contents($value['path'])) != md5($buffer) ) {
							o3_unlink($value['path']);
						} else {
							$cache_file_name = basename($value['path']);
						}
					} else {
						o3_unlink($value['path']);
					}
				}
			}
		}
		*/

		//in no cache file found
		if( $cache_file_name == '' ) {			
			//create cache file
			$cache_file_name = $this->cache_uniqid_manifest.'-'.$cache_name.'.appcache';
			
			//set content
			file_put_contents( $this->manifest_cache_dir.'/'.$cache_file_name, utf8_encode($buffer) );
		}		

		//return file path
		return $cache_file_name != '' && file_exists($this->manifest_cache_dir.'/'.$cache_file_name) ? $this->manifest_cache_dir.'/'.$cache_file_name : false;
	}

	/*
	* Create and inject manifest attr in the html tag of the document
	*/
	public function html_inject( $buffer ) {

		if ( $this->allowed ) {
			
			//fix for browser with broken manifest engine
			header("Cache-Control: no-cache, must-revalidate");

			$manifest_file = $this->create_manifest();
			if ( $manifest_file !== false ) {
				$manifest_url = $this->manifest_cache_url.'/'.basename($manifest_file);
				$buffer = str_replace( '<html>', '<html manifest="'.$manifest_url.'">', $buffer);
			}
		}

		$buffer = str_replace( '#O3_MANIFEST_CACHE_COUNT#', count($this->manifest['cache']), $buffer );
		$buffer = str_replace( '#O3_MANIFEST_FALLBACK_COUNT#', count($this->manifest['fallback']), $buffer );
		$buffer = str_replace( '#O3_MANIFEST_NETWORK_COUNT#', count($this->manifest['network']), $buffer );
		$buffer = str_replace( '#O3_MANIFEST_COMMENT_COUNT#', count($this->manifest['comment']), $buffer );

		return $buffer;
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
     
}

?>