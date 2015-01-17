<?php

/**
 * Form module for O3 Engine
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
* Base class for o3 form module classes
*/
class o3_form_base_class {

	//form is in scope, all fields will be added to this form
	static $o3_form_base_current_form = '';

	//if in form is in scope create knockout bindings
	static $o3_form_base_current_knockout = true;	

	//type of field - form, text, percent, number, checkbox, radio
	public $type = 'text';

	//label
	public $label = '';

	//mandatory to be filled
	public $mandatory_ = false;

	//checked only for radio and checkbox
	public $checked_ = false;

	//error msg to show on not valid
	public $error_ = '';	

	//parent form of input
	public $form_ = '';	

	//attributes for field (input/select)
	public $field_attr_ = '';

	//data-bind attribute for field (input/select)
	public $field_bind_ = array();

	//attributes for the container
	public $attr_ = '';

	//data-bind attribute for the container
	public $bind_ = array();

	//node slist for nested fields like checkbox list, radio
	public $nodes = array();

	/*
	* Constructor
	*/
	function __construct( $type = 'text', $label = '', $id = '', $value = null ) {
		//set scope from
		$this->form( o3_form_base_class::$o3_form_base_current_form );

		//initialize
		$this->type = $type;
		$this->label = $label;				
		$this->field_attr( 'value', $value === null ? '' : $value );
		$this->field_attr( 'id', $id );

		//set container id
		$this->attr( 'id', 'o3-form-field-'.( $this->form() != '' ? $this->form().'-' : '' ).$this->field_attr('id') );
		$this->attr( 'class', 'o3-form-field-holder o3-form-field-holder-'.$this->type );

		$name = $id;
		if ( $type != 'form' )
			$name = ( $this->form() != '' ? $this->form().'[' : '' ).$id.( $this->form() != '' ? ']' : '' );
		$this->field_attr( 'name', $name );

		//set o3 form type attr for javascript
		$this->field_attr('o3-form-type',$this->type);
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {
		$buffer = '';
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;		
	}

	/*
	* Set or get field attributes
	*
	* @param $attr_name Name of the attribute to get or set
	* @param $attr_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function field_attr() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->field_attr_[$args[0]] = $args[1];
			} else {
				//append
				$this->field_attr_[$args[0]] = trim( ( isset($this->field_attr_[$args[0]]) ? $this->field_attr_[$args[0]].' ' : '' ).$args[1] );
			}
		} else if ( func_num_args() > 0 ) {
			return isset($this->field_attr_[$args[0]]) ? $this->field_attr_[$args[0]] : '';
		}
		return $this;
	}

	/*
	* Set or get attributes for container
	*
	* @param $attr_name Name of the attribute to get or set
	* @param $attr_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function attr() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->attr_[$args[0]] = $args[1];
			} else {
				//append
				if ( isset($args[0]) )
				$this->attr_[$args[0]] = trim( ( isset($this->attr_[$args[0]]) ? $this->attr_[$args[0]].' ' : '' ).$args[1] );
			}
		} else if ( func_num_args() > 0 ) {
			return $this->attr_[$args[0]];
		}
		return $this;
	}

	/*
	* Set form true / false or get value
	*/
	public function form() {
		$args = func_get_args();
		if ( func_num_args() > 0 ) {
			$this->form_ = $args[0];
		} else {
			return $this->form_;
		}
		return $this;
	}

	/*
	* Set mandatory true / false or get value
	*/
	public function mandatory() {
		$args = func_get_args();
		if ( func_num_args() > 0 ) {
			$this->mandatory_ = $args[0];
		} else {
			return $this->mandatory_;
		}
		return $this;
	}

	/*
	* Set checked true / false or get value
	*/
	public function checked() {
		$args = func_get_args();
		if ( func_num_args() > 0 ) {
			$this->checked_ = $args[0];
		} else {
			return $this->checked_;
		}
		return $this;
	}

	/*
	* Set error message or get value
	*/
	public function error() {
		$args = func_get_args();
		if ( func_num_args() > 0 ) {
			$this->error_ = $args[0];
		} else {
			return $this->error_;
		}
		return $this;
	}

	/*
	* Set or get data-bind attribute values
	*	
	* @param $bind_name Name of the attribute to get or set
	* @param $bind_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function field_bind() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->field_bind_[$args[0]] = $args[1];
			} else {
				//append and trim the sep. comma
				$this->field_bind_[$args[0]] = o3_trim( ( isset($this->field_bind_[$args[0]]) ? $this->field_bind_[$args[0]].', ' : '' ).o3_trim( $args[1], ',' ), ',' );				
			}
		} else if ( func_num_args() > 0 ) {			
			return $this->field_bind_[$args[0]];
		}
		return $this;
	}

	/*
	* Set or get data-bind attribute values for container
	*
	* @param $bind_name Name of the attribute to get or set
	* @param $bind_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function bind() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->bind_[$args[0]] = $args[1];
			} else {
				//append and trim the sep. comma
				$this->bind_[$args[0]] = o3_trim( ( isset($this->bind_[$args[0]]) ? $this->bind_[$args[0]].', ' : '' ).o3_trim( $args[1], ',' ), ',' );				
			}
		} else if ( func_num_args() > 0 ) {
			return $this->bind_[$args[0]];
		}
		return $this;
	}

	/*
	* Returns data-bind attribute value
	*/
	public function get_field_bind() {
		$binds = array();
		foreach ( $this->field_bind_ as $key => $value ) {
			if ( $key == 'css' || $key == 'event' || $key == 'attr' )
				$value = '{ '.$value.' }';
			$binds[] = ' '.$key.': '.$value.' ';
		}
		return implode(', ', $binds);
	}

	/*
	* Returns data-bind attribute container
	*/
	public function get_bind() {
		$binds = array();
		foreach ( $this->bind_ as $key => $value ) {
			if ( $key == 'css' || $key == 'event' || $key == 'attr' )
				$value = '{ '.$value.' }';
			$binds[] = ' '.$key.': '.$value.' ';
		}
		return implode(', ', $binds);
	}

	/*
	* Returns attributes string for input
	*/
	public function get_field_attr() {

		$bind = $this->get_field_bind();
		if ( $bind != '' )
			$this->field_attr( 'data-bind', $bind );		

		$attrs = array();
		foreach ( $this->field_attr_ as $key => $value ) 
			$attrs[] = ' '.$key.'="'.o3_html($value,ENT_COMPAT).'" ';

		return implode( ' ', $attrs);
	}

	/*
	* Returns attributes string for container
	*/
	public function get_attr() {

		$bind = $this->get_bind();
		if ( $bind != '' )
			$this->attr( 'data-bind', $bind );		

		$attrs = array();
		foreach ( $this->attr_ as $key => $value ) 
			$attrs[] = ' '.$key.'="'.o3_html($value,ENT_COMPAT).'" ';

		return implode( ' ', $attrs);
	}

	/*
	* Add node
	* @param - $label
	* @param - $value
	*/
	function node( $label, $value, $id = "" ) {
		$this->nodes[] = new o3_form_radio_node( $label, $id, $value );
		return $this;
	}

	/*
	* Set / get validator function name
	*/
	function validator() {
		$args = func_get_args();
		if ( func_num_args() > 0 ) {
			$this->field_attr('o3-form-validator',$args[0],true);
			return $this;
		}
		return $this->field_attr('o3-form-validator');		
	}

}

/**
* Base class for HTML input/select field
*/
class o3_form_field_base_class extends o3_form_base_class {

	//attributes for error
	var $error_attr_ = array();

	/*
	* Set or get error attributes
	* If 1 parameter passed gets the attribute, if 2 parameter passed sets the attribute with the 2nd parameter value
	*/
	function error_attr() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			$this->error_attr_[$args[0]] = $args[1];
		} else if ( func_num_args() > 0 ) {
			return $this->error_attr_[$args[0]];
		}
		return $this;
	}

	/*
	* Returns error attributes string for input
	*/
	public function get_error_attr() {
		$error_attrs = array();
		foreach ( $this->error_attr_ as $key => $value ) {
			$error_attrs[] = ' '.$key.'="'.$value.'" ';
		}

		return implode( ' ', $error_attrs);
	}



}

/**
* HTML text input email value
*/
class o3_form_email extends o3_form_field_base_class {

	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null ) {
		global $o3;
		parent::__construct( 'email', $label, $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );

			//Add knockout value
			$this->field_bind( 'value', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
		
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//validator function 
		$this->validator('email');

		//add mandatory class
		/*if ( $this->mandatory() )
			$this->attr( 'class', $this->attr( 'class' ).' o3-form-field-mandatory ' );
		*/

		//Set valid decimal error message
		$this->error( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_VALID_EMAIL) : O3_FORM_ERR_VALID_EMAIL );

		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {

		$buffer = '<div '.$this->get_attr().'>
				   		<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>';
				
		$buffer .= '<input type="text" '.$this->get_field_attr().' />';
				
		$buffer .= '<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {
		global $o3;	
		
		//if mandatory
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( trim($this->error()."\n".( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY ) ) );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');
		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->field_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').".o3_showError()" );			

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError() : false' );		
			}

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );

		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;		
	}

}

/**
* HTML text input float value
*/
class o3_form_number extends o3_form_field_base_class {

	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null ) {
		global $o3;
		parent::__construct( 'number', $label, $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );

			//Add knockout value
			$this->field_bind( 'value', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
		
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//validator function 
		$this->validator('number');

		//add mandatory class
		if ( $this->mandatory() )
			$this->attr( 'class', $this->attr( 'class' ).' o3-form-field-mandatory ' );

		//Set valid decimal error message
		$this->error( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_VALID_NUMBER) : O3_FORM_ERR_VALID_NUMBER );

		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {

		$buffer = '<div '.$this->get_attr().'>
				   		<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>';
				
		$buffer .= '<input type="text" '.$this->get_field_attr().' />';
				
		$buffer .= '<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {
		global $o3;	
		
		//if mandatory
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( trim($this->error()."\n".( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY ) ) );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');
		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->field_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').".o3_showError()" );			

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError() : false' );		
			}

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );

		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;		
	}

}

/**
* HTML text input float value
*/
class o3_form_float extends o3_form_field_base_class {

	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null ) {
		global $o3;
		parent::__construct( 'float', $label, $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );

			//Add knockout value
			$this->field_bind( 'value', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
			
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//validator function 
		$this->validator('float');

		//Set valid decimal error message
		$this->error( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_VALID_FLOAT) : O3_FORM_ERR_VALID_FLOAT );

		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}
		
	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {

		//add mandatory class
		if ( $this->mandatory() )
			$this->attr( 'class', $this->attr( 'class' ).' o3-form-field-mandatory ' );

		$buffer = '<div '.$this->get_attr().'>
						<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>';
				
		$buffer .= '<input type="text" '.$this->get_field_attr().' />';
				
		$buffer .= '<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {		
		global $o3;	
		
		//if mandatory 
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( trim($this->error()."\n".( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY ) ) );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');
		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->field_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').".o3_showError() " );

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError() : false' );		
			}

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );

		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML text input
*/
class o3_form_select extends o3_form_field_base_class {

	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null ) {
		parent::__construct( 'select', $label, $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );

			//Add knockout value
			$this->field_bind( 'value', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
			
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {

		//add mandatory class
		if ( $this->mandatory() )			
			$this->attr( 'class', $this->attr( 'class' ).' o3-form-field-mandatory ' );

		$buffer = '<div '.$this->get_attr().'>
						<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>
						<select '.$this->get_field_attr().'>';

		foreach ( $this->nodes as $node )
			$buffer .= '<option value="'.o3_html($node->field_attr('value')).'">'.$node->label.'</option>';

		$buffer .= '</select>
					<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
	
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {		
		global $o3;	
		
		//if mandatory 
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');

		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->field_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').".o3_showError()" );

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError() : false' );
			}

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );
		
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML text input
*/
class o3_form_text extends o3_form_field_base_class {

	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null ) {
		parent::__construct( 'text', $label, $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );

				//Add knockout value
			$this->field_bind( 'value', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
			
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}
		
	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {

		//add mandatory class
		if ( $this->mandatory() )
			$this->attr( 'class', $this->attr( 'class' ).' o3-form-field-mandatory ' );

		$buffer = '<div '.$this->get_attr().'>
						<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>';
				
		$buffer .= '<input type="'.$this->type.'" '.$this->get_field_attr().' />';
				
		$buffer .= '<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {		
		global $o3;	
		
		//if mandatory 
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');
		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->field_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').".o3_showError()" );

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError() : false' );
			}

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );
		
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML text input
*/
class o3_form_textarea extends o3_form_field_base_class {

	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null ) {
		parent::__construct( 'textarea', $label, $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );

				//Add knockout value
			$this->field_bind( 'value', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
			
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}
		
	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {

		//add mandatory class
		if ( $this->mandatory() )
			$this->attr( 'class', $this->attr( 'class' ).' o3-form-field-mandatory ' );

		$buffer = '<div '.$this->get_attr().'>
						<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>';
				
		$buffer .= '<textarea '.$this->get_field_attr().'></textarea>';
				
		$buffer .= '<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
			

	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {		
		global $o3;	
		
		//if mandatory 
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( trim($this->error()."\n".( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY ) ) );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');			

		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )
			//data-bind
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->field_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').".o3_showError()" );

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError() : false' );
			}

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );
		
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML hidden input
*/
class o3_form_hidden extends o3_form_field_base_class {

	/*
	* Constructor
	*/
	function __construct( $id, $value = null ) {
		parent::__construct( 'hidden', '', $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {			
			//Add knockout value
			$this->field_bind( 'value', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );					
		}
		
	}
		
	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {
		$buffer = '<input type="'.$this->type.'" '.$this->get_field_attr().' />';
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {		
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML text input
*/
class o3_form_password extends o3_form_field_base_class {

	// False input type is text else password
	public $show_characters = false;

	//password check param.
	public $min_length = 8; 
	public $max_length = 32;
	public $special_chars = "-+=?!*:;.,@#$^%&";

	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null, $min_length = 8, $max_length = 32, $special_chars = "-+=?!*:;.,@#$^%&" ) {
		global $o3;
		parent::__construct( 'password', $label, $id, $value );

		//password check param.
		$this->min_length = $min_length; 
		$this->max_length = $max_length;
		$this->special_chars = $special_chars;

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );

			//Add knockout value
			$this->field_bind( 'value', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );			
			
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//validator function 
		$this->validator('password');

		//Set valid decimal error message
		$this->error( sprintf( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_VALID_PASSWORD) : O3_FORM_ERR_VALID_PASSWORD, $this->min_length, $this->max_length, $this->special_chars ) );

		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}
		
	/*
	* Input type is password or text
	*/
	public function show_characters( $value = true ) {
		$this->show_characters = true;
		return $this;
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {
				
		$buffer = '<div '.$this->get_attr().'>
						<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>';
				
		$buffer .= '<input type="'.( $this->show_characters ? 'text' : 'password' ).'" '.$this->get_field_attr().' />';
				
		$buffer .= '<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {		
		global $o3;	
		
		//if mandatory 
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( trim($this->error()."\n".( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY ) ) );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');			
		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->field_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').".o3_showError()" );			

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError() : false' );		
			}

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );
		
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML checkbox input
*/
class o3_form_check extends o3_form_field_base_class {

	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null ) {
		parent::__construct( 'checkbox', $label, $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );

			//Add knockout checked
			$this->field_bind( 'checked', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
		
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}
	
	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {
		
		//add mandatory class
		if ( $this->mandatory() )
			$this->attr( 'class', $this->attr( 'class' ).' o3-form-field-mandatory ' );

		$buffer = '<div '.$this->get_attr().'>
						<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>';
				
		$buffer .= '<input type="checkbox" '.$this->get_field_attr().' />';
				
		$buffer .= '<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/	
	public function flush( $output = true ) {		
		global $o3;	
				
		if ( $this->checked() ) {
			$this->field_attr('checked','checked');
		} else {
			unset($this->field_attr_['checked']);
		}

		//if mandatory 
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');
		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )			
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->field_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').".o3_showError()" );

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError() : false' );
			}

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );
		
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML checkbox node object
*/
class o3_form_radio_node extends o3_form_field_base_class  {

	/*
	* Constructor	
	*/
	function __construct( $label, $id = '', $value = null ) {
		parent::__construct( 'radio-node', $label, $id, $value );		

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );
		
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {
		
		$buffer = '<div class="o3-form-radio-node">
						<div class="o3-form-radio-node-spacer-top"></div>
						<input type="radio" '.$this->get_field_attr().' />
						<label class="o3-form-radio-node-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>
						<div class="o3-form-radio-node-spacer-bottom"></div>
				   </div>
				  ';

		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/	
	public function flush( $output = true, $parent_name = '', $parent_id = '' ) {		
		global $o3;	

		//set name
		$this->field_attr('name', $parent_name );

		//Add knockout checked
		$this->field_bind( 'checked', ( $this->form() != '' ? $this->form().'.field.' : '' ).$parent_id );

		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML checkbox input
*/
class o3_form_radio extends o3_form_field_base_class {
	
	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null ) {
		parent::__construct( 'radio', $label, $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			;
		}

		$this->field_attr( 'class', 'o3-form-radio-list-holder' );

		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}
	
	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {
		
		//add mandatory class
		if ( $this->mandatory() )
			$this->attr( 'class', $this->attr( 'class' ).' o3-form-field-mandatory ' );

		$buffer = '<div '.$this->get_attr().'>
						<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>';
				
		$buffer .= '<div '.$this->get_field_attr().'>';

		if ( count($this->nodes) > 0  ) {
			foreach ($this->nodes as $node ) {
				$buffer .= $node->flush( false, $this->field_attr('name'), $this->field_attr('id') );
			}
		}

		$buffer .= '</div>';
				
		$buffer .= '<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/	
	public function flush( $output = true ) {		
		global $o3;	
		
		/*
		todo: radio logic
		if ( $this->checked() ) {
			$this->field_attr('checked','checked');
		} else {
			unset($this->field_attr_['checked']);
		}

		//if mandatory 
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');
		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->field_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.' : '' )."field.".$this->field_attr('id').".o3_showError()" );

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.' : '' ).'field.'.$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.' : '' ).'field.'.$this->field_attr('id').'.o3_showError() : false' );
			}		

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );
		*/		

		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML file input value
*/
class o3_form_file extends o3_form_field_base_class {

	//attributes for upload (input/select)
	public $upload_attr_ = '';

	//data-bind attribute for upload (input/select)
	public $upload_bind_ = array();

	//attributes for upload holder (input/select)
	public $upload_holder_attr_ = '';

	//data-bind attribute for upload holder (input/select)
	public $upload_holder_bind_ = array();

	//upload holder label
	public $upload_holder_label = null;

	//attributes for result (input/select)
	public $result_attr_ = '';

	//data-bind attribute for result (input/select)
	public $result_bind_ = array();

	//accept file type
	public $accept = '';

	//max allowed file size
	public $max_upload_size = 0;	
 
	/*
	* Constructor
	*/
	function __construct( $label, $id, $value = null, $filename = null ) {
		global $o3;
		
		$filename = $filename == null || $filename == '' ? '' : $filename;
		$value = $value == null || $value == '' ? '' : $value.( $filename != '' ? O3_FORM_FILE_NAME_PATH_SEP.$filename : '' ); 
		parent::__construct( 'file', $label, $id, $value );

		//set max allowed upload size
		$this->max_upload_size( o3_upload_max_size() );

		//load upclick - file uploader
		$o3->head_res( 'o3_upclick' );	

		//setup upload attr id
		$this->upload_attr( 'id', $id.'_'.md5(time()) );

		//field attr
		$this->field_attr( 'data-o3-upload-field', $this->upload_attr( 'id' ) );
		$this->field_attr( 'data-o3-upload-value', $value );

		//setup result attr 
		$this->result_attr( 'id', $this->upload_attr( 'id' ).'_result' );
		$this->result_attr( 'class', 'o3-form-field-file-display' );

		//set container id
		$this->attr( 'id', 'o3-form-field-'.( $this->form() != '' ? $this->form().'-' : '' ).$this->field_attr('id') );
		$this->attr( 'class', 'o3-form-field-holder o3-form-field-holder-'.$this->type );

		//add placeholder
		//$this->field_attr( 'placeholder', o3_html(isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_FILE_NO_FILE_SELECTED) : O3_FORM_FILE_NO_FILE_SELECTED) );
		
		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add knockout update by keypress
			$this->field_bind( 'valueUpdate', "'keyup'" );

			//Add knockout value
			$this->field_bind( 'value', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
		
			//Event to readonly on loading
			//$this->field_bind( "attr", "readonly: ".( $this->form() != '' ? $this->form().'.' : '' )."loading" );

			//upload
			$this->upload_bind( 'visible', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id')."() == '' " );
			$this->upload_holder_bind( 'visible', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id')."() == '' " );

			//result
			$this->result_bind( 'visible', ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id')."() != '' " );


		}
		
		//Add class to error msg
		$this->error_attr( 'class', 'o3-form-field-error' );
	}

	/*
	* Set/get max allowed upload size
	*/
	public function max_upload_size() {
		$args = func_get_args();
		if ( count($args) > 0 ) {
			$this->max_upload_size = $args[0];
			$this->max_upload_size = $this->max_upload_size > o3_upload_max_size() ? o3_upload_max_size() : $this->max_upload_size;
			return $this;
		}
		return $this->max_upload_size;
	}

	/*
	* Set/get accept file type
	*/
	public function accept() {
		$args = func_get_args();
		if ( count($args) > 0 ) {
			$this->accept = $args[0];
			return $this;
		}
		return $this->accept;
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {
		global $o3;

		$buffer = '<div '.$this->get_attr().'>
				   		<div class="o3-form-field-spacer-top"></div>
						<label class="o3-form-field-label" class="o3-form-field-label" for="'.$this->field_attr('id').'">'.$this->label.'</label>';

		$field_name = ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id');

		$buffer .= '<input type="hidden" '.$this->get_field_attr().' />
					<div class="o3-form-field-file-holder">
						<div class="o3-form-field-file-input-holder" '.$this->get_upload_holder_attr().'>
							<span>'.( $this->upload_holder_label !== null ? $this->upload_holder_label : ( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_FILE_SELECT_FILE) : O3_FORM_FILE_SELECT_FILE ) ).'</span>
							<input type="file" '.$this->get_upload_attr().' />
						</div>

						<div '.$this->get_result_attr().'>
							<div>
								<div class="o3-form-field-file-display-input">
									<input type="text" readonly="readonly" data-bind="value: '.$field_name.'.filename()" />
								</div>
								<div class="o3-form-field-file-display-progress" data-bind="visible: '.$field_name.'.progress() > -1, text: '.$field_name.'.progress()+\'%\'">
									0%
								</div>
								<div class="o3-form-field-file-display-remove" data-bind="click: '.$field_name.'.cancel">
									X
								</div>
							</div>
						</div>
					</div>';
				
		$buffer .= '<div class="o3-form-field-spacer"></div>
					<div '.$this->get_error_attr().'>'.nl2br(o3_html( isset($this->error_attr_['text']) ? $this->error_attr_['text'] : '' )).'</div>
					<div class="o3-form-field-spacer-bottom"></div>
				</div>';

		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {
		global $o3;
		
		//if mandatory
		if ( $this->mandatory() ) {

			//update the error text
			$this->error( trim($this->error()."\n".( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_MANDATORY) : O3_FORM_ERR_MANDATORY ) ) );				

			//javascript reference
			$this->field_attr('o3-form-mandatory','true');
		}

		//if validator or mandatory add error
		if ( $this->validator() != '' || $this->mandatory() )			
			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				$this->field_bind( "o3_validate", ( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id') );
				$this->upload_bind( "css", "'o3-field-error': ".( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').".o3_showError()" );			

				//show field error
				$this->error_attr( 'data-bind', 'o3_slideVisible: '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError ? '.( $this->form() != '' ? $this->form().'.field.' : '' ).$this->field_attr('id').'.o3_showError() : false' );		
			}

		if ( !isset($this->error_attr_['text']) )
			$this->error_attr( 'text', $this->error() );

		//set accept
		$this->upload_attr( 'accept', $this->accept() );

		//set accept
		$this->upload_attr( 'data-o3-form-field-file-max-upload', $this->max_upload_size() );

		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;		
	}

	/*
	* Returns attributes string for input
	*/
	public function get_upload_attr() {

		$bind = $this->get_upload_bind();
		if ( $bind != '' )
			$this->upload_attr( 'data-bind', $bind );		

		$attrs = array();
		foreach ( $this->upload_attr_ as $key => $value ) 
			$attrs[] = ' '.$key.'="'.o3_html($value,ENT_COMPAT).'" ';

		return implode( ' ', $attrs);
	}


	/*
	* Returns data-bind attribute value
	*/
	public function get_upload_bind() {
		$binds = array();
		foreach ( $this->upload_bind_ as $key => $value ) {
			if ( $key == 'css' || $key == 'event' || $key == 'attr' )
				$value = '{ '.$value.' }';
			$binds[] = ' '.$key.': '.$value.' ';
		}
		return implode(', ', $binds);
	}

	/*
	* Set or get upload attributes
	*
	* @param $attr_name Name of the attribute to get or set
	* @param $attr_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function upload_attr() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->upload_attr_[$args[0]] = $args[1];
			} else {
				//append
				$this->upload_attr_[$args[0]] = trim( ( isset($this->upload_attr_[$args[0]]) ? $this->upload_attr_[$args[0]].' ' : '' ).$args[1] );
			}
		} else if ( func_num_args() > 0 ) {
			return $this->upload_attr_[$args[0]];
		}
		return $this;
	}

	/*
	* Set or get data-bind attribute values
	*	
	* @param $bind_name Name of the attribute to get or set
	* @param $bind_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function upload_bind() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->upload_bind_[$args[0]] = $args[1];
			} else {
				//append and trim the sep. comma
				$this->upload_bind_[$args[0]] = o3_trim( ( isset($this->upload_bind_[$args[0]]) ? $this->upload_bind_[$args[0]].', ' : '' ).o3_trim( $args[1], ',' ), ',' );				
			}
		} else if ( func_num_args() > 0 ) {			
			return $this->upload_bind_[$args[0]];
		}
		return $this;
	}

	/*
	* Set/get upload holder label
	*/
	public function upload_holder_label() {
		$args = func_get_args();
		if ( func_num_args() == 1 ) {
			//append
			$this->upload_holder_label = $args[0];
			return $this;
		}
		return $this->upload_holder_label;			
	}

	/*
	* Returns attributes string for input
	*/
	public function get_upload_holder_attr() {

		$bind = $this->get_upload_holder_bind();
		if ( $bind != '' )
			$this->upload_holder_attr( 'data-bind', $bind );		

		$attrs = array();
		foreach ( $this->upload_holder_attr_ as $key => $value ) 
			$attrs[] = ' '.$key.'="'.o3_html($value,ENT_COMPAT).'" ';

		return implode( ' ', $attrs);
	}


	/*
	* Returns data-bind attribute value
	*/
	public function get_upload_holder_bind() {
		$binds = array();
		foreach ( $this->upload_holder_bind_ as $key => $value ) {
			if ( $key == 'css' || $key == 'event' || $key == 'attr' )
				$value = '{ '.$value.' }';
			$binds[] = ' '.$key.': '.$value.' ';
		}
		return implode(', ', $binds);
	}

	/*
	* Set or get upload holder attributes
	*
	* @param $attr_name Name of the attribute to get or set
	* @param $attr_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function upload_holder_attr() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->upload_holder_attr_[$args[0]] = $args[1];
			} else {
				//append
				$this->upload_holder_attr_[$args[0]] = trim( ( isset($this->upload_holder_attr_[$args[0]]) ? $this->upload_holder_attr_[$args[0]].' ' : '' ).$args[1] );
			}
		} else if ( func_num_args() > 0 ) {
			return $this->upload_holder_attr_[$args[0]];
		}
		return $this;
	}

	/*
	* Set or get data-bind attribute values
	*	
	* @param $bind_name Name of the attribute to get or set
	* @param $bind_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function upload_holder_bind() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->upload_holder_bind_[$args[0]] = $args[1];
			} else {
				//append and trim the sep. comma
				$this->upload_holder_bind_[$args[0]] = o3_trim( ( isset($this->upload_holder_bind_[$args[0]]) ? $this->upload_holder_bind_[$args[0]].', ' : '' ).o3_trim( $args[1], ',' ), ',' );				
			}
		} else if ( func_num_args() > 0 ) {			
			return $this->upload_holder_bind_[$args[0]];
		}
		return $this;
	}

	/*
	* Returns attributes string for input
	*/
	public function get_result_attr() {

		$bind = $this->get_result_bind();
		if ( $bind != '' )
			$this->result_attr( 'data-bind', $bind );		

		$attrs = array();
		foreach ( $this->result_attr_ as $key => $value ) 
			$attrs[] = ' '.$key.'="'.o3_html($value,ENT_COMPAT).'" ';

		return implode( ' ', $attrs);
	}


	/*
	* Returns data-bind attribute value
	*/
	public function get_result_bind() {
		$binds = array();
		foreach ( $this->result_bind_ as $key => $value ) {
			if ( $key == 'css' || $key == 'event' || $key == 'attr' )
				$value = '{ '.$value.' }';
			$binds[] = ' '.$key.': '.$value.' ';
		}
		return implode(', ', $binds);
	}

	/*
	* Set or get result attributes
	*
	* @param $attr_name Name of the attribute to get or set
	* @param $attr_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function result_attr() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->result_attr_[$args[0]] = $args[1];
			} else {
				//append
				$this->result_attr_[$args[0]] = trim( ( isset($this->result_attr_[$args[0]]) ? $this->result_attr_[$args[0]].' ' : '' ).$args[1] );
			}
		} else if ( func_num_args() > 0 ) {
			return $this->result_attr_[$args[0]];
		}
		return $this;
	}

	/*
	* Set or get data-bind attribute values
	*	
	* @param $bind_name Name of the attribute to get or set
	* @param $bind_value (optional) The value to set to the attribute 
	* @param $append (optional) If true the attribute is replaced with the value, else is appended
	*
	* @return mixed If 2nd parameter omited the value of attribute else the object itself
	*/
	public function result_bind() {
		$args = func_get_args();
		if ( func_num_args() > 1 ) {
			if ( isset($args[2]) && $args[2] === true ) {
				//replace
				$this->result_bind_[$args[0]] = $args[1];
			} else {
				//append and trim the sep. comma
				$this->result_bind_[$args[0]] = o3_trim( ( isset($this->result_bind_[$args[0]]) ? $this->result_bind_[$args[0]].', ' : '' ).o3_trim( $args[1], ',' ), ',' );				
			}
		} else if ( func_num_args() > 0 ) {			
			return $this->result_bind_[$args[0]];
		}
		return $this;
	}


}

/**
* HTML button
*/
class o3_form_button extends o3_form_base_class {	

	/*
	* Constructor
	*/
	function __construct( $value = '', $id = '' ) {
		parent::__construct( 'button', '', $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Event to disable on loading
			//$this->field_bind( "disable", ( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//add class
		$this->field_attr('class','o3-form-button');
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {		
		$buffer = '<div '.$this->get_attr().'><input type="'.$this->type.'" '.$this->get_field_attr().' /></div>';
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/	
	public function flush( $output = true ) {			
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML submit
*/
class o3_form_submit extends o3_form_base_class {	

	/*
	* Constructor
	*/
	function __construct( $value = '', $id = '' ) {
		parent::__construct( 'submit', '', $id, $value );

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Event to disable on loading
			$this->field_bind( "disable", ( $this->form() != '' ? $this->form().'.' : '' )."loading" );
		}

		//add class
		$this->field_attr('class','o3-form-submit');
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {		
		$buffer = '<div '.$this->get_attr().'><input type="'.$this->type.'" '.$this->get_field_attr().' /></div>';		
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/	
	public function flush( $output = true ) {
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML form openeing tag
*/
class o3_form_open extends o3_form_base_class {	

	//auto load javascript controller
	public $js_autoload_ = true;

	//security code for post
	private $security_code_ = '';

	/*
	* Constructor
	*/
	function __construct( $id = '', $action = '', $method = 'POST' ) {
		global $o3;		
		parent::__construct( 'form', '', $id, '' );
		
		//set form scope fior fields
		o3_form_base_class::$o3_form_base_current_form = $id;

		//Has no value attribute, we need to remove
		unset($this->field_attr_['value']);

		//add class
		$this->field_attr('class','o3-form');

		//set form action and method attr
		$this->field_attr('method',$method);
		$this->field_attr('action',$action);		

	}

	/*
	* Set javascript controller autoload true / false or get value
	*/
	public function autoload() {
		$args = func_get_args();
		if ( func_num_args() > 0 ) {
			$this->js_autoload_ = $args[0];
		} else {
			return $this->js_autoload_;
		}
		return $this;
	}

	/*
	* Create knockout bindings in form
	*/
	public function knockout() {
		$args = func_get_args();
		if ( func_num_args() > 0 ) {
			o3_form_base_class::$o3_form_base_current_knockout = $args[0];

			if ( o3_form_base_class::$o3_form_base_current_knockout ) {
				//set submit in data-bind
				$this->field_bind("submit", $this->field_attr('id').".submit");
			}

		} else {
			return o3_form_base_class::$o3_form_base_current_knockout;
		}
		return $this;
	}

	/**
	 * Generate new scurity code for the form
	 *
	 * @return string
	 */	
	private function security_code() {
		
		if ( $this->security_code_ == '' ) {
						
			//security code list
			$_SESSION['o3_form_security_code'] = !isset($_SESSION['o3_form_security_code']) ? array() : $_SESSION['o3_form_security_code'];				

			//generate security code
			$this->security_code_ = substr(md5(o3_micro_time()),0,8);
			while ( isset($_SESSION['o3_form_security_code'][$this->security_code_]) )
				$this->security_code_ = substr(md5(o3_micro_time()),0,8);

			$_SESSION['o3_form_security_code'][$this->security_code_] = $this->security_code_;
			
		}

		return $this->security_code_;		
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {			
		$buffer = '<div '.$this->get_attr().'><form '.$this->get_field_attr().' o3-form-code="'.o3_html($this->security_code()).'">';			
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/	
	public function flush( $output = true ) {

		//set autoload
		$this->field_attr('o3-form-autoload', $this->autoload() ? 'true' : 'false' );	

		$this->field_attr('o3-form-method',$this->field_attr('method'));
		unset($this->field_attr_['method']);

		$this->field_attr('o3-form-action',$this->field_attr('action'));
		unset($this->field_attr_['action']);

		//on autoload we need to disable external binding
		$this->bind('o3_stopBinding', $this->autoload() ? 'true' : 'false' );

		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}


/**
* HTML form closing tag
*/
class o3_form_close extends o3_form_base_class {	

	/*
	* Constructor
	*/
	function __construct() {
		o3_form_base_class::$o3_form_base_current_form = '';
		o3_form_base_class::$o3_form_base_current_knockout = false;
		parent::__construct( 'form', '', '', '' );
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {		
		$buffer = '</form></div>';
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML inner part opening tag, this is hidden on loading
*/
class o3_form_inner_open extends o3_form_base_class {

	/*
	* Constructor
	*/
	function __construct() {		
		parent::__construct( 'inner_open', '', '', '' );

		//Has no value, id and name attribute, we need to remove
		unset($this->field_attr_['value']);
		unset($this->field_attr_['id']);
		unset($this->field_attr_['name']);

		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			//Add event to hide on loading
			$this->field_bind( 'o3_slideVisible', '!'.( $this->form() != '' ? $this->form().'.' : '' ).'loading()' );
		}

		//add class
		$this->field_attr('class','o3-form-inner-open');

	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {		
		$buffer = '<div '.$this->get_field_attr().'>';
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML inner part closing tag, this is hidden on loading
*/
class o3_form_inner_close extends o3_form_base_class {

	/*
	* Constructor
	*/
	function __construct() {		
		parent::__construct( 'inner_close', '', '', '' );
		//Has no value attribute, we need to remove
		unset($this->field_attr_['value']);
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {		
		$buffer = '</div>';
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML error message holder. Only showed when is an error
*/
class o3_form_error extends o3_form_base_class {

	/*
	* Constructor
	*/
	function __construct() {		
		parent::__construct( 'error', '', '', '' );

		//Has no value, id and name attribute, we need to remove
		unset($this->field_attr_['value']);
		unset($this->field_attr_['id']);
		unset($this->field_attr_['name']);
		
		//add class
		$this->field_attr('class','o3-form-error');

		//Add event to show if error set
		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			$this->field_bind( 'o3_slideVisible', ( $this->form() != '' ? $this->form().'.' : '' ).'error() != \'\'' );
			$this->field_bind( 'text', ( $this->form() != '' ? $this->form().'.' : '' ).'error' );
		}

	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {		
		$buffer = '<div '.$this->get_field_attr().'></div>';
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML success message holder. Only showed when is a success message
*/
class o3_form_success extends o3_form_base_class {

	/*
	* Constructor
	*/
	function __construct() {		
		parent::__construct( 'success', '', '', '' );

		//Has no value, id and name attribute, we need to remove
		unset($this->field_attr_['value']);
		unset($this->field_attr_['id']);
		unset($this->field_attr_['name']);
		
		//add class
		$this->field_attr('class','o3-form-success');

		//Add event to show if success set
		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			$this->field_bind( 'o3_slideVisible', ( $this->form() != '' ? $this->form().'.' : '' ).'success() != \'\'' );
			$this->field_bind( 'text', ( $this->form() != '' ? $this->form().'.' : '' ).'success' );
		}

	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {		
		$buffer = '<div '.$this->get_field_attr().'></div>';
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {		
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* HTML form loader part
*/
class o3_form_loader extends o3_form_base_class {	

	//laoding message
	var $loading_text_ = '';
	
	/*
	* Constructor
	*/
	function __construct( $loading_text = null ) {
		global $o3;		
		parent::__construct( 'loader', '', '', '' );
		
		//Has no value, id and name attribute, we need to remove
		unset($this->field_attr_['value']);
		unset($this->field_attr_['id']);
		unset($this->field_attr_['name']);

		//add class
		$this->field_attr('class','o3-form-loader');

		//Add event to show on loading
		if ( o3_form_base_class::$o3_form_base_current_knockout ) {
			$this->field_bind( 'o3_slideVisible', ( $this->form() != '' ? $this->form().'.' : '' ).'loading()' );
		}

		//set loading message
		$this->text( $loading_text != null ? $loading_text : ( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_TXT_LOADING) : O3_FORM_TXT_LOADING ) );
	}

	/*
	* Set loading message or get value
	*/
	public function text() {
		$args = func_get_args();
		if ( func_num_args() > 0 ) {
			$this->loading_text_ = $args[0];
		} else {
			return $this->loading_text_;
		}
		return $this;
	}

	/* 
	* Buffer
	* @return string
	*/
	public function buffer() {		
		$buffer = '<div '.$this->get_field_attr().'>
						<span class="o3-form-loader-img"></span> <span class="o3-form-loader-text">'.o3_html($this->text()).'</span>
					</div>';
		return $buffer;
	}
		
	/* 
	* Print the buffer
	* @return self / string
	*/
	public function flush( $output = true ) {		
		$buffer = $this->buffer();
		if ( $output ) {
			echo $buffer;
			return $this;
		}
		return $buffer;
	}

}

/**
* Class for form ajax post result
*/
class o3_form_result extends o3_ajax_result {
	
	private $id = '';
	private $security_code_ = '';
	private $post = array();

	//custom data
	public $data_ = array();
 	
 	/*
	* Constructor
	*/
	public function __construct( $id ) {
		global $o3;
		$this->id = $id;
		$this->success_ = true;
		$this->success_msg_ = $this->success_msg_default_ = 'Data has been transferred successfully.';
		$this->error_msg_ = $this->error_msg_default_ = 'An error occurred. Please try again.';
		$this->redirect_ = '';
		$this->security_code_ = $this->security_code();		

		if ( isset($_POST[$this->id]) )
			$this->post = gettype($_POST[$this->id]) == 'string' ? json_decode($_POST[$this->id],true) : $_POST[$this->id];

		if ( isset($o3) && isset($o3->lang) ) {
			$this->success_msg_ = $this->success_msg_default_ = $o3->lang->_($this->success_msg_);
			$this->error_msg_ = $this->error_msg_default_ = $o3->lang->_($this->error_msg_);
		}

	}

	/*
	* Check field is posted 
	*/
	public function is( $field ) {
		return isset($this->post[$field]);
	}

	/*
	* Check value of field posted 
	*/
	public function value( $field ) {
		return $this->is( $field) ? $this->post[$field] : null;
	}

	/*
	* Check filename of field posted 
	*/
	public function filename( $field ) {
		$file = $this->is( $field) ? $this->post[$field] : null;
		$file = explode(O3_FORM_FILE_NAME_PATH_SEP, $file);
		return isset($file[1]) ? $file[1] : basename($file[0]);
	}

	/*
	* Check filepath of field posted 
	*/
	public function filepath( $field ) {
		$file = $this->is( $field) ? $this->post[$field] : null;
		$file = explode(O3_FORM_FILE_NAME_PATH_SEP, $file);
		return $file[0];
	}

	/*
	* Get array with all the poster fields
	*/
	public function all() {
		return $this->post;
	}
	
	/**
	 * Generate new scurity code for the form
	 *
	 * @return string
	 */	
	private function security_code() {
		
		if ( $this->security_code_ == '' ) {
			
			//security code list
			$_SESSION['o3_form_security_code'] = !isset($_SESSION['o3_form_security_code']) ? array() : $_SESSION['o3_form_security_code'];				

			//generate security code
			$this->security_code_ = substr(md5(o3_micro_time()),0,8);
			while ( isset($_SESSION['o3_form_security_code'][$this->security_code_]) )
				$this->security_code_ = substr(md5(o3_micro_time()),0,8);

			$_SESSION['o3_form_security_code'][$this->security_code_] = $this->security_code_;
			
		}

		return $this->security_code_;		
	}

	/*
	* Print out data as json
	* @return self
	*/
	public function flush() {
		$result = array(
			'success' => $this->success_,
			'success_msg' => $this->success_msg_,
			'error_msg' => $this->error_msg_,
			'redirect' => $this->redirect_,
			'o3-form-code' => $this->security_code_,
			'data' => $this->data_
		);
		die(json_encode($result));
	}

}

/*
* Check for form in post data
* If no valid  form post data send header 404
*/
function o3_form_posted( $id, $validate_form_code = true ) {
	//file upload
	if ( isset($_FILES[$id]) ) {
		$o3_form_result = new o3_form_result( $id );

		foreach ( $_FILES[$id]['name'] as $key => $value ) {
			$filename = isset($_FILES[$id]['name'][$key][0]) ? $_FILES[$id]['name'][$key][0] : $_FILES[$id]['name'][$key];
			$tmp_name = isset($_FILES[$id]['tmp_name'][$key][0]) ? $_FILES[$id]['tmp_name'][$key][0] : $_FILES[$id]['tmp_name'][$key];
			//keep file for 5 min
			$filepath = o3_temp_cache_file( strtolower($flags.$dpi.$background.filesize($source).filemtime($source).'-'.basename($filename)), 300 );
			
			//upload file
			if ( move_uploaded_file( $tmp_name, $filepath ) ) {
				//set data	
				$o3_form_result->data( 'filepath', $filepath );
				$o3_form_result->data( 'filename', $filename );
			};
		};

		$o3_form_result->flush();
	};

	//form post
	if ( isset($_POST[$id]) ) {
		$o3_form_result = new o3_form_result( $id );
		if ( $validate_form_code === true && !isset($_SESSION['o3_form_security_code'][$_POST['o3-form-code']]) ) {
			global $o3;
			//security check fail send 404
			//o3_header_code( 404 );
			//$o3_form_result->error( isset($o3) && isset($o3->lang) ? $o3->lang->_(O3_FORM_ERR_GENERAL) : O3_FORM_ERR_GENERAL );
			$o3_form_result->flush();
		}
		return $o3_form_result;
	};
	return false;
}

?>