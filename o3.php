<?php

/**
 * O3 Engine
 *
 * With using O3 you can create php projects faster and better. 
 * O3 includes a set of classes and functions that are basic for every project, you don't need to waste time on collecting them.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
if ( !defined('O3') )
	/** string Protection against direct HTTP call of files. */
	define('O3','o3');

PHP_VERSION >= 5.3 or die("O3 requires PHP 5.3.0+");

/** The root of your O3 installation */
define("O3_DIR", str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))));

//load O3 global configs
require_once(O3_DIR.'/config.php');
require_once(O3_INC_DIR.'/func.php'); //load O3 functions
require_once(O3_INC_DIR.'/str.php'); //load O3 string functions
require_once(O3_INC_DIR.'/math.php'); //load O3 math functions
require_once(O3_INC_DIR.'/valid.php'); //load O3 string functions
require_once(O3_INC_DIR.'/time.php'); //load O3 timer functions
require_once(O3_INC_DIR.'/ua.php'); //load O3 functions
require_once(O3_INC_DIR.'/debug.php'); //load O3 debug functions
require_once(O3_INC_DIR.'/file.php'); //load O3 timer functions
require_once(O3_INC_DIR.'/header.php'); //load O3 header functions
require_once(O3_INC_DIR.'/log.php'); //load O3 debug functions
require_once(O3_INC_DIR.'/image.php'); //load O3 debug functions
			
/**
 * O3 Engine main class
 *
 * Builds and loads the O3 Engine
 * 
 * @package O3
 *
 * @example example.php
 * require_once('o3/o3.php');<br>
 * $o3 = new o3();
 */
class o3 {
	
	/** mixed List of loaded modules */
	private $modules = array();
	
	/**
	 * O3 constructor starts the debuger and logger 
	 *
	 * @return void
	 */	
	public function __construct() {

		//start debug
		$this->debug = new o3_debug();
		$this->debug->set_parent( $this );
		$this->debug->_('O3 Engine v'.O3_VERSION,O3_DEBUG);
		
		//start log
		$this->log = new o3_log( O3_LOG_FILE );
		$this->log->set_parent( $this );				
		$this->debug->add_shutdown_callback( array( $this->log, 'append_bottom' ) );

		//inject code in buffer
		$this->debug->add_ob_start_callback( array( $this, 'html_head_inject' ) );

		//add general o3 javascript variables
		$this->head_inline( 'var O3_ERR_GENERAL = \''.addslashes(O3_ERR_GENERAL).'\';', 'javascript' );

		//create list of html head frameworks
		//jquery
		$this->head_framework( 'jquery', array( array( O3_URL.'/resource/js/lib/jquery/jquery-latest.min.js', O3_RES_DIR.'/js/lib/jquery/jquery-latest.min.js' ) ) );			
		
		//knockout
		$this->head_framework( 'knockout', array( array( O3_URL.'/resource/js/lib/knockout/knockout-latest.js', O3_RES_DIR.'/js/lib/knockout/knockout-latest.js' ),
											      array( O3_URL.'/resource/js/lib/knockout/knockout.mapping-latest.js', O3_RES_DIR.'/js/lib/knockout/knockout.mapping-latest.js' ),
												  array( O3_URL.'/resource/js/lib/knockout/knockout-o3.js', O3_RES_DIR.'/js/lib/knockout/knockout-o3.js' ) ) );

		//sprintf
		$this->head_framework( 'sprintf', array( array( O3_URL.'/resource/js/lib/sprintf/sprintf-latest.min.js', O3_RES_DIR.'/js/lib/sprintf/sprintf-latest.min.js' ) ) );			

		//upclick
		$this->head_framework( 'o3_upclick', array( array( O3_URL.'/resource/js/o3_upclick/o3_upclick.js', O3_RES_DIR.'/js/o3_upclick/o3_upclick.js' ) ) );			

		//awesome
		$this->head_framework( 'awesome', array( array( O3_URL.'/resource/css/lib/awesome/awesome.min.css', '', 'stylesheet' ) ) );			

		//o3
		$this->head_framework( 'o3', array( array( O3_URL.'/resource/js/o3.js', O3_RES_DIR.'/js/o3.js' ),
											array( O3_URL.'/resource/css/o3.css', O3_RES_DIR.'/css/o3.css', 'stylesheet' ) ) );
		$this->head_framework( 'o3_date', array( array( O3_URL.'/resource/js/o3_date.js', O3_RES_DIR.'/js/o3_date.js' ) ) );
		$this->head_framework( 'o3_route', array( array( O3_URL.'/resource/js/o3_route.js', O3_RES_DIR.'/js/o3_route.js' ) ) );
		$this->head_framework( 'o3_native', array( array( O3_URL.'/resource/js/o3_native.js', O3_RES_DIR.'/js/o3_native.js' ) ) );
		$this->head_framework( 'o3_string', array( array( O3_URL.'/resource/js/o3_string.js', O3_RES_DIR.'/js/o3_string.js' ) ) );
		$this->head_framework( 'o3_touch', array( array( O3_URL.'/resource/js/o3_touch.js', O3_RES_DIR.'/js/o3_touch.js' ) ) );
		$this->head_framework( 'o3_valid', array( array( O3_URL.'/resource/js/o3_valid.js', O3_RES_DIR.'/js/o3_valid.js' ) ) );
		$this->head_framework( 'o3_all', array( array( 'o3,o3_date,o3_route,o3_native,o3_string,o3_touch,o3_valid' ) ) );

	    //o3_table
	    $this->head_framework( 'o3_table', array( array( 'jquery,knockout,o3' ),
	    										  array( O3_URL.'/resource/js/o3_table/o3_table.js', O3_RES_DIR.'/js/o3_table/o3_table.js' ),
											      array( O3_URL.'/resource/js/o3_table/o3_table.css', O3_RES_DIR.'/js/o3_table/o3_table.css', 'stylesheet' ) ) );

		//o3_popup
		$this->head_framework( 'o3_popup', array( array( 'jquery,o3,o3_touch' ),
												  array( O3_URL.'/resource/js/o3_popup/o3_popup.js', O3_RES_DIR.'/js/o3_popup/o3_popup.js' ),
												  array( O3_URL.'/resource/js/o3_popup/o3_popup.css', O3_RES_DIR.'/js/o3_popup/o3_popup.css', 'stylesheet' ) ) );

		//o3_popup
		$this->head_framework( 'o3_popnote', array( array( 'jquery,o3' ),
												    array( O3_URL.'/resource/js/o3_popnote/o3_popnote.js', O3_RES_DIR.'/js/o3_popnote/o3_popnote.js' ),
												    array( O3_URL.'/resource/js/o3_popnote/o3_popnote.css', O3_RES_DIR.'/js/o3_popnote/o3_popnote.css', 'stylesheet' ) ) );

		//o3_popup
		$this->head_framework( 'o3_scrolltop', array( array( 'jquery,o3' ),
												      array( O3_URL.'/resource/js/o3_scrolltop/o3_scrolltop.js', O3_RES_DIR.'/js/o3_scrolltop/o3_scrolltop.js' ),
												      array( O3_URL.'/resource/js/o3_scrolltop/o3_scrolltop.css', O3_RES_DIR.'/js/o3_scrolltop/o3_scrolltop.css', 'stylesheet' ) ) );

		//o3_popup
		$this->head_framework( 'o3_tooltip', array( array( 'jquery,o3' ),
												    array( O3_URL.'/resource/js/o3_tooltip/o3_tooltip.js', O3_RES_DIR.'/js/o3_tooltip/o3_tooltip.js' ),
												    array( O3_URL.'/resource/js/o3_tooltip/o3_tooltip.css', O3_RES_DIR.'/js/o3_tooltip/o3_tooltip.css', 'stylesheet' ) ) );



  	}
  
	/**
	 * Add module for loading
	 *
	 * @example example.php
	 * require_once('o3/o3.php'); <br>
		 * $o3 = new o3(); <br>
		 * <b>//loading the language module with parameter <br></b>
	 * $o3->module( array( 'name' => 'lang', 'data' => array( 'current' => 'en' ) ) ); <br>	 
		 * <b>//loading the email module without parameter <br></b>
	 * $o3->module( 'email' );
	 *
	 * @param array $m Accepts string as the name of the modul or an array with 2 items 'name' string 
	 * as the name of the module and 'data' as the values to pass at module load
	 *
	 * @return void
	 */
	public function module( $m ) {
		//add to modules list
		$modules = is_string($m) ? array( 'name' => $m ) : $m;
		if ( isset($modules['name']) && $modules['name'] != '' ) {
	  	$o3_module_data = isset($modules['data']) ? $modules['data'] : array(); 
			if ( @include_once(O3_MOD_DIR.'/'.$modules['name'].'/o3_'.$modules['name'].'.php') ) {
				$this->modules[] = $modules;
				return true;
			}
		}
		trigger_error('Module "'.$modules['name'].'" not found.');		
	}
 
	/**
	* Loads the added modules
	*
	* @return void
	*/
	public function load() {
		$c = count($this->modules);
		for ( $i = 0; $i < $c; $i++ ) {
			$o3_module_data = isset($this->modules[$i]['data']) ? $this->modules[$i]['data'] : array(); 
			@include_once(O3_MOD_DIR.'/'.$this->modules[$i]['name'].'/load.php');
		}
	}

	/** array List of resource to load in html head */ 
	public $head_resources = array();	

	/** array Frameworks list for head resources */ 
	public $head_frameworks = array();

	/*
	* Add resource or framework to load
	*
	* Framework - $names string one or more framework name with comma sep.
	* Resource - $url - url to the file, $path - path to the file, $type - type of resource javascript or stylesheet
	*
	* @return void
	*/
	public function head_res() {
		$args = func_get_args();
		$url = isset($args[0]) ? $args[0] : '';
		$path = isset($args[1]) ? $args[1] : '';
		$type = isset($args[2]) ? $args[2] : 'javascript';
		if ( func_num_args() == 1 ) {

			//add frameworks
			$url = explode(',', $url);
			foreach ( $url as $value ) {
				$value = trim($value);
				if ( isset($this->head_frameworks[$value]) && count($this->head_frameworks[$value]) ) {
					foreach ( $this->head_frameworks[$value] as $key => $value ) {
						if ( count($value) == 1 ) {
							//framework
							$this->head_res( $value[0] );
						} else {
							//resource
							$this->head_res( $value[0], $value[1], isset($value[2]) ? $value[2] : 'javascript' );
						}
						
					}
				}
			}
			
		} else {

			if ( $path != '' ) {
				//get caller script path, we need for relaive paths
				$relative_path = o3_get_caller_path();
				if ( $relative_path != '' && !realpath($path) )
					$path = $relative_path.'/'.$path;
				$path = realpath($path);
			}

			if ( $path == '' ) {
				if ( $url != '' )
					if ( !isset($this->head_resources[$url]) )
						$this->head_resources[$url] = array( 'url' => $url,
													   	 	 'path' => $path,
												     		 'type' => $type );
			} else {
				if ( !isset($this->head_resources[$path]) )
					$this->head_resources[$path] = array( 'url' => $url,
												   	 	  'path' => $path,
											     		  'type' => $type );
			};
		}
	}

	/*
	* Add framework to load
	* $name - $name Name of framework
	* $data - $data List of files from framework
	*
	* @return void
	*/
	public function head_framework( $name, $data ) {
		$this->head_frameworks[$name] = $data;
	}	

	/** array List of inline content to insert in html head */ 
	public $head_inlines = array(); 
	
	/*
	* Add inline to html head
	* $url - $content
	* $type - Type of resource empty string, javascript or stylesheet
	*
	* @return void
	*/
	public function head_inline( $content, $type = '' ) {
		$this->head_inlines[] = array( 'content' => $content, 
									   'type' => $type );
	}

	/*
	* Inject HTML code to head tag 
	* $buffer - Buffer string
	*
	* @return string
	*/
	public function html_head_inject( $buffer ) {		
		$js = array();
		$css = array();		
		$is_mini = isset($this->mini);
		$inline_js = '';
		$inline_css = '';
		$code_js = '';
		$code_css = '';

		//inline
		if ( count($this->head_inlines) > 0 ) {
			foreach ($this->head_inlines as $value) {
				switch ($value['type']) {
					case 'javascript':
						$inline_js .= '<script type="text/javascript">'.$value['content'].'</script>';
						break;
					case 'stylesheet':
						$inline_css .= '<style>'.$value['content'].'</style>';
						break;
				}
			}
		}

		//resources
		if ( count($this->head_resources) > 0 ) {
			foreach ($this->head_resources as $value) {
				if ( !isset($value['path']) || $value['path'] == '' ) {
					switch ($value['type']) {
						case 'javascript':
							if ( count($js) > 0 )
								$code_js .= $this->mini->js_script( $js );
							$js = array();	
							$code_js .= '<script type="text/javascript" src="'.$value['url'].'"></script>';
							break;
						case 'stylesheet':
							if ( count($css) > 0 )
								$code_css .= $this->mini->css_link( $css );
							$css = array();
							$code_css .= '<link rel="stylesheet" type="text/css" href="'.$value['url'].'" />';
							break;
					}
				} else {
					switch ($value['type']) {
						case 'javascript':
							$js[] = $is_mini ? $value['path'] : $value['url'];	
							break;
						case 'stylesheet':
							$css[] = $is_mini ? $value['path'] : $value['url'];	
							break;
					}
				}				
			}

			if ( $is_mini ) {
				if ( count($css) > 0 ) {
					$url = $this->mini->css_cache_url( $css );
					$code_css .= '<link rel="stylesheet" type="text/css" href="'.$url.'" />';
					isset($this->manifest) ? $this->manifest->cache($url) : ''; //add url to cache manifest
				}
				if ( count($js) > 0 ) {
					$url = $this->mini->js_cache_url( $js );
					$code_js .= '<script type="text/javascript" src="'.$url.'"></script>';
					isset($this->manifest) ? $this->manifest->cache($url) : ''; //add url to cache manifest
				}
			} else {
				if ( count($css) > 0 )
					foreach ($css as $value) {
						isset($this->manifest) ? $this->manifest->cache($value) : false; //add url to cache manifest
						$code_css .= '<link rel="stylesheet" type="text/css" href="'.$value.'" />';
					}
				if ( count($js) > 0 )
					foreach ($js as $value) {
						isset($this->manifest) ? $this->manifest->cache($value) : false; //add url to cache manifest
						$code_js .= '<script type="text/javascript" src="'.$value.'"></script>';
					}
			}

		}
		
		//put together in correct order
		$code = $inline_js.$code_css.$inline_css.$code_js;
		
		//insert code	
		if ( $code != '' ) {
			//split html at head tag
			$matches = preg_split('/(<head.*?>)/i', $buffer, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE); 	
			if ( count($matches) > 1 ) { 
				//assemble the html output back with the script code in it
				$buffer = $matches[0] . $matches[1] . ( !preg_match( '/(<header)/i', $matches[1]) ? $code : '' );
				for ( $i = 2; $i < count($matches); $i++ )
					$buffer .= $matches[$i];							
			}
		}
		

		return $buffer;
	}
  
	/**
	 * Destructor of O3
	 *
	 * @return void
	 */
	public function __destruct() {
		;
	}
	
}

/**
* Class for o3 ajax result
*/
class o3_ajax_result {
	
	public $success_ = true;
	public $success_msg_ = '';
	public $success_msg_default_ = '';
	public $fail = false;
	public $error_msg_ = '';
	public $error_msg_default_ = '';
	public $redirect_ = '';
	
	//custom data
	public $data_ = array();
 	
 	/*
	* Constructor
	*/
	public function __construct() {
		global $o3;
		$this->success_ = false;		
	}

	/*
	* Check field is posted 
	*/
	public function is( $field ) {
		return isset($_POST[$field]);
	}

	/*
	* Check value of field posted 
	*/
	public function value( $field ) {
		return $this->is( $field) ? $_POST[$field] : null;
	}

	/*
	* Get array with all the poster fields
	*/
	public function all() {
		return $_POST;
	}

	/*
	* Set success
	* @return self
	*/
	public function success( $success_msg = '' ) {
		$this->success_ = true;
		$this->fail = false;
		if ( $success_msg !== null ) {
			$this->success_msg_ = $success_msg;
		} else {
			$this->success_msg_ = $this->success_msg_default_;
		}
		$this->error_msg_ = '';
	}	
			
	/*
	* Set error
	* @return self
	*/
	public function error( $error_msg = null ) {
		$this->success_ = false;
		$this->success_msg_ = '';
		$this->fail = false;
		if ( $error_msg !== null ) {
			$this->error_msg_ = $error_msg;
		} else {
			$this->error_msg_ = $this->error_msg_default_;
		}
	}

	/*
	* Set fail
	* @return self
	*/
	public function fail( $error_msg = null ) {
		$this->success_ = false;
		$this->success_msg_ = '';
		$this->fail = true;
		if ( $error_msg !== null ) {
			$this->error_msg_ = $error_msg;
		} else {
			$this->error_msg_ = $this->error_msg_default_;
		}
	}

	/*
	* Set or get return data
	*
	* @param $data_name Name of the data to get or set
	* @param $data_value (optional) The value to set to the data
	*
	* @return mixed If 2nd parameter omited the value of attribute else the whole data list
	*/
	public function data() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			$this->data_[$args[0]] = $args[1];			
		} else if ( func_num_args() > 0 ) {
			return $this->data_[$args[0]];
		}
		return $this->data_;
	}

	/*
	* Set redirect
	* @return self
	*/
	public function redirect( $redirect = '' ) {
		$this->redirect_ = $redirect;
	}
	
	/*
	* Print out data as json
	* @return self
	*/
	public function flush() {
		//on error send page not found
		if( $this->fail ) 
			o3_header_code(404);
		$result = array(
			'success' => $this->success_,
			'success_msg' => $this->success_msg_,
			'error_msg' => $this->error_msg_,
			'redirect' => $this->redirect_,
			'data' => $this->data_
		);
		die(json_encode($result,JSON_HEX_QUOT | JSON_HEX_TAG));
	}

}

?>