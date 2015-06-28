<?php 

/**
 * Base class for template view's controller
 *
 * @category module
 * @package o3_template
 */
class o3_template_view_controller {

	/** string Path to the folder with views */
	public $view_dir = O3_TEMPLATE_VIEW_DIR;

	/** string Path to the folder with controllers for views */
	public $view_controller_dir = O3_TEMPLATE_VIEW_CONTROLLER_DIR;

	/** array List of views to create content */
	public $views = array();

	/** mixed View filepath */
	private $view_file = ''; 

	/** Content of the view */
	public $content = null;

	/** Title of the view */
	public $title = null;

	/**
	 * Constructor of view
	 * @param string $view_file File path to view
	 */
	public function __construct() {}

	/**
	 * Get view content
	 * @return string View content
	 */
	public function get_content() { return $this->content; }

	/**
	 * Set view content
	 * @string $content View content to set
	 * @return void
	 */
	public function set_content( $content ) { $this->content = $content; }

	/**
	 * Get view title
	 * @return string View title
	 */
	public function get_title() { return $this->title; }

	/**
	 * Set view title
	 * @string $title View title to set
	 * @return void
	 */
	public function set_title( $title ) { $this->title = $title; }

	/**
	 * Set view file
	 * @param string $view_file File path to view
	 */
	public function set_view_file( $view_file = '' ) { 
		$relative_path = o3_get_caller_path();
		if ( $relative_path != '' && !realpath($view_file) )
			$view_file = $relative_path.'/'.$view_file;
		$this->view_file = $view_file;
	}

	/**
	* Use instead of construct. Parameters passed to this function
	* @return void
	*/
	public function init_() {
		if ( method_exists( $this, 'init' ) )
			call_user_func_array( array( $this, 'init' ), func_get_args() );
	}

	/**
	* Do things before load
	* @return void
	*/
	public function before_load_() {
		if ( method_exists( $this, 'before_load' ) )
			$this->before_load();
	}

	/**
	* Do things after load
	* @return void
	*/
	public function after_load_() {
		if ( method_exists( $this, 'after_load' ) )
			$this->after_load();
	}

	/**
	* Load a view
	*
	* @return boolean
	*/	
	public function load() {	
		$view = basename($this->view_file,".php");

		//create globals
		$object_vars = get_object_vars($this);
		foreach ( $object_vars as $key => $value )
  			eval('$'.$key.' = $this->'.$key.';');

  		ob_start();
		if ( is_file($this->view_file) && is_readable($this->view_file) && include($this->view_file) ) {
			if ( isset($this->root) )
				$this->root->debug->_( 'View "'. o3_html($view).'" included.' );			
		} else {
			trigger_error( 'View error! Unable to load "'. o3_html($view).'".', E_USER_WARNING );
			return false;
		}
		$content = ob_get_contents();
		ob_end_clean();
		
		//set content if view content not empty
		if ( $content != '' )
			$this->set_content( $content );

		return true;
		
	}

	/**
	* Load and show view
	*
	* @param string $view Name of the view
	* @param mixed. List of parameters pass to view constructor
	* @return object
	*/	
  	public function view( $viewname ) {
		//load view
		$view = call_user_func_array( array( $this, 'load_view' ), func_get_args() );
		if ( $view !== false ) {
			$view->before_load_();
			$view->load();
			$view->after_load_();
			echo $view->get_content();
		}
  	}

	/**
	* Add view
	*
	* @param string $view Name of the view
	* @param mixed. List of parameters pass to view constructor
	* @return object
	*/	
  	public function load_view( $viewname ) {
  		$name = basename($viewname);
  		$args = func_get_args();

		//remove the first argument that is the view
		array_shift($args);

		//full path to view
		$view_file = $this->view_dir.'/'.$viewname.'.php';
		$is_view_file = ( is_file($view_file) && is_readable($view_file) );

		//full path to controller
		$controller_file = $this->view_controller_dir.'/'.$viewname.'.php';
		$is_controller_file = ( is_file($controller_file) && is_readable($controller_file) );

		if ( $is_view_file || $is_controller_file ) {

			//Create controller
			$controller = null;
			if ( $is_controller_file ) {
				if ( !include_once($controller_file) ) {
					trigger_error( 'View controller error! Unable to load "'. o3_html($controller_file).'".', E_USER_WARNING );
				} else {
					$controller = new $name();
					if ( isset($this->root) )
						$this->root->debug->_( 'View controller "'. o3_html($controller_file).'" loaded.' );
				}
			}

			//if no controler create a default one
			if ( $controller == null )
				$controller = new o3_template_view_controller();					

			//set view file, parent & initialize the controller
			$controller->set_view_file( $is_view_file ? $view_file : '' );
			$controller->set_parent( $this );			
			$controller->set_root( $this->root );
			call_user_func_array( array( $controller, 'init_' ), $args );

			//add to views list
			$this->views[] = &$controller;			

			if ( isset($this->root) )
				$this->root->debug->_( 'View "'. o3_html($view_file).'" loaded.' );
			return $controller;

		}
  	}

	/** mixed Parent object */
	public $parent = null;

	/**
	* Function sets the parent object
	*
	* @param object $parent Parent object	
	* @return object
	*/	
	public function set_parent( $parent ) {
		return $this->parent = &$parent;
	}

	/** mixed Root object */
	public $root = null;

	/**
	* Function sets the root object
	*
	* @param object $parent Root object	
	* @return object
	*/	
	public function set_root( $root ) {		
		return $this->root = &$root;
	}

}

?>