/**                                                                                                                                                                                                          
* O3 js library handling route changes                                                                                                                                                                       
*/                                                                                                                                                                                                           
                                                                                                                                                                                                             
/** set route change events */                                                                                                                                                                               
function o3_route(callb) {                                                                                                                                                                                   
	setInterval(function(){ check_o3_route(callb); }, 42 ); //24 fps                                                                                                                                           
}                                                                                                                                                                                                            
                                                                                                                                                                                                             
var o3_route_hash = null;
/** check for route change */                                                                                                                                                                       
function check_o3_route(callb) {                                                                                                                                                                             
	var hash = decodeURIComponent(window.location.hash.toString().substring(1));
	hash = hash == null ? '' : hash; 
	if ( o3_route_hash != hash ) {                                                                                                                                                                             
		o3_route_hash = hash;                                                                                                                                                                                    
		if ( callb != null )                                                                                                                                                                                     
			callb(o3_route_hash);                                                                                                                                                                                  
	};                                                                                                                                                                                                          
};                                                                                                                                                                                                            
                                                                                                                                                                                                             
/** set route */                                                                                                                                                                                             
function o3_route_set( hash, allow_callback ) {                                                                                                                                                                              
	hash = typeof hash == 'undefined' ? '' : hash;
	
	//do not trigger update event
	allow_callback = typeof allow_callback == 'undefined' ? true : allow_callback;
	if ( !allow_callback ) o3_route_hash = hash;
	
	var url = window.location.protocol+'//'+window.location.hostname+( window.location.port ? ':'+window.location.port : '' )+window.location.pathname+window.location.search+'#'+hash; 
	/*                                                                                                                                                                                                         
	if ( hash == '' && window.history && window.history.pushState ) {                                                                                                                                          
		var url = window.location.protocol+'//'+window.location.hostname+( window.location.port ? ':'+window.location.port : '' )+window.location.pathname+window.location.search+( hash != '' ? '#' : '' )+hash;
		window.history.pushState( null, document.title, url );                                                                                                                                                   
	} else {                                                                                                                                                                                                   
	*/                                                                                                                                                                                                         
		window.location = url;                                                                                                                                                                                   
	//}                                                                                                                                                                                                        
};                                                                                                                                                                                                            
                                                                                                                                                                                                             
/** get route */                                                                                                                                                                                             
function o3_route_get() {                                                                                                                                                                                    
	return o3_route_hash;                                                                                                                                                                                      
};                                                                                                                                                                                                            
     
/**route back count*/                                   
o3_route_back_count = 0;

/**route back count timer*/
o3_route_back_timer = null;
                                                                                                                                                         
/** goes back in route history if it possible */                                                                                                                                                             
function o3_route_back() {
	clearTimeout( o3_route_back_timer );	
	o3_route_back_count--;
	o3_route_back_timer =	setTimeout(function() {	window.history.go(o3_route_back_count); o3_route_back_count = 0; }, 50 );
};