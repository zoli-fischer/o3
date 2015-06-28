<?php

/**
 * PHP/HTML Templating module for O3 Engine
 *
 * Load and show templates
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once('o3_template_controller.php');
require_once('o3_template_view_controller.php');

/**
 * Class for PHP/HTML templating
 *
 * @category module
 * @package o3_template
 */
class o3_template {

	/** mixed Parent object */
	public $parent = null;
	
	/** mixed Content */
	public $content = null;

	/** mixed Title */
	public $title = null;

	/** string Path to the folder with tempaltes */
	public $template_dir = O3_TEMPLATE_DIR;

	/** string Path to the folder with controllers for templates */
	public $template_controller_dir = O3_TEMPLATE_CONTROLLER_DIR;
	
	/** string Path to the folder with views */
	public $view_dir = O3_TEMPLATE_VIEW_DIR;

	/** string Path to the folder with controllers for views */
	public $view_controller_dir = O3_TEMPLATE_VIEW_CONTROLLER_DIR;
	 	
	/** array List of views to create content */
	public $views = array();

	/** array List of loaded templates */
	public $templates = array();

	/**
	 * Constructor of template
	 * @return void
	 */
	public function __construct() {}

  	/**
	* Flush template content
	*
	* @param string $template Name of the template to load and flush content
	* @param mixed. List of parameters pass to template constructor
	* @return object
	*/
  	public function flush() {
  		$args = func_get_args();

  		//if argument passed, load template
  		if ( func_num_args() > 0 ) {
  			$templatename = $args[0];

	  		//remove the first argument that is the template
			array_shift($args);

			//load template
			call_user_func_array( array( $this, 'template' ) , $args );
		}		

		//get template contents
		//if ( count($this->templates) ) {
			
			//trigger before load event
			if ( count($this->views) )
				foreach ( $this->views as $key => $value )
					$value->before_load_();

			//trigger before load event
			if ( count($this->templates) )
				foreach ( $this->templates as $key => $value )
					$value->before_load_();

			//load views
			$content = '';
			$title = null;
			if ( count($this->views) )
				foreach ( $this->views as $key => $value ) {
					$value->load();
					$content .= $value->get_content();	
					if ( $value->get_title() != null ) 
						$title = $value->get_title();			
				}

			//load templates
			if ( count($this->templates) ) {
				foreach ( $this->templates as $key => $value ) {
					//set view content
					$value->set_content( $content );

					//if template title null set the view title
					if ( $value->get_title() == null ) 
						$value->set_title( $title );

					$value->load();
					$this->content .= $value->get_template_content();					
				}
			} else {
				$this->content = $content;	
			}

			//trigger after load event	
			if ( count($this->views) )		
				foreach ( $this->views as $key => $value )
					$value->after_load_();

			//trigger after load event
			if ( count($this->templates) )	
				foreach ( $this->templates as $key => $value )
					$value->after_load_();

		//}

		echo $this->content;
  	}

  	/**
	* Add template
	*
	* @param string $template Name of the template
	* @param mixed. List of parameters pass to template constructor
	* @return object
	*/
  	public function template( $templatename ) {
  		$args = func_get_args();

		//remove the first argument that is the template
		array_shift($args);

		//full path to template
		$template_file = $this->template_dir.'/'.$templatename.'.php';
		$is_template_file = ( is_file($template_file) && is_readable($template_file) );

		//full path to controller
		$controller_file = $this->template_controller_dir.'/'.$templatename.'.php';
		$is_controller_file = ( is_file($controller_file) && is_readable($controller_file) );

		if ( $is_template_file || $is_controller_file ) {

			//Create controller
			$controller = null;
			if ( $is_controller_file ) {
				if ( !include_once($controller_file) ) {													
					trigger_error( 'Template controller error! Unable to load "'. o3_html($controller_file).'".', E_USER_WARNING );
				} else {			
					$controller = new $templatename();
					$this->parent->debug->_( 'Template controller "'. o3_html($controller_file).'" loaded.' );
				}
			}

			//if no controler create a default one
			if ( $controller == null )
				$controller = new o3_template_controller();					

			//set template file, parent & initialize the controller
			$controller->set_template_file( $is_template_file ? $template_file : '' );
			$controller->set_parent( $this->parent );
			call_user_func_array( array( $controller, 'init_' ), $args );

			//add to templates list
			$this->templates[] = &$controller;

			$this->parent->debug->_( 'Template "'. o3_html($template_file).'" loaded.' );
			return $controller;

		}
  	}

  	/**
	* Add view to the content
	*
	* @param string $view Name of the view
	* @param mixed. List of parameters pass to view constructor
	* @return object
	*/	
  	public function view( $viewname ) {
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
					$this->parent->debug->_( 'View controller "'. o3_html($controller_file).'" loaded.' );
				}
			}

			//if no controler create a default one
			if ( $controller == null )
				$controller = new o3_template_view_controller();

			//set view file, parent & initialize the controller
			$controller->set_view_file( $is_view_file ? $view_file : '' );
			$controller->set_parent( $this->parent );
			call_user_func_array( array( $controller, 'init_' ), $args );

			//add to views list
			$this->views[] = &$controller;			

			$this->parent->debug->_( 'View "'. o3_html($view_file).'" loaded.' );
			return $controller;

		}
  	}

	/**
	* Function sets the parent object
	*
	* @param object $parent Parent object	
	* @return object
	*/	
	public function set_parent( $parent ) {
		return $this->parent = &$parent;
	}	

}

?>