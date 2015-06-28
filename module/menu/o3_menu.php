<?php

/**
 * Menu handler module for O3 Engine
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */


/**
 * Class for handling menus 
 * 
 * Use for handling/generating menu/breadcrumbs.
 */
class o3_menu {
	
	/** mixed Parent object */
	public $parent = null;
	
	/** array List of menu items */
	public $items = array();

	/** Flag for uri compare algorithm */
	public $check_uri_flag = 0;	
	
	/**
	 * O3 menu constructor
	 *
	 * @return void
	 */		
	public function __construct() {
		$this->check_uri_flag = ( O3_COMPARE_URI_HOST | O3_COMPARE_URI_PATH | O3_COMPARE_URI_QUERY );
	}
  
	/**
	 * Generate menu to print out as UL/LI tag tree
	 *
	 * @param string $classname (optional) Default value: menu
	 *
	 * @return string
	 */		
	public function get_ul( $classname = 'menu' ) {
		$buffer = '';
		if ( count($this->items) > 0 ) {
			$buffer .= '<ul class="'.$classname.'"><li class="'.$classname.'_spacer '.$classname.'_spacer_first"></li>';
			
			$items = array();
			foreach ( $this->items as $value ) {	  		
				if ( $value->visible ) {
					//show menu item
					$items[] = '<li class="'.$classname.'_item '.( $value->selected ? $classname.'_selected' : '' ).' '.$value->classname.'"><a href="'.( $value->href == '' ? 'javascript:{}' : $value->href ).'" '.( $value->target != '' ? 'target="'.$value->target.'"' : '' ).'>'.$value->text.'</a>'.$this->get_ul_level( $value->items, $classname ).'</li>';			
				}
			}
			$buffer .= implode( '<li class="'.$classname.'_spacer"></li>', $items ).'<li class="'.$classname.'_spacer '.$classname.'_spacer_last"></li></ul>';
		}
		return $buffer;
	}
  
	/**
	* Generate submenu recursively for get_ul()
	*
	* @param array $items
	* @param string $classname (optional) Default value: menu
	*
	* @return string
	*/		
	public function get_ul_level( $items, $classname = 'menu', $level = 1 ) {
		$buffer_aux = '';
		//show submenu
		if ( count($items) > 0 ) {					
			$buffer_aux .= '<ul class="'.$classname.'_level'.$level.'"><li class="'.$classname.'_level'.$level.'_spacer '.$classname.'_level'.$level.'_spacer_first"></li>';
			$level_items = array();
			foreach ( $items as $level_value ) {						
				if ( $level_value->visible ) {
					$level_items[] = '<li class="'.$classname.'_level'.$level.'_item '.( $level_value->selected ? $classname.'_level'.$level.'_selected' : '' ).' '.$level_value->classname.'"><a href="'.( $level_value->href == '' ? 'javascript:{}' : $level_value->href ).'" '.( $level_value->target != '' ? 'target="'.$level_value->target.'"' : '' ).'>'.$level_value->text.'</a>'.$this->get_ul_level( $level_value->items, $classname, $level+1 ).'</li>';
				}
			}
			$buffer_aux .= implode( '<li class="'.$classname.'_level'.$level.'_spacer"></li>', $level_items ).'<li class="'.$classname.'_level'.$level.'_spacer '.$classname.'_level'.$level.'_spacer_last"></li></ul>';
		}
		return $buffer_aux;
	}
  	

  	/**
	 * Generate menu to print out as SELECT/OPTION tag tree
	 *
	 * @param string $classname (optional) Default value: menu
	 *
	 * @return string
	 */		
	public function get_dropdown( $classname = 'menu' ) {
		$buffer = '';
		if ( count($this->items) > 0 ) {
			$buffer .= '<select class="'.$classname.'" onchange="this.value != \'\' ? window.location = this.value : \'\'">';
			
			$items = array();
			foreach ( $this->items as $value ) {	  		
				if ( $value->visible ) {
					//show menu item
					$items[] = '<option '.( $value->selected ? 'selected' : '' ).' class="'.$classname.'_item '.( $value->selected ? $classname.'_selected' : '' ).' '.$value->classname.'" value="'.( $value->href == '' ? '' : $value->href ).'">'.$value->text.'</option>'.$this->get_dropdown_level( $value->items, $classname );			
				}
			}
			$buffer .= implode( "", $items ).'</select>';
		}
		return $buffer;
	}
  
	/**
	* Generate submenu recursively for get_ul()
	*
	* @param array $items
	* @param string $classname (optional) Default value: menu
	*
	* @return string
	*/		
	public function get_dropdown_level( $items, $classname = 'menu', $level = 1 ) {
		$buffer_aux = '';
		//show submenu
		if ( count($items) > 0 ) {					
			$level_items = array();
			foreach ( $items as $level_value ) {						
				if ( $level_value->visible ) {
					$level_items[] = '<option '.( $level_value->selected ? 'selected' : '' ).' class="'.$classname.'_level'.$level.'_item '.( $level_value->selected ? $classname.'_level'.$level.'_selected' : '' ).' '.$level_value->classname.'" value="'.( $level_value->href == '' ? '' : $level_value->href ).'">'.$level_value->text.'</option>'.$this->get_ul_level( $level_value->items, $classname, $level+1 );
				}
			}
			$buffer_aux .= implode( "", $level_items );
		}
		return $buffer_aux;
	}

  	/**
	 * Generate breadcrumbs to print out as A tags
	 *
	 * @param string $split_char (optional) Character between breadcrumbs Default value: |
	 * @param string $classname (optional) Default value: breadcrumbs
	 *
	 * @return string
	 */	
	public function breadcrumbs( $split_char = ' | ', $classname = 'breadcrumbs' ) {
		$buffer = array();
		$items = $this->items;
		while ( count($items) > 0 ) {
			$found = false;
			foreach ( $items as $value ) {
				if ( $value->selected && $value->visible ) {
					$buffer[] = '<a href="'.( $value->href == '' ? 'javascript:{}' : $value->href ).'" '.( $value->target != '' ? 'target="'.$value->target.'"' : '' ).'>'.$value->text.'</a>';  				
					$items = $value->items;
					$found = true;
					break;
				}
			}
			if ( !$found ) //if selected was not found stop loop
				$items = array();
		}
		return '<div class="'.$classname.'">'.implode('<span class="'.$classname.'_spacer">'.$split_char.'</span>',$buffer).'</div>';
	}
 
 	/**
	 * Get selected menu
	 * @return array Array of menu items
	 */	
	public function get_selected() {
		$return = array();
		
		$items = $this->items;
		while ( count($items) > 0 ) {
			$found = false;
			foreach ( $items as $value ) {
				if ( $value->selected && $value->visible ) {
					$return[] = $value;
					$items = $value->items;
					$found = true;
					break;
				}
			}
			if ( !$found ) //if selected was not found stop loop
				$items = array();
		}
		return $return;
	}

  	/**
	 * Set menu as selected
	 *
	 * @param string $index
	 * @param boolean $value (optional) Default value: true
	 *
	 * @return void
	 */
	public function select( $index, $value = true ) {
		foreach ( $this->items as $key => $value ) {
			$this->items[$key]->selected = false;
			if ( $index == $key || $this->items[$key]->select( $index, $value ) )
				return $this->items[$key]->selected = true;
		}
		return false;
	}

	/**
	 * Add menu item
	 *
	 * @param mixed $index
	 * @param string $text	 
	 * @param string $href (optional)
	 * @param string $target (optional)
	 * @param string $classname (optional)
	 * @param boolean $visible (optional)	Default value: true
	 * @param string $attr (optional)
	 *
	 * @return void
	 */		
	public function add( $index, $text, $href = '', $target = '_self', $classname ='', $visible = true, $attr = '' ) {	
		$menu_item = new o3_menu_item( $text, $href, $target, $classname, $visible, $attr );
		$menu_item->check_uri_flag = $this->check_uri_flag;
		
		if ( $index != '' ) {
			$menu_item->index = $index;
			$this->items[$index] = $menu_item;
		} else {
			$this->items[] = $menu_item;
		}

		//check if the current uri is the same as menu item's href
		if ( $this->check_uri( $href ) && $index != '' )
			$this->select( $index, true );		

		return $menu_item;
	}

	/**
	 * Get menu item
	 *
	 * @param mixed $index
	 *
	 * @return mixed
	 */		
	public function get( $index ) {	
		if ( $index != '' ) {			
			foreach ( $this->items as $key => $value ) {
				if ( $key == $index )
					return $value;
				if ( ( $menu_item = $value->get( $index ) ) !== false ) {
					return $menu_item;
				}
			}
		}
		return false;
	}
  
	/**
	 * Remove menu item
	 *
	 * @param mixed $index
	 *
	 * @return void
	 */	
	public function remove( $index ) {	
		unset($this->items[$index]);
	}

	/**
	 * Compare uri parts
	 *
	 * @param string $index
	 *
	 * @return void
	 */
	public function check_uri( $uri1 ) {
		$uri2 = $_SERVER['REQUEST_URI'];
		$uri2_parts = parse_url($uri2);
		$uri2_full_host = o3_get_host();
		$uri2_full = $uri2_full_host.$uri2;

		$uri1_parts = parse_url($uri1); 
		$part = ( isset($uri1_parts['path']) ? $uri1_parts['path'] : '' );
		if ( isset($part[0]) && $part[0] != '/' )
			$part = substr( $uri2, 0, strrpos($uri2, '/') ).'/'.$part;
		$uri1_full = ( isset($uri1_parts['scheme']) ? $uri1_parts['scheme'].'://' : O3_HOST_PROTOCOL.'://' ).
					 ( isset($uri1_parts['host']) ? $uri1_parts['host'] : O3_HOST ).
					 ( isset($uri1_parts['port']) ? ':'.$uri1_parts['port'] : ( O3_HOST_PORT != 80 && O3_HOST_PORT != 443 && O3_HOST_PORT != '' ? ':'.O3_HOST_PORT : '' ) ).	
					 $part.
					 ( isset($uri1_parts['query']) ? '?'.$uri1_parts['query'] : '' ).
					 ( isset($uri1_parts['fragment']) ? '#'.$uri1_parts['fragment'] : '' );
		
		return o3_compare_uri( $uri1_full, $uri2_full, $this->check_uri_flag );
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

/**
 * O3 Engine menu item
 *
 * @category module
 * @package o3_menu
 */
class o3_menu_item {

	/** string Menu item index */
	var $index = '';

	/** string Text to display */
	var $text = '';
	
	/** string Link href */
	var $href = '';
	
	/** string Link target */
	var $target = '';
	
	/** string Additional class names */
	var $classname = '';
	
	/** string Additional attributes */
	var $attr = '';
	
	/** array List of sub menu items */
	public $items = array();
	
	/** string Selected menu */
	var $selected = false;

	/** Flag for uri compare algorithm */
	public $check_uri_flag = 0;
	
	/**
	 * O3 menu item constructor
	 *
	 * @param mixed $index
	 * @param string $text	 
	 * @param string $href (optional)
	 * @param string $target (optional)
	 * @param string $classname (optional)
	 * @param boolean $visible (optional)	Default value: true
	 * @param string $attr (optional)
	 *
	 * @return void
	 */		
	public function __construct( $text, $href = '', $target = '_self', $classname ='', $visible = true, $attr = '' ) {
		$this->text = $text;
		$this->href = $href;
		$this->target = $target;		
		$this->classname = $classname;
		$this->visible = $visible;		
		$this->attr = $attr;
		$this->check_uri_flag = ( O3_COMPARE_URI_HOST | O3_COMPARE_URI_PATH | O3_COMPARE_URI_QUERY );
	}

	/**
	 * Add menu item
	 *
	 * @param mixed $index
	 * @param string $text	 
	 * @param string $href (optional)
	 * @param string $target (optional)
	 * @param string $classname (optional)
	 * @param boolean $visible (optional)	Default value: true
	 * @param string $attr (optional)
	 *
	 * @return void
	 */		
	public function add( $index, $text, $href = '', $target = '_self', $classname ='', $visible = true, $attr = '' ) {	
		$menu_item = new o3_menu_item( $text, $href, $target, $classname, $visible, $attr );		

		if ( $index != '' ) {
			$menu_item->index = $index;
			$this->items[$index] = $menu_item;

			//check if the current uri is the same as menu item's href
			if ( $this->check_uri( $href ) )
				$this->select( $index, true );
		
			return $this->items[$index];
		} else {
			return $this->items[] = $menu_item;
		}
	}

	/**
	 * Get menu item
	 *
	 * @param mixed $index
	 *
	 * @return mixed
	 */		
	public function get( $index ) {	
		if ( $index != '' ) {			
			foreach ( $this->items as $key => $value ) {
				if ( $key == $index )
					return $value;
				if ( ( $menu_item = $value->get( $index ) ) !== false ) {
					return $menu_item;
				}
			}
		}
		return false;
	}

	/**
	 * Remove menu item
	 *
	 * @param mixed $index
	 *
	 * @return void
	 */	
	public function remove( $index ) {	
		unset($this->items[$index]);
	}
  
  	/**
	 * Set menu as selected
	 *
	 * @param string $index
	 * @param boolean $value (optional) Default value: true
	 *
	 * @return void
	 */
	public function select( $index, $value = true ) {
		foreach ( $this->items as $key => $value ) {  		
			$this->items[$key]->selected = false;
			if ( $index == $key || $this->items[$key]->select( $index, $value ) )
				return $this->items[$key]->selected = true;
		}
		return false;
	}

	/**
	 * Compare uri parts
	 *
	 * @param string $index
	 *
	 * @return void
	 */
	public function check_uri( $uri1 ) {
		$uri2 = $_SERVER['REQUEST_URI'];
		$uri2_parts = parse_url($uri2);
		$uri2_full_host = o3_get_host();
		$uri2_full = $uri2_full_host.$uri2;

		$uri1_parts = parse_url($uri1); 
		$part = ( isset($uri1_parts['path']) ? $uri1_parts['path'] : '' );
		if ( $part[0] != '/' )
			$part = substr( $uri2, 0, strrpos($uri2, '/') ).'/'.$part;
		$uri1_full = ( isset($uri1_parts['scheme']) ? $uri1_parts['scheme'].'://' : O3_HOST_PROTOCOL.'://' ).
					 ( isset($uri1_parts['host']) ? $uri1_parts['host'] : O3_HOST ).
					 ( isset($uri1_parts['port']) ? ':'.$uri1_parts['port'] : ( O3_HOST_PORT != 80 && O3_HOST_PORT != 443 && O3_HOST_PORT != '' ? ':'.O3_HOST_PORT : '' ) ).	
					 $part.
					 ( isset($uri1_parts['query']) ? '?'.$uri1_parts['query'] : '' ).
					 ( isset($uri1_parts['fragment']) ? '#'.$uri1_parts['fragment'] : '' );
					 
		return o3_compare_uri( $uri1_full, $uri2_full, $this->check_uri_flag );
	}
  
}

?>