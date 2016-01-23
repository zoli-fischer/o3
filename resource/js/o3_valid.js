/**
* Check if string is a valid email address
* @param string str String to check
* @return boolean True if the string is a valid email adress
*/
function o3_valid_email( str ) {
	return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test( str );
};

/**
* Check if string is a valid password
* @param string str String to check
* @param string int Minimum length
* @param string int Maximum length
* @param string str Special chars allowed
* @return boolean True if the string is a valid password
*/
function o3_valid_password( str, min, max, special_chars ) {
	min = typeof(min) != 'undefined' ? min : 8;
	max = typeof(max) != 'undefined' ? max : 32;
	special_chars = typeof(special_chars) != 'undefined' ? special_chars : "-+=?!*:;.,@#$^%&";

	var min_ = Math.min( min, max ),
		max_ = Math.max( min, max );
	if ( min_ < 1 || max_ < 1 )
		return false;
	
	var reg = new RegExp( '^[a-zA-Z0-9'+special_chars+']{'+min_+','+max_+'}$' );

	return reg.test( str );///^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test( str );
};

/**
* Check if string is a valid number
* @param string str String to check
* @return boolean True if the string is a valid number
*/
function o3_valid_number( str ) {
	return ( str != null && str.toString().replace(/^\s+|\s+$/gm,'').length > 0 && str.toString().match(/^-?\d*?$/) );
};

/**
* Check if string is a valid float number
* @param string str String to check
* @return boolean True if the string is a valid float number
*/
function o3_valid_float( str ) {
	return ( str != null && str.toString().replace(/^\s+|\s+$/gm,'').length > 0 && str.toString().replace(',','.').match(/^-?\d*(\.\d+)?$/) );
};

/**
* Check if string is a valid url
* @param string str String to check
* @return boolean True if the string is a valid url
*/
function o3_valid_url( str ) {
	return /\b(https?|ftp|file):\/\/[\-A-Za-z0-9+&@#\/%?=~_|!:,.;]*[\-A-Za-z0-9+&@#\/%=~_|‌​]/.test( str );
};