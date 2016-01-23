/**
*
* O3 tool tip
*
* @package o3
* @link    todo: https://github.com/zoli-fischer/o3/wiki
* @author  Zotlan Fischer <zoli_fischer@yahoo.com>
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/

o3_tooltip_class = function( opts ) {

	var t = this;	

	//options
	t.opts = jQuery.extend({ 
						enabled: true,
						cursor: {
							offsetX: 12, //Customize x offset of tooltip
							offsetY: 14 //Customize y offset of tooltip
						},
						track: true,
						refattr: 'data-tooltip',
						init: ''
					  }, opts );
	
	//tooltip container
	t.$container = null;
	t.container = null;

	//private - show/hide status
	t.visible = false;
	
	//enabled/disable tooltip
	t.enabled = t.opts.enabled;

	//change tooltip offset from cursor
	t.cursor = t.opts.cursor;

	//Check mouse over target element for tooltip
	t.check_tooltip = function( evt ){			
		if ( t.enabled ) {			
			var evt = (window.event) ? window.event : evt,
				target = evt.target,
				$target = null,
				refattr_val = '',
				found = false; //check if ttfound

			//check target
			var i = 0;
			while ( target = i == 0 ? target : target.parentNode ) {					
				if ( target && ( $target = jQuery(target) ) && ( refattr_val = $target.attr(t.opts.refattr) ) && jQuery.trim(refattr_val).length > 0 ) {					

					if ( t.opts.track ) {

						var left = 0,
							top = 0,
							$htmlbody = jQuery('html,body'),
							$body = jQuery('body'),
							curX = evt.pageX ? evt.pageX : evt.clientX + $htmlbody.scrollLeft(),
							curY = evt.pageY ? evt.pageY : evt.clientY + $htmlbody.scrollTop(),
							//how close the mouse is to the corner of the window
							winwidth = document.all && !window.opera ? $body.clientWidth : ( window.innerWidth - 20 ),
							winheight = document.all && !window.opera ? $body.clientHeight : ( window.innerHeight - 20 ),
							leftedge = ( t.cursor.offsetX < 0 ) ? -t.cursor.offsetX : -1000,						
							rightedge = document.all && !window.opera ? winwidth - evt.clientX-t.cursor.offsetX : ( winwidth - evt.clientX-t.cursor.offsetX ),
							bottomedge=document.all && !window.opera ? winheight - evt.clientY-t.cursor.offsetY : ( winheight - evt.clientY-t.cursor.offsetY  );

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
							left = curX+t.cursor.offsetX+"px";
						};
						
						//same concept with the vertical position
						if (bottomedge<t.container.offsetHeight){
							top = curY-t.container.offsetHeight-t.cursor.offsetY+"px";			
						}else{
							top = curY+t.cursor.offsetY+"px";
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
	
	//tooltip text
	t.text = '';

	//show tooltip	
	t.showtt = function( text, left, top ) {
		if ( !t.visible ) {
			t.text = text;
			t.$container.css( { left: left, top: top } ).html( text ).addClass('o3_tooltip_show');
			t.visible = true;
		} else if ( t.text != text ) {
			t.text = text;
			t.$container.html( text );
		};
	};

	//constructor
	t.init = function() {

	   	//create cotainer
	  	t.$container = jQuery('<div class="o3_tooltip"></div>').appendTo('body');	
	  	t.container = t.$container.get(0);

	  	//add event check
	  	jQuery(document).mousemove( t.check_tooltip ).mousedown( t.hidett ); 	   	
	  	
	};	

	if ( t.opts.init == 'onload' ) { 
		jQuery(document).ready(function () {	
			t.init();
		});
	} else {
		t.init();
	};

};

if ( typeof jQuery != 'undefined' ) {
	//if jquery loaded init o3_tooltip
	window.o3_tooltip = new o3_tooltip_class( {  init: 'onload' } );
} else {
	//if jQuery loaded after this
	function o3_tooltip_class_init() {
		window.o3_tooltip = new o3_tooltip_class();
	};
	//add init on document load
	window.attachEvent ? window.attachEvent( 'onload', o3_tooltip_class_init ) : window.addEventListener( 'load', o3_tooltip_class_init, false );
};
