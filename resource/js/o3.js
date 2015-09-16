//define consol if not exists
if ( typeof console == 'undefined' ) console = { log: function(str) {} };

//shortcut to o3_lang._, checks if o3_lang is defined
function o3_lang_( value, language ) {
  return typeof o3_lang == 'undefined' ? value : o3_lang._( value, language );
}

//shortcut to o3_lang.n_, checks if o3_lang is defined
function o3_langn_( value, nr, language ) {
  return typeof o3_lang == 'undefined' ? value : o3_lang.n_( value, nr, language );
}

//same as jQuery.ajax with O3 debug tool
function o3_ajax( url, settings ) { //todo optimize
  if ( typeof settings != 'undefined' ) {
    var success = settings.success  ? settings.success : function() {},
        error = settings.error ? settings.error : function() {};
    console.log('AJAX: '+url);
    settings.success = function (data) { if ( typeof data.o3_console != 'undefined' && typeof data.o3_console != 'undefined' ) for ( var i = 0; i < data.o3_console.length; i++ ) console.log(data.o3_console[i]); success(data); };
    settings.error = function (jqXHR, status, error_thrown) { 
      console.log('AJAX Status: '+status);
      if ( status == 'parsererror' ) console.log('AJAX Response: '+jqXHR.responseText);
      console.log('AJAX Error: '+error_thrown);
      error(jqXHR, status, error_thrown); 
    };
    return jQuery.ajax( url, settings );
  } else {
    console.log('AJAX: '+url.url);
    var success = url.success  ? url.success : function() {},
        error = url.error ? url.error : function() {};
    url.success = function (data) { if ( typeof data.o3_console != 'undefined' && typeof data.o3_console != 'undefined' ) for ( var i = 0; i < data.o3_console.length; i++ ) console.log(data.o3_console[i]); success(data); };
    url.error = function (jqXHR, status, error_thrown) { 
      console.log('AJAX Status: '+status);
      if ( status == 'parsererror' ) console.log('AJAX Response: '+jqXHR.responseText);
      console.log('AJAX Error: '+error_thrown);
      error(jqXHR, status, error_thrown); 
    };
    return jQuery.ajax( url );
  } 
}

/*
* Create ajax handler
* @param url Request Url
* @param data Data to send
* @param onSuccess On success callback
* @param onError On error callback
* @param onFail On fail callback
*/
function o3_ajax_call( url, data, onSuccess, onError, onFail ) {
  return o3_ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
             
          if ( data && typeof data.redirect != 'undefined' && data.redirect != '' )
            window.location = data.redirect;

          if ( data && data.success === true ) {
            if ( onSuccess ) {
              if ( typeof onSuccess == 'function')
                onSuccess( data );
            } else {
               if ( data.success_msg ) 
                 alert( o3_lang_(data.success_msg) );
            }  
          } else {
            if ( onError ) {         
              if ( typeof onError == 'function') 
                onError( data );
            } else {
               if ( data.error_msg ) 
                 alert( o3_lang_(data.error_msg) );
            }
          }         
          
        },
        error: function (jqXHR, status, error) {          
          if ( onFail ) {
            if ( typeof onFail == 'function')
              onFail( jqXHR, status, error );
          } else {
            alert( o3_lang_(O3_ERR_GENERAL) );
          }
        }
    });
};

//create script
function o3_write_script(url){ 
  document.write('<script src="'+ url + '" type="text/javascript"></script>'); 
}

//dynamic load javascript
function o3_load_script( url, async, onload ) {
	async = typeof async == 'undefined' ? true : async;
	var s = document.createElement('script');
      s.type = 'text/javascript';
      s.src = url;
      s.onload = onload;
      var x = document.getElementsByTagName('script')[0];
      x.parentNode.insertBefore(s, x);
}

//alias for o3_load_script
function o3_script( url, async ) {
  o3_load_script( url, async );
};

//check for condition and runc function when is true
function o3_trigger( func, cond, delay ) {  
  var t = this;

  delay = typeof delay == 'undefined' ? 100 : delay;

  t.interval = setInterval( function() {    
    if ( cond() ) {
      clearInterval(t.interval);
      func();
    };
  }, delay );
};

//local storage & cookies

//set value and expiration of cookie
function o3_set_cookie( name, value, seconds ) {  
  var expires = "";
  if ( typeof seconds != 'undefined' ) {
      var date = new Date();
      date.setTime(date.getTime() + (seconds * 1000));
      expires = "; expires=" + date.toGMTString();
  };
  document.cookie = name + "=" + value + expires + "; path=/";
};

//unset cookie by name
function o3_unset_cookie( name ) {  
  var expires = "",
      value = "",
      seconds = -3600;  
  var date = new Date();
  date.setTime(date.getTime() + (seconds * 1000));
  expires = "; expires=" + date.toGMTString();  
  document.cookie = name + "=" + value + expires + "; path=/";
};

//get value of cookie
function o3_get_cookie( name ) {
    var name = name + "=", 
        cookies = document.cookie.split(';');
    for( var i = 0; i < cookies.length; i++ ) {
        var c = cookies[i];
        while ( c.charAt(0) == ' ' ) 
          c = c.substring(1);
        if ( c.indexOf(name) != -1 ) 
          return c.substring( name.length, c.length );
    }
    return "";
};

/*
* Check if html5 storage available
*/
function o3_is_html5_storage() {
  try {
    return 'localStorage' in window && window['localStorage'] !== null;
  } catch (e) {
    return false;
  };
};

/* 
* Get or set 
*/
function o3_html5_store( index, value ) {
  if ( o3_is_html5_storage() ) {    
    if ( typeof value != 'undefined' ) {      
      return localStorage.setItem( index, value.toString() );
    } else {
      return localStorage.getItem( index ); 
    };
  };  
};


//array functions

/*
* Update elements in array
*/
function o3_array_update( arr, func, data ) {
  if ( typeof func == 'function' ) 
    for ( prop in arr ) {    
      if ( func(arr[prop]) ) 
        arr[prop] = data;
    };
};
/*
if (!('o3_array_update' in Array.prototype))
  Array.prototype.o3_array_update = function( func, data ) {
    o3_array_update( this, func, data );
  };
*/

/*
* Remove elements from array
*/
function o3_array_remove( arr, func ) {
  if ( typeof func == 'function' ) 
    for ( prop in arr ) {    
      if ( func(arr[prop]) ) {
        var index = arr.indexOf( arr[prop] );
        if ( index > -1 ) 
          arr.splice( index, 1 );      
      };
    };
};

//file/url functions

/*
* Add parameter(s) to url
*/
function o3_param2url( url, param ){
    if ( typeof url == 'string' ) {
      url += (url.split('?')[1] ? '&':'?') + param;
      return url;
    };
    return false;
};

/*
* Get basename from path
*/
function o3_basename( path ) {
  return path.split(/[\\/]/).pop();
};
