<?php

/**
 * Content language module for O3 Engine
 *
 * Handle interface/content language translations in PHP and Javascript.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Class for handling interface/content language translations
 * 
 * The class loading languages from json files. Each language can have one or more collections. 
 * Each collection has its own json file. The general collection is loaded with the language file all other collections need to be loaded separately. 
 * The language and collection name must contain only alpha numeric characters. The language file name contains only the language name, like [a-z0-9+].json . 
 * The collection file name contains the name of the language and the name of the collection separetad with a <b>-</b>, like [a-z0-9+]-[a-z0-9+].json . <br><br>
 * The class handles different langauge's plural rules. <br>
 * List of plural rules: https://developer.mozilla.org/en/docs/Localization_and_Plurals
 * <br>
 * <b>Example:</b><br>
 * Crating a new language Polish for the system:<br>
 * <b>1.</b> Create the file pl.json in the language folder.<br>
 * <b>2.</b> Add indexes to the language file. Set the content of the file to <i> {"login":{"0":"login","plural":0},"logout":{"0":"wyloguj","plural":0}} </i><br>
 * <b>3.</b> Add system values like o3_lang_display_name. Set the content of the file to <i> {"o3_lang_display_name":{"0":"Dutch","plural":0},"login":{"0":"login","plural":0},"logout":{"0":"wyloguj","plural":0}} </i><br>
 * <b>4.</b> Add plural rule #9. This rule has 3 forms, each index with plural version should have 3 indexes to cover all the forms. Set the content of the file to <i> {"o3_lang_display_name":{"0":"Dutch","plural":0},"o3_lang_plural_rule_id":{"0":"9","plural":0},"file":{"0":"plik","1":"pliki","2":"pliko'w","plural":1},"login":{"0":"login","plural":0},"logout":{"0":"wyloguj","plural":0}} </i><br><br>
 *
 */
class o3_lang {
	
	/** mixed Parent object */
	public $parent = null;
	
	/** array List of loaded language indexes */
	public $list = array();
	
	/** string Selected language */
	public $current = '';
	
	/** array Loaded collections */
	public $collections = array();
	
	/** string Path to the folder with language json-s */
	public $lang_dir = O3_LANG_DIR; 
	
	/** string URL to langauage javascript file*/
	public $lang_js_url = O3_LANG_JS_URL;
	
	/**  
	* array List of plural rules <br> https://developer.mozilla.org/en/docs/Localization_and_Plurals
	*/
	public $plural_rules = array();
	
	/**
	 * Constructor of language
	 * @return void
	 */
	public function __construct() {
  	/** Default plural rules */
  	$this->plural_rules[0] = array( 'form' => 1, 'rule' => '0' ); //Asian (Chinese, Japanese, Korean, Vietnamese), Persian, Turkic/Altaic (Turkish), Thai, Lao
		$this->plural_rules[1] = array( 'form' => 2, 'rule' => 'n == 1 ? 0 : 1'  ); //Germanic (Danish, Dutch, English, Faroese, Frisian, German, Norwegian, Swedish), Finno-Ugric (Estonian, Finnish, Hungarian), Language isolate (Basque), Latin/Greek (Greek), Semitic (Hebrew), Romanic (Italian, Portuguese, Spanish, Catalan)
		$this->plural_rules[2] = array( 'form' => 2, 'rule' => 'n == 0 || n == 1 ? 0 : 1'  ); //Romanic (French, Brazilian Portuguese)
		$this->plural_rules[3] = array( 'form' => 3, 'rule' => 'n == 0 ? 0 : ( n % 10 == 1 && n != 11 ? 1 : 2 ) ' ); //Baltic (Latvian)
		$this->plural_rules[9] = array( 'form' => 3, 'rule' => 'n == 1 ? 0 : ( n % 10 >= 2 && n % 10 <= 4 && ( n > 14 || n < 10 ) ? 1 : 2 ) ' ); //Slavic (Polish)		
  }
  
 	/**
	 * Set the path to the folder with language json-s
	 * @param string $path
	 * @return void
	 */
  function set_dir( $path ) {
  	$this->lang_dir = $path;
  }
  
  /**
	 * Set the URL to langauage javascript file
	 * @param string $url
	 * @return void
	 */
  function set_js_url( $url ) {
  	$this->lang_js_url = $url;
  }
  
  /**
	 * Set current language and load if it's not loaded. 
	 * 
	 * If parameter omitted the current language is returned
	 *
	 * @param string $language (optional) Language to set as selected
	 * 
	 * @return void
	 */
	function current() {
		$args = func_get_args();
		if ( count($args) > 0 ) {
			$language = $args[0];
			$this->current = $language;
	  	$this->load( '' );
	  }
	  return $this->current;
  }  
  
  //load collection
  //$collections - use '*' for all collection, '' for general collection 
  //$languages - languages name, comma separeted
  //TODO 
  
  /**
	 * Load collection or language
	 * 
	 * @todo Add support for *, security for path generate
	 *
	 * @param string $collections (optional) Collection name Default value: ''
	 * @param string $languages (optional) Language name Default value: ''
	 *
	 * @return void
	 */
  function load( $collections = '', $languages = '' ) {
  	$languages = $languages == '' ? array( $this->current ) : explode(',',$languages);
  	$cs = explode(',',$collections);
  	if ( count( $languages ) > 0 ) { 
	  	foreach ( $languages as $key => $value ) {
				if ( $value != '' ) {
					//check general
	  			if ( !isset($this->list[$value]) ) {
		  			//load general file
	 					$file = $this->lang_dir.'/'.$value.'.json';	 					
	 					if ( file_exists($file) ) {
	 						$content = json_decode(file_get_contents($file),true);			
	 						if ( is_array($content) ) {
	 							$this->list[$value][$value] = $content;
	 						} else {
	 							trigger_error( 'Language file '.o3_html($value).'is not valid!' );
	 						} 		
	 					} else {
	 						trigger_error( 'Language file '.o3_html($value).' not found!' );
	 					}
	  			}
	  			//check collections
	  			if ( count( $cs ) > 0 ) {
	  				foreach ( $cs as $key_cs => $value_cs ) {
	  					if ( $value_cs != '' ) {
		  					if ( isset($this->list[$value]) && !isset($this->list[$value][$value_cs]) ) {
		  						$file = $this->lang_dir.'/'.$value.'-'.$value_cs.'.json';
		  						if ( file_exists($file) ) {
				 						if ( !in_array($value_cs,$this->collections) ) 
				 							$this->collections[] = $value_cs;
				 						$content = json_decode(file_get_contents($file),true);
				 						if ( is_array($content) ) {
				 							$this->list[$value][$value_cs] = $content;
				 						} else {
					 						trigger_error( 'Language collection file '.o3_html($value).'-'.o3_html($value_cs).' is not valid!' );	 							
				 						} 		
				 					} else {
				 						trigger_error( 'Language collection file '.o3_html($value).'-'.o3_html($value_cs).' not found! ');
				 					}
		  					}
		  				}
	  				}
	  			}
	  		}
	  	}
  	}
  }
  
  
  /**
	 * Get a text from a language
	 *
	 * Same as o3_lang::get() 
	 * @see o3_lang::get() o3_lang::get() 
	 * @param string $index
	 * @param string $collection (optional)
	 * @param string $language (optional)
	 *
	 * @return void
	 */
  function _( $index, $collection = '*', $language = '' ) {
  	return $this->get( $index, $collection, $language );
  }
  
  /**
	 * Get a text from a language
	 * 
	 * @param string $index Text index
	 * @param string $collection (optional) Collection name where to search for the index. If is omitted index is searched in all collections. Default value: '*'
	 * @param string $language (optional) Language from where to get the text. If is omitted the current language is used. Default value: ''
	 *
	 * @return void
	 */
  function get( $index, $collection = '*', $language = '' ) {
  	$language = $language == '' ? $this->current : $language; //use current language 
  		
  	//load language if not loaded
  	if ( !isset($this->list[$language]) || 
					( $collection != '' && !isset($this->list[$language][$collection == '*' ? $language : $collection]) ) ) {  			
				$this->load( $language, $collection );
		}
		
		//check for index
		if ( $collection == '*' ) {		
			if ( isset($this->list[$language]) && count($this->list[$language]) > 0 ) {
				//check index in all collections
				$array = array_reverse($this->list[$language]);
				foreach ( $array as $key => $value ) {
				 	if ( isset($value[$index]) ) {
						return $value[$index][0];	
		  		}
				}
 			}
		} else { 
			$collection = $collection == '' ? $language : $collection;		
  		//check index in selected collection
			if ( isset($this->list[$language][$collection]) &&
  				 isset($this->list[$language][$collection][$index]) ) {  				 	
				return $this->list[$language][$collection][$index][0];	
  		}
  	}
  	
  	return $index;
  }
  
  /**
	 * Get plural form of a text from a language
	 *
	 * Same as o3_lang::nget() 
	 * @see o3_lang::nget() o3_lang::nget() 
	 *
	 * @param string $index
	 * @param double $n
	 * @param string $collection (optional)
	 * @param string $language (optional)
	 *
	 * @return void
	 */
  function n_( $index, $n, $collection = '*', $language = '' ) {
  	return $this->nget( $index, $n, $collection, $language );
  }
  
  /**
	 * Get plural form of a text from a language
	 * 
	 * @param string $index Text index
	 * @param double $n Number to check which form is used in the added language
	 * @param string $collection (optional) Collection name where to search for the index. If is omitted index is searched in all collections. Default value: '*'
	 * @param string $language (optional) Language from where to get the text. If is omitted the current language is used. Default value: ''
	 *
	 * @return void
	 */
  function nget( $index, $n, $collection = '*', $language = '' ) {
  	$language = $language == '' ? $this->current : $language; //use current language
  	$nindex = $this->get_nindex($n,$language); //index of plural for the n
  		
  	//load language if not loaded
  	if ( !isset($this->list[$language]) || 
					( $collection != '' && !isset($this->list[$language][$collection == '*' ? $language : $collection]) ) ) {  			
				$this->load( $language, $collection );
		}
		
		//check for index
		if ( $collection == '*' ) {		
			if ( isset($this->list[$language]) && count($this->list[$language]) > 0 ) {
				//check index in all collections
				$array = array_reverse($this->list[$language]);
				foreach ( $array as $key => $value ) {
				 	if ( isset($value[$index]) ) {
						return $value[$index][ isset($value[$index]['plural']) && $value[$index]['plural'] == 1 ? $nindex : 0 ];	
		  			}
				}
 			}
		} else { 
			$collection = $collection == '' ? $language : $collection;		
  			//check index in selected collection
			if ( isset($this->list[$language][$collection]) &&
  				 isset($this->list[$language][$collection][$index]) ) {  
				return $this->list[$language][$collection][$index][ isset($this->list[$language][$collection][$index]['plural']) && $this->list[$language][$collection][$index]['plural'] == 1 ? $nindex : 0 ];	
  		}
  	}
  	
  	return $index;
  }

	/**
	 * Functio returns 1st parameter if the number in given language is singular else 2nd parameter
	 * 
	 * @param string $index1 Singular index
	 * @param string $index2 Plural index
	 * @param double $n Number to check which form is used in the added language
	 * @param string $language (optional) Language from which to check the plural rule. If is omitted the current language is used. Default value: ''
	 *
	 * @return void
	 */
	function nget_text( $index1, $index2, $n, $language = '' ) {
		//if index 0 return singular else correct plural
		return $this->get_nindex( $n, $language ) == 0 ? $this->_( $index1 ) : $this->_( $index2, $n );
	}
  
	/**
	* Get plural form index for the number for the current language
	* 
	* @param double $n Number to check which form is used in the added language
	* @param string $language (optional) Language from which to check the plural rule. If is omitted the current language is used. Default value: ''
	*
	* @return void
	*/
	function get_nindex( $n, $language = '' ) {  	
		$language = $language == '' ? $this->current : $language; //use current language
		$n = floatval($n);  	
		$p_rule_data = $this->get_plural_rule( $language ); //get plural rule data
		try {
			eval('$n = '.str_replace( 'n', '$n', $p_rule_data['rule'] ).';'); //TODO: security
		} catch (Exception $e) {
			$n = 0;
			throw new Exception( 'Plural rule can not be parsed for rule ID: '.$p_rule_id.'.' );
		}
		return $n;
	} 
  
	/**
	 * Get plural rule of the current language
	 * 
	 * @param string $language (optional) Language from which to check the plural rule. If is omitted the current language is used. Default value: ''
	 *
	 * @return void
	 */
	function get_plural_rule( $language = '' ) {
		$language = $language == '' ? $this->current : $language; //use current language
		$p_rule_id = intval($this->get(O3_LANG_PLURAL_RULE_ID_INDEX,'',$language));  	
		$p_rule_id = isset($this->plural_rules[$p_rule_id]) ? $p_rule_id : 0;
		$p_rule_data = $this->plural_rules[$p_rule_id];
		return array_merge( array( 'form' => 1, 'rule' => '0' ), $p_rule_data );  	
	}

  	/**
	* Generate the url for language javascript
	*
	* @return string
	*/
	public function gen_html_script_url() {
		$buffer = '';
		if ( count($this->list) > 0 ) {
			$keys = array();
			foreach ( $this->list as $key => $value ) {	
				$keys[] = $key;
			}   
			$buffer = $this->lang_js_url.'?ls='.implode(',',$keys).'&amp;l='.$this->current.'&amp;c='.implode(',',$this->collections);
		}
		return $buffer;	
	}
  	
  	/** boolean Injected external script or inline script. Default value: true */
  	public $html_script_external = true;

  	/**
	* Enable or disable external javascript injection in head tag
	* @param boolean $value Default: true
	* @return void
	*/
	function set_html_script_external( $value = true ) {
		$this->html_script_external = $value;
	}

	/**
	* Generate the script tag for language javascript load
	*
	* @return string
	*/
	public function gen_html_script() {
		$buffer = '';
		$url = $this->gen_html_script_url();
		if ( $url != '' ) {
			$buffer = $this->html_script_external ? '<script src="'.$url.'" type="text/javascript"></script>' : '<script>'.o3_get_url(str_replace('&amp;','&',$url)).'</script>';
		}
		return $buffer;
	}
  
	
	/** boolean If true the javascript loader not injected in the head tag. Default value: false
	Deprecated - check load.php */
  	public $no_inject_html_script = false;
  
	/**
	* Allow or disallow the javascript injection in head tag
	* @see o3_lang::inject_html_script() o3_lang::inject_html_script()
	* @param boolean $value
	* @return void
	*/
	function allow_inject_html_script( $value ) {
		$this->no_inject_html_script = !$value;
	}
	
	/**	 
	 * Inject language javascript tag to head tag
	 * @see o3_lang::allow_inject_html_script() o3_lang::allow_inject_html_script()
	 * @param string $buffer Text in which to inject the code
	 * @return string
	 */
	function inject_html_script( $buffer ) {
		if ( count($this->list) > 0 && !$this->no_inject_html_script ) { //do nothing if list is empty
			//split html at head tag
			$matches = preg_split('/(<head.*?>)/i', $buffer, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE); 
			if ( count($matches) > 1 ) {
				//assemble the html output back with the script code in it
				$buffer = $matches[0] . $matches[1] . ( !preg_match( '/(<header)/i', $matches[1]) ? $this->gen_html_script() : '' );
				for ( $i = 2; $i < count($matches); $i++ )
					$buffer .= $matches[$i];
				//$buffer = $matches[0] . $matches[1] . $this->gen_html_script() . $matches[2];
				$bodys = preg_split('#(</body.*?>)#i', $buffer, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE); 
				if ( count($bodys) > 1 )
					$buffer = $bodys[0] . '<script type="text/javascript">o3_lang.now();</script>' . $bodys[1] . $bodys[2];			
			}
			
		}
		return $buffer;	
	}
	
	/** boolean If true the template tags, like {o3_lang:index}, are not replaced. Default value: false */
  	public $no_relpace_template_tags = false;
  
	/**
	 * Allow or disallow the replacement of template tags
	 * @see o3_lang::relpace_template_tags() o3_lang::relpace_template_tags()
	 * @param boolean $value
	 * @return void
	 */
	function allow_relpace_template_tags( $value ) {
		$this->no_relpace_template_tags = !$value;
	}
  
	/**
	 * Replace template tags, like {o3_lang:index}, with text from current language
	 * @see o3_lang::allow_relpace_template_tags() o3_lang::allow_relpace_template_tags()
	 * @param string $buffer Text in which to repalce the template tags
	 * @return string
	 */
	function relpace_template_tags( $buffer ) {
		if ( count($this->list) > 0 && $this->current != '' && !$this->no_relpace_template_tags ) { //do nothing if list is empty
			$from = array();
			$to = array();
			$array = array_reverse($this->list[$this->current]);
			foreach ( $array as $key => $value ) {				
				foreach ( $value as $key_ => $value_ ) {
					$from[] = '{o3_lang:'.$key_.'}';
					$to[] = o3_html($value_[0]); //make html safe 					
				}				
			}
			$buffer = str_ireplace( $from, $to, $buffer );
		}
		return $buffer;
	}

	/**
	 * Same as o3_lang::relpace_template_tags without replacing with html safe
	 * @param string $buffer Text in which to repalce the template tags
	 * @return string
	 */
	function relpace_template_tags_no_html_safe( $buffer ) {
		if ( count($this->list) > 0 && $this->current != '' && !$this->no_relpace_template_tags ) { //do nothing if list is empty
			$from = array();
			$to = array();
			$array = array_reverse($this->list[$this->current]);
			foreach ( $array as $key => $value ) {				
				foreach ( $value as $key_ => $value_ ) {
					$from[] = '{o3_lang:'.$key_.'}';
					$to[] = $value_[0]; //make html safe 					
				}				
			}
			$buffer = str_ireplace( $from, $to, $buffer );
		}
		return $buffer;	
	}
  
  /**
	 * Function sets the parent object
	 *
	 * @param object $parent Parent object
	 *
	 * @return object
	 */	
  function set_parent( $parent ) {
  	return $this->parent = &$parent;
  }
  
  /**
	 * Destructor of language
	 *
	 * @return void
	 */
  public function __destruct() {}
     
}

?>