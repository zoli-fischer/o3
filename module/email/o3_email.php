<?php

/**
 * Email sending module for O3 Engine
 *
 * Send emails with or without attachment.
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

use o3\module\email\o3_css2inline as o3_css2inline;

/**
 * Class for sending emails
 *
 * Send emails with or without attachment.
 * Email address examples:<br> John Test &lt;jt@domain.com&gt; <br> jt@domain.com <br> ad@domain.com, John Test &lt;jt@domain.com&gt;, test@domain.com
 *
 * @category module
 * @package o3_email
 */
class o3_email {
	
	/** mixed Parent object */
	public $parent = null;
	
	/** boolean If is true, all emails will be sent to test email. */
	private $is_test = O3_EMAIL_TEST_MODE; 
	
	/** boolean Test email address. All emails will be sent to this address in test mode. */
	public $test = O3_EMAIL_TEST_ADDRESS;
	
	/** string Path to the folder with email tempaltes json-s */
	public $template_dir = O3_EMAIL_DIR;
	 
	/**
	 * Set test email address and test mode
	 * 
	 * If all parameters are omitted the function returns the test email address.
	 * Email address examples:<br> John Test &lt;jt@domain.com&gt; <br> jt@domain.com <br> ad@domain.com, John Test &lt;jt@domain.com&gt;, test@domain.com
	 *
	 * @param string $test (optional) Set test email address
	 * @param boolean $is_test (optional) Set test mode
	 * @return string Test email address
	 */
	function test() {
		$args = func_get_args();
		if ( isset($args[0]) )
			$this->test = $args[0];		
		if ( isset($args[1]) )
			$this->is_test( $args[1] );
		return $this->test;
	}
	
	/**
	 * Set test mode 
	 * 
	 * True test is on, false test is off.
	 * If parameter is omitted the function returns the test mode.
	 *
	 * @param boolean $is_test (optional) Set test mode
	 * @return boolean Is test mode
	 */
	function is_test() {
		$args = func_get_args();
		if ( isset($args[0]) ) 
			$this->is_test = $args[0];
		return $this->is_test;
	}
	
	/** string Email address from where emails will be sent */
 	private $from = O3_EMAIL_SEND_FROM;
	
	//set default send from
	//from - ex. test <ts@ydsf.com> OR trsr@sf.com
	
	/**
	 * Set email address from where emails will be sent
	 * 
	 * If parameter is omitted the function returns the from email address.
	 * Email address like:<br> John Test <jt@domain.com> <br> jt@domain.com <br> ad@domain.com, John Test <jt@domain.com>, test@domain.com
	 *
	 * @param string $from (optional) From email address
	 * @return string From email address
	 */
	function from() {
		$args = func_get_args();
		if ( isset($args[0]) ) 
			$this->from = $args[0];
		return $this->from;
	}
	
	/**
	 * Constructor of email
	 * @return void
	 */
	public function __construct() {
		;
	}
  
  	/**
	 * Set the path to the folder with email templates json-s
	 * @param string $path
	 * @return void
	 */
	function set_dir( $path ) {
		$this->template_dir = $path;
	}
  
  /**
	 * Function sets the parent object
	 *
	 * @param object $parent Parent object	
	 * @return object
	 */	
	function set_parent( $parent ) {
		return $this->parent = &$parent;
	}
  
  	//send mail by index
	//o3_email_data with send/mail informaton 
	//arguments - relpace template arguments ex. {$1] - 1st param, {$2} - 2nd param
	//return: MIXED
	
	/**
	 * Send email
	 *
	 * The function has 1 parameter with type o3_email_data.
	 * If the o3_email_data template is not empty the message is replaced with the template.
	 *
	 * @see o3_email_data o3_email_data
	 *
	 * @example example.php
	 * //Send email to send-to-this-addess@domain.com with message 'Hello John This is a message' and with attachment logo.png
	 * $o3_email_data = new o3_email_data( 'send-to-this-addess@domain.com' );<br> 
	 * $o3_email_data->subject = 'This is a subject';
	 * $o3_email_data->message = '&lt;p>Hello __name__&lt;/p&gt;&lt;p&gt;This is a message&lt;/p&gt;';<br>
	 * $o3_email_data->add_attachment( 'res/logo.png' );<br>
	 * $o3_email_data->add_replace( '__name__', 'John' );<br>
	 * $o3->email->send( $o3_email_data );<br>
	 *
	 * @param o3_email_data $o3_email_data Email data
	 * @return boolean True if the email is sent
	 */	
	function send( $o3_email_data ) {
		
		$to = $o3_email_data->to;
		$cc = $o3_email_data->cc;
		$bcc = $o3_email_data->bcc;
		$subject = $o3_email_data->subject;
		$styles = $o3_email_data->styles;
		$message = $o3_email_data->message;
		$content_type = $o3_email_data->content_type;
		$from = $o3_email_data->from == '' ? $this->from : $o3_email_data->from; //get send from email
		$files = $o3_email_data->files;
		$send_callback = $o3_email_data->send_callback;
		$template = $o3_email_data->template;
		
		$mail_data = false;
		//is template index
		if ( $template > '' ) {
			$mail_data = $this->get( $template );
			$subject = $mail_data['subject'];		
			$content_type = $mail_data['content_type'];	
			$message = '';
			$styles = '';
			//add header
			if ( $mail_data['header'] != '' ) {
				$header_data = $this->get( $mail_data['header'] );
				if ( $header_data !== false ) {
					$message .= $header_data['body'];
					$styles .= $header_data['css'];
				}
			}
			//add body
			$message .= $mail_data['body'];
			$styles .= $mail_data['css'];
			//add footer
			if ( $mail_data['footer'] != '' ) {
				$footer_data = $this->get( $mail_data['footer'] );
				if ( $footer_data !== false ) {
					$message .= $footer_data['body'];
					$styles .= $footer_data['css'];
				}
			}
		} 
		
		//replace email template arguments		
		$from_tmp = array();
		$to_tmp = array();
		$args = func_get_args();
		if ( count($args) > 1 ) {
			for ( $i = 1; $i < count($args); $i++ ) {
				$from_tmp[] = '{param'.($i).'}';
				$to_tmp[] = $args[$i];				
			}
		}
		
		//check if replace defined
		if ( count($o3_email_data->replace) > 0 ) {
			foreach ( $o3_email_data->replace as $key => $value ) {
				$from_tmp[] = $value[0];
				$to_tmp[] = $value[1];
			}
		}

		//run object callbacks
		if ( isset($this) && count($this->send_callbacks) > 0 ) {
			foreach ( $this->send_callbacks as $key => $value ) {
				if ( !is_array($value) ) {
					$message = call_user_func( $value, $message );
					$subject = call_user_func( $value, $subject );
				} else if ( count($value) == 2 ) {	
					$message = call_user_func( array( $value[0], $value[1] ), $message );
					$subject = call_user_func( array( $value[0], $value[1] ), $subject );
				}
			}
		}
		
		//run parameter callback
		if ( $send_callback != '' ) {
			if ( !is_array($send_callback) ) {
				$message = call_user_func( $send_callback, $message );
				$subject = call_user_func( $send_callback, $subject );
			} else if ( count($send_callback) == 2 ) {	
				$message = call_user_func( array( $send_callback[0], $send_callback[1] ), $message );
				$subject = call_user_func( array( $send_callback[0], $send_callback[1] ), $subject );
			}
		}

		//replace param tags 
		$message = str_replace( $from_tmp, $to_tmp, $message );
		$subject = str_replace( $from_tmp, $to_tmp, $subject );		
		
		if ( $content_type != 'text/plain' && strlen(trim($message)) > 0 ) {
			/*
			//insert inline styles
			require_once('o3_css2inline.php');

			$css2inline = new o3_css2inline();
			$css2inline->setHTML(o3_is_utf8($message) ? utf8_decode($message) : $message);
			$css2inline->setCSS($styles);		

			$css2inline->convert();
			*/
			
			$message = o3_email::css2inline( o3_is_utf8($message) ? utf8_decode($message) : $message, $styles );
		}
		
		if ( isset($this) && $this->is_test() ) {
			$message .= $content_type == 'text/plain' ? "\n\nTest email. Original to: ".$to : '<div style="padding: 20px; background: #CC0000; color: #FFFFFF;">Test email. Original to: '.$to.'</div>';
			$to = $this->test();
			$cc = '';
			$bcc = '';
		}
		
		//send email		
		return o3_send_mail( $to, $subject, $message, $from, $cc, $bcc, $files, $content_type );
   } 

   /** Insert inline styles in html 
   * @param strin $html
   * @param strin $css 
   * @return strin
   */
   public static function css2inline( $html, $css ) {

   		if ( strlen(trim($html)) > 0 ) {
	   		//insert inline styles
			require_once('o3_css2inline.php');

			$css2inline = new o3_css2inline();
			$css2inline->setHTML($html);
			$css2inline->setCSS($css);
			return $css2inline->convert();
		}

		return '';

   }
  
    /** array List of send callback functions */
    private $send_callbacks = array(); 
  
	/**
	 * Add custom send callback.
	 *
	 * The added function will be executed at email send.
	 *
	 * @param string|array $data String is name of the function. Array with 2 elements first is the object and second is the function.   
	 *
	 * @return void
	 */
	function add_send_callback( $data ) {
		$this->send_callbacks[] = $data; 
	}
	
	/**
	 * Get data from template file
	 *
	 * @param string $template String is name of the template file without extension.   
	 *
	 * @return array|boolean If the template not found false is returned
	 */
	function get( $template ) {
		$file = $this->template_dir.'/'.$template.'.json';
		if ( file_exists($file) ) {
			$content = json_decode(file_get_contents($file),true);
			if ( is_array($content) ) {
				return $content;
			}
		}
		return false;
	}
	
	/**
	 * Get list of all template in the tempalte folder
	 *
	 * @return array
	 */
	function get_all() {
		$return = array();
		$files = o3_read_path( $this->template_dir );
		foreach ( $files as $key => $value ) {
			if ( $value['file'] == 1 && $value['extension'] == 'json' ) {
				$data = $this->get( $value['filename'] );
				if ( $data === false ) 
					$data['index'] = $value['filename'];
				$return[] = $data;
			}
		}
		return $return;
	}
 
	/**
	 * Delete a template file
	 *
	 * @param string $index Name of tempalte to remove
	 * @return boolean
	 */
	function remove( $index ) {
		$file = $this->template_dir.'/'.$index.'.json';
		return o3_unlink( $file );
	}  
     
}


/**
 * O3 Engine email object for sending email
 *
 * Holds information for email sending
 *
 * @category module
 * @package o3_email
 */
class o3_email_data {
	
	/** string To email address */
	public $to = '';
	
	/** string Carbon copy to email address */
	public $cc = '';
	
	/** string Blind carbon copy to email address */
	public $bcc = '';
	
	/** string Email subject */
	public $subject = '';
	
	/** string Email inline style definitions */
	public $styles = '';
	
	/** string Email message */
	public $message = '';
	
	/** string Email content type. Like: text/html, text/plain Default value: text/html */
	public $content_type = 'text/html';
	
	/** string From email address */
	public $from = '';
	
	/**
	* array|o3_email_attachment Array with attachments or one attachment object. 
	* @see o3_email_attachment o3_email_attachment 
	*/
	public $files = array();
	
	/** array|string Funtion is run before the email is sent. The message is passed to the function and should return a string. String is name of the function. Array with 2 elements first is the object and second is the function.*/
	public $send_callback = '';
	
	/** string Name of the template. If is not empty string the message is replace with the tempalte text. */
	public $template = '';
	
	/** 
	* array Values to replace in the messsage. 
	* @example: example.php
	* //replace __name__ with John in the message before sending the email<br>
	* $o3_email_data->add_replace( '__name__', 'John' );
	*/
	public $replace = array();
	
	//files - can be 1 o3_email_attachment or array of o3_email_attachment 
	
	/**
	* Constructor
	*
	* @param string $to (optional) To email address
	* @param string $subject (optional) Email subject 
	* @param string $message (optional) Email message 
	* @param string $from (optional) From email address
	* @param string $cc (optional) Carbon copy to email address
	* @param string $bcc (optional) Blind carbon copy to email address
	* @param array|o3_email_attachment $files (optional) Array with attachments or one attachment object.
	* @param string $content_type (optional) Email content type. Like: text/html, text/plain Default value: text/html
	* @return void
	*/
	public function __construct( $to = '', $subject = '', $message = '', $from = '', $cc = '', $bcc = '', $styles = '', $files = array(), $content_type = 'text/html' ) {
  	$this->to = $to;
  	$this->cc = $cc;
  	$this->bcc = $bcc;
  	$this->subject = $subject;
  	$this->styles = $styles;
  	$this->message = $message;
  	$this->from = $from;
  	$this->files = is_array($files) ? $files :array( $files );
  	$this->content_type = $content_type;
  }
  
  /**
	* Add attachment to email
	*
	* @param string $path Path to the file to attach
	* @param string $filename (optional) Attachments filename in the email. If omitted or empty the real filename is used.
	* @return void
	*/
  function add_attachment( $path, $filename = '', $buffer = '' ) {
  	$this->files[] = new o3_email_attachment( $path, $filename, $buffer );
  }
  
  /**
	* Add replace values to email
	*
	* @see str_replace() str_replace()
	* 
	* @param string|array $from Replace from
	* @param string|array $to Replace with
	* @return void
	*/
  function add_replace( $from, $to ) {
  	$this->replace[] = array( $from, $to );
  }

}

/**
 * O3 Engine email object for email attachment
 *
 * Holds attachment information for email sending
 *
 * @category module
 * @package o3_email
 */
class o3_email_attachment {
	
	/** Path to the file to attach */
	public $path = ''; 
	
	/** Attachment's filename in the email */
	public $filename = '';

	/** Attachment's buffer */
	public $buffer = '';
	
	/**
	* Constructor
	*
	* @param string $path Path to the file to attach
	* @param string $filename (optional) Attachments filename in the email. If omitted or empty the real filename is used.
	* @return void
	*/
	public function __construct( $path, $filename = '', $buffer = '' ) {
  		$this->path = $path;
  		$this->filename = $filename == '' ? basename($path) : $filename;
  		$this->buffer = $buffer == '' ? '' : $buffer;
  		$this->load( $this->path );
  	}

	/**
	* Load attachment file
	*
	* @param string $path Path to the file to attach	
	* @return void
	*/
  	public function load( $path = null ) {
  		if ( $path != null ) 
  			$this->path = $path;
  		if ( file_exists($this->path) ) 	  		
  			return $this->buffer = file_get_contents($this->path);
  		return false;
  	}  
  
}

/**
* Send email
*
* The email is sent with UTF-8 encodeing.
*
* @param string $to To email address
* @param string $subject (optional) Email subject 
* @param string $message (optional) Email message 
* @param string/array $from (optional) From email address. If array then first element is from email second is the replay-to email.
* @param string $cc (optional) Carbon copy to email address
* @param string $bcc (optional) Blind carbon copy to email address
* @param array|o3_email_attachment $attachment (optional) Array with attachments or one attachment object.
* @param string $content_type (optional) Email content type. Like: text/html, text/plain Default value: text/html
* @return void
*/
function o3_send_mail( $to, $subject = '', $message = '', $from = '', $cc = '', $bcc = '', $attachment = array(), $content_type = 'text/html' ) {
	$headers = "MIME-Version: 1.0\r\n";
	
	//if attachment change content type
	if ( count($attachment) > 0 ) {
		$boundary = md5(date('r', time()));
		$headers .= "Content-type: multipart/mixed; boundary=\"_1_$boundary\"\r\n";
		$message_mixed = "--_1_$boundary
Content-Type: multipart/alternative; boundary=\"_2_$boundary\"

--_2_$boundary
Content-Type: ".$content_type."; charset=\"UTF-8\"
Content-Transfer-Encoding: 7bit

".o3_convert($message)."

--_2_$boundary--

";

		foreach ( $attachment as $key => $value ) {
$attachment = chunk_split(base64_encode($value->buffer));
$message_mixed .= "--_1_$boundary
Content-Type: application/octet-stream; name=\"".$value->filename."\" 
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

$attachment

";		
		}
		
		$message = $message_mixed."--_1_$boundary--";
		
	} else {
		$headers .= "Content-type: ".$content_type."; charset=UTF-8\r\n";
		$message = o3_convert($message);
	}
	
	//check if Reply-To to set
	if ( is_array($from) ) {
		if ( isset($from[0]) )
			$headers .= "From: ".$from[0]."\r\n";
		if ( isset($from[1]) )
			$headers .= "Reply-To: ".$from[1]."\r\n";
	} else {
		$headers .= "From: ".$from."\r\n";
	}


	if ( $cc != '' ) {
		$headers .= "Cc: ".$cc."\r\n";
	}
	if ( $bcc != '' ) {
		$headers .= "Bcc: ".$bcc."\r\n";
	}	 		
	$headers .= "X-Priority: 3\r\n";
	if ( isset($_SERVER['HTTP_HOST']) ) {
		$headers .= "X-Mailer: ".$_SERVER['HTTP_HOST']."\r\n";
	}

	return mail( $to, '=?UTF-8?B?'.base64_encode(o3_convert($subject)).'?=', $message, $headers ); 
}
 

?>