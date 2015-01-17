/**
*
* O3 tool tip
*
* @author Zoltan Fischer
*/

o3_tooltip_class = function( opts ) {

	var t = this;
	$ = jQuery;

	//options
	t.opts = $.extend({ 
						enabled: true,
						cursor: {
							offsetX: 12, //Customize x offset of tooltip
							offsetY: 14 //Customize y offset of tooltip
						},
						track: true,
						refattr: 'data-tooltip'
					  }, opts );
	
	//popup container
	t.$container = null;
	t.container = null;

	//show status
	t.visible = false;

	//Check mouse over target element for tooltip
	t.check_tooltip = function( evt ){			
		if ( t.opts.enabled ) {			
			var evt = (window.event) ? window.event : evt,
				target = evt.target,
				$target = null,
				refattr_val = '',
				found = false; //check if ttfound

			//check target
			var i = 0;
			while ( target = i == 0 ? target : target.parentNode ) {					
				if ( target && ( $target = $(target) ) && ( refattr_val = $target.attr(t.opts.refattr) ) && $.trim(refattr_val).length > 0 ) {					

					if ( t.opts.track ) {

						var left = 0,
							top = 0,
							$htmlbody = $('html,body'),
							$body = $('body'),
							curX = evt.pageX ? evt.pageX : evt.clientX + $htmlbody.scrollLeft(),
							curY = evt.pageY ? evt.pageY : evt.clientY + $htmlbody.scrollTop(),
							//how close the mouse is to the corner of the window
							winwidth = document.all && !window.opera ? $body.clientWidth : ( window.innerWidth - 20 ),
							winheight = document.all && !window.opera ? $body.clientHeight : ( window.innerHeight - 20 ),
							leftedge = ( t.opts.cursor.offsetX < 0 ) ? -t.opts.cursor.offsetX : -1000,						
							rightedge = document.all && !window.opera ? winwidth - evt.clientX-t.opts.cursor.offsetX : ( winwidth - evt.clientX-t.opts.cursor.offsetX ),
							bottomedge=document.all && !window.opera ? winheight - evt.clientY-t.opts.cursor.offsetY : ( winheight - evt.clientY-t.opts.cursor.offsetY  );

						//show tt
						t.showtt( refattr_val, left, top );

						//if the horizontal distance isn't enough to accomodate the width of the context menu
						if ( rightedge < t.container.offsetWidth ) {
							//move the horizontal position of the menu to the left by it's width
							left = curX-t.container.offsetWidth+"px";			
						} else if (curX<leftedge) {
							left = "5px";
						} else {
							//position the horizontal position of the menu where the mouse is positioned
							left = curX+t.opts.cursor.offsetX+"px";
						};
						
						//same concept with the vertical position
						if (bottomedge<t.container.offsetHeight){
							top = curY-t.container.offsetHeight-t.opts.cursor.offsetY+"px";			
						}else{
							top = curY+t.opts.cursor.offsetY+"px";
						};

						//set tt pos
						t.$container.css( { left: left, top: top } );			

					};
					found = true;
					break;
				};
				i++;
			};

			//if no element hide tt
			if ( !found )
				t.hidett();			

		};
	};	

	//hide tooltip
	t.hidett = function() {
		if ( t.visible ) {
			t.$container.removeClass('o3_tooltip_show');
			t.visible = false;
		};
	};

	//show tooltip
	t.showtt = function( text, left, top ) {
		if ( !t.visible ) {
			t.$container.css( { left: left, top: top } ).html( text ).addClass('o3_tooltip_show');
			t.visible = true;
		};
	};

	//constructor
	t.init = function() {

	   	//create cotainer
	  	t.$container = $('<div class="o3_tooltip"></div>').appendTo('body');	
	  	t.container = t.$container.get(0);

	  	//add event check
	  	$(document).mousemove( t.check_tooltip ); 
		$(document).mousedown( t.hidett ); 
	   	
	  	
	};	

	jQuery(document).ready(function () {	
		t.init();
	});

};
window.o3_tooltip = new o3_tooltip_class();