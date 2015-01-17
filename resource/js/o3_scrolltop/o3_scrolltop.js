/**
*
* O3 scroll to page top
*
* Options:
* 	topOffset integer Number of pixels needed to be scrolled from document top before the button apears. Default: 100
*   bottomOffset integer Number of pixels before the scroll reaches the end of the document to hide the button. Default: 0
*
* @author Zoltan Fischer
*/
o3_scrolltop = function( opts ) {
	
	var t = this;
	$ = jQuery;
	
	//options
	t.opts = $.extend({ topOffset: 100,
											bottomOffset: 0,										
											className: 'o3_scrolltop'
											//,topBoundary: 
											}, opts );		

	//button jQuery obj
	t.$btn = null;

	t.check_position = function() {
		var top = $(window).scrollTop();		
	
		if ( t.opts.topOffset == 0 || ( top > t.opts.topOffset &&
				 ( t.opts.bottomOffset == 0 || ( $(document).height() - ( $(window).height() + top ) ) > t.opts.bottomOffset ) ) ) {
			if ( !t.$btn.hasClass('o3_scrolltop_show') )
      	t.$btn.addClass('o3_scrolltop_show');
  	} else {
  		if ( t.$btn.hasClass('o3_scrolltop_show') )
      	t.$btn.removeClass('o3_scrolltop_show');
		}	
	};
	
	//constructor
	t.init = function() {
	   		
	  //create button		
	  t.$btn = $('<div class="'+t.opts.className+'"><span>^</span></div>').appendTo('body').click( function() { $("html, body").animate({ scrollTop: 0 }, "slow"); } );
	   		
	  $(window).scroll(function(){t.check_position()}); //check on scroll event 		
	  $(window).resize(function(){t.check_position()}); //check on resize
		
	  //check on touch
	  if ( typeof window.addEventListener == 'function' )
		window.addEventListener( "touchmove", function() { t.check_position() }, false );
	   
	};
	t.init();
	
	
	
			
	
};


/**
* jQuery function, not chainable
*
* @return o3_scrolltop object
*/
jQuery.o3_scrolltop = function( opts ) {		
	//create o3 scrolltop
	return new o3_scrolltop( opts );
};