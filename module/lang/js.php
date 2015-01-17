<?php

/**
 * Content language module for O3 Engine
 *
 * @package o3
 * @link    todo: add url
 * @author  Zotlan Fischer <zoli_fischer@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if ( !defined('O3') )
	define('O3','o3');
 
error_reporting(0);

if ( !defined('O3_DIR') )
	/** The root of your O3 installation */
	define("O3_DIR", str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))).'/../..');

require_once(O3_DIR.'/config.php'); //load o3 config and custom config
require_once(str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))).'/config.php'); //load lang module config 
require_once(O3_INC_DIR.'/header.php');
require_once(O3_INC_DIR.'/func.php');
require_once(O3_INC_DIR.'/str.php');

o3_header_encoding( 'application/x-javascript', '' );

//set cache
$expires = 1800; //0.5 hours
//header('Content-Length: ' . ob_get_length());
header('Cache-Control: max-age='.$expires.', must-revalidate');
header('Pragma: public');
header('Expires: '. gmdate('D, d M Y H:i:s', time()+$expires).'GMT');

$script_url = isset($_SERVER['HTTPS']) ? 'https' : 'http'.'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];

$ls = explode(',',o3_get('ls','') ); //load languages, comma separated string
$l = o3_get('l',''); //current languge
$c = o3_get('c',''); //collection
$update_onload = o3_get('update_onload',1);

//todo increase onload performance
/* 
//uncompressed js
if ( typeof o3_lang  == 'undefined' ) { 
	function o3_lang_handler() {
		var t = this;
		t.nowed = false;
		t.l = {}; //language list
		t.selected = ''; //selected language
	}; 

	//set selected 
	//update - boolean - update data-lang
	o3_lang_handler.prototype.select = function( language, update ) {
		if ( this.selected != language ) {
			update = typeof update === 'undefined' ? true : update;
			this.selected = language;		
			if ( update ) {
				if ( document.readyState === "complete" ) {
					this.update();
				} else {					
					if ( window.attachEvent ) {
						window.attachEvent( 'onload', function(){ o3_lang.now() } );
					} else {
						window.addEventListener( 'load', function(){ o3_lang.now() } );
					}
				}
			}
		}
	};
	
	//get text values
	o3_lang_handler.prototype.get_values = function( index, language ) {
		if ( typeof this.l[language] != 'undefined' &&
		 		 typeof this.l[language].list[index] != 'undefined'
			 ) {
			return this.l[language].list[index].values;
		}
		return []; 
	};
	
	//get text by index
	//index of text
	//language - if not set selected language used
	o3_lang_handler.prototype._ = function( index, language ) {
		language = typeof(language) != 'undefined' ? language : this.selected;
		var values = this.get_values( index, language );
		return typeof values[0] !== 'undefined' ? values[0] : index;
	};
	
	//get plural text by index
	//index of text
	//n - float 
	//language - if not set selected language used
	o3_lang_handler.prototype.n_ = function( index, n, language ) {
		language = typeof(language) != 'undefined' ? language : this.selected;
		var values = this.get_values( index, language ),
				nindex = this.get_nindex( n, language );
		return typeof values[nindex] ? values[nindex] : "";
	};
	
	//get plural index for the selected n value for language
	//n - float 
	//language - if not set selected language used
	o3_lang_handler.prototype.get_nindex = function( n, language ) {
		language = typeof(language) != 'undefined' ? language : this.selected;
		if ( typeof this.l[language] != 'undefined' ) {
			n = parseFloat(n);
			eval("n = "+this.l[language].settings.plural_rule+";"); //todo secure
			return n;
		}
		return 0;
	};
		
	//add a langugage
	o3_lang_handler.prototype.add_language = function( index, settings ) {
		if ( typeof this.l[index] == 'undefined' )
			this.l[index] = { settings: settings, list: {} }; 				
	};
		
	//add a index
	o3_lang_handler.prototype.add_index = function( index, values, language, is_plural ) {
		if ( typeof this.l[language] == 'undefined' )
			this.add_language( language, {} ); //add language if not in the list			
		this.l[language].list[index] = { values: values, is_plural: is_plural };
	};
	
	//replace now data-lang	
	o3_lang_handler.prototype.now = function() {
		if ( !this.nowed )
			this.update();
	};
	
	//update data-lang
	o3_lang_handler.prototype.update = function() {		
		if ( typeof jQuery !== 'undefined' ) {
			var t = this;
			t.nowed = true;
			jQuery(t).trigger( jQuery.Event( "o3_lang_before_update", { language: t.selected } ) ); //event
			jQuery('*[data-lang]').each( function() {
				//todo: add function and encoding support				
				eval('var obj = '+jQuery(this).attr('data-lang')+';');
				var escape_html = typeof(obj.escape_html) != 'undefined' ? obj.escape_html : true;
				jQuery(t).trigger( jQuery.Event( "o3_lang_each_update", { language: t.selected, data: obj, escape_html: false } ) ); //event 
				if ( typeof(obj.value) != 'undefined' ) { 						
					jQuery(this).val( escape_html ? t.escp_html(t._( obj.value )) : t._( obj.value ) ); 
				}
				if ( typeof(obj.html) != 'undefined' ) {
					jQuery(this).html( escape_html ? t.escp_html(t._( obj.html )) : t._( obj.html ) );
				}
				if ( typeof(obj.attr) != 'undefined' ) {
					for (var key in obj.attr)
				    if (obj.attr.hasOwnProperty(key) && typeof obj.attr[key] == 'string' )
				    	obj.attr[key] = escape_html ? t.escp_html(t._( obj.attr[key] )) : t._( obj.attr[key] );				    
					jQuery(this).attr( obj.attr );
				}
				if ( typeof(obj.css) != 'undefined' ) {
					for (var key in obj.css)
				    if (obj.css.hasOwnProperty(key) && typeof obj.css[key] == 'string' )
				    	obj.css[key] = escape_html ? t.escp_html(t._( obj.css[key] )) : t._( obj.css[key] );		
					jQuery(this).css( obj.css );
				}
			});
			jQuery(t).trigger( jQuery.Event( "o3_lang_after_update", { language: t.selected } ) ); //event
		} else {
			alert('jQuery missing!');
		}
	};
	
	//escape chars to html entityes
	o3_lang_handler.prototype.escp_html = function( s ) {
		return String(s).replace(/[<>&"']/g, function (s) { return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[s]; });
	};
	
	var o3_lang = new o3_lang_handler();
};
*/
?>
if(typeof o3_lang=='undefined'){function o3_lang_handler(){var t=this;t.nowed=false;t.l={};t.selected=''};o3_lang_handler.prototype.select=function(language,update){if(this.selected!=language){update=typeof update==='undefined'?true:update;this.selected=language;if(update){if(document.readyState==="complete"){this.update()}else{if(window.attachEvent){window.attachEvent('onload',function(){o3_lang.now()})}else{window.addEventListener('load',function(){o3_lang.now()})}}}}};o3_lang_handler.prototype.get_values=function(index,language){if(typeof this.l[language]!='undefined'&&typeof this.l[language].list[index]!='undefined'){return this.l[language].list[index].values}return[]};o3_lang_handler.prototype._=function(index,language){language=typeof(language)!='undefined'?language:this.selected;var values=this.get_values(index,language);return typeof values[0]!=='undefined'?values[0]:index};o3_lang_handler.prototype.n_=function(index,n,language){language=typeof(language)!='undefined'?language:this.selected;var values=this.get_values(index,language),nindex=this.get_nindex(n,language);return typeof values[nindex]?values[nindex]:""};o3_lang_handler.prototype.get_nindex=function(n,language){language=typeof(language)!='undefined'?language:this.selected;if(typeof this.l[language]!='undefined'){n=parseFloat(n);eval("n = "+this.l[language].settings.plural_rule+";");return n}return 0};o3_lang_handler.prototype.add_language=function(index,settings){if(typeof this.l[index]=='undefined')this.l[index]={settings:settings,list:{}}};o3_lang_handler.prototype.add_index=function(index,values,language,is_plural){if(typeof this.l[language]=='undefined')this.add_language(language,{});this.l[language].list[index]={values:values,is_plural:is_plural}};o3_lang_handler.prototype.now=function(){if(!this.nowed)this.update()};o3_lang_handler.prototype.update=function(){if(typeof jQuery!=='undefined'){var t=this;t.nowed=true;jQuery(t).trigger(jQuery.Event("o3_lang_before_update",{language:t.selected}));jQuery('*[data-lang]').each(function(){eval('var obj = '+jQuery(this).attr('data-lang')+';');var escape_html=typeof(obj.escape_html)!='undefined'?obj.escape_html:true;jQuery(t).trigger(jQuery.Event("o3_lang_each_update",{language:t.selected,data:obj,escape_html:false}));if(typeof(obj.value)!='undefined'){jQuery(this).val(escape_html?t.escp_html(t._(obj.value)):t._(obj.value))}if(typeof(obj.html)!='undefined'){jQuery(this).html(escape_html?t.escp_html(t._(obj.html)):t._(obj.html))}if(typeof(obj.attr)!='undefined'){for(var key in obj.attr)if(obj.attr.hasOwnProperty(key)&&typeof obj.attr[key]=='string')obj.attr[key]=escape_html?t.escp_html(t._(obj.attr[key])):t._(obj.attr[key]);jQuery(this).attr(obj.attr)}if(typeof(obj.css)!='undefined'){for(var key in obj.css)if(obj.css.hasOwnProperty(key)&&typeof obj.css[key]=='string')obj.css[key]=escape_html?t.escp_html(t._(obj.css[key])):t._(obj.css[key]);jQuery(this).css(obj.css)}});jQuery(t).trigger(jQuery.Event("o3_lang_after_update",{language:t.selected}))}else{alert('jQuery missing!')}};o3_lang_handler.prototype.escp_html=function(s){return String(s).replace(/[<>&"']/g,function(s){return{"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[s]})};var o3_lang=new o3_lang_handler()};
<?php

require_once(__DIR__.'/o3_lang.php');	
$o3_lang = new o3_lang();
$o3_lang->set_dir( O3_LANG_DIR );

if ( count($ls) > 0 ) {
	$rnd_func = 'o3_l'.substr(md5(time()),0,6); //function to load
	echo 'function '.$rnd_func.'() { var o = o3_lang;';
	foreach ( $ls as $key => $value ) {
		$o3_lang->current( $value );  //set language
		$o3_lang->load( $c );	// load collection
		if ( isset($o3_lang->list[$value]) && count($o3_lang->list[$value]) > 0 ) {
			$p_rule_data = $o3_lang->get_plural_rule();
			echo 'o.add_language(\''.addslashes($value).'\', { plural_form: '.( intval($p_rule_data['form']) ).', plural_rule: \''.addslashes($p_rule_data['rule']).'\' } );';
			foreach ( $o3_lang->list[$value] as $key_lang => $value_lang ) {		
				foreach ( $value_lang as $key_ => $value_ ) {					
					//generate array
					$value_array = array();
					foreach ( $value_ as $key__ => $value__ ) {			
						if ( $key__ !== 'plural' )
							$value_array[$key__] = '\''.str_replace( array("\n","\r"),  array( '\n', '\r' ), addslashes( $value__ ) ).'\'';
					} 
					echo 'o.add_index(\''.addslashes($key_).'\',['.implode(',',$value_array).'],\''.addslashes($value).'\','.( isset($value_['plural']) && $value_['plural'] == 1 ? 'true' : 'false' ).');'; //insert indexes
				}
			}
		}
		if ( $l == $value && trim($l) != '' ) {
			echo 'o.select("'.addslashes($l).'",'.( $update_onload == 0 ? 'false' : 'true' ).');';
		}
	}
	echo '}; '.$rnd_func.'();';
}



?>