/**
*
* O3 popup for inline popups
*
* Options:
*
* @author Zoltan Fischer
*/

var o3_popup_zindex = 5000,
	o3_popup_zindex_ref = o3_popup_zindex;
o3_popup = function( opts ) {

	var t = this;

	//options
	t.opts = jQuery.extend({ width: '400px',
						height: '300px',
						className: '',
						showOverlay: true,
						overlayColor: '#000000',
						overlayOpacity: 0.3,
						animate: true,
						showAnimation: 'scale',
						hideAnimation: 'scale',
						hideAnimationTime: 220,
						fadeInSpeed: 0, //popup fade in anim. speed
						fadeOutSpeed: 0, //popup fade out anim. speed						
						header: {},
						body: {},
						footer: {},
						clearOnClose: true, //set empty content on close
						closeOnEsc: true, //close on pressing esc
						disabled: false, //disable pop		
						dragable: true, //allow draging
						swffix: false, //set in front of swf, no drag, animation available
						//events
						onafterclose: null,
						onbeforeclose: null,
						onafterload: null,
						onbeforeload: null,
						onsubmit: null	
					  }, opts );

	t.opts.header = jQuery.extend({ visible: true, //is visible
							   title: '', //the title of the popup
							   showCloseButton: true, //show close button
							   height: '40px' //header height 
							 }, opts.header );


	t.opts.footer = jQuery.extend({ visible: true, //is visible
							   height: '60px', //footer height 
							   content: [] //list of items in the footer
							 }, opts.footer );

	t.opts.body = jQuery.extend({ type: 'html', //html, url
							 src: '' // type html - html code, type url - load content from url
						   }, opts.body );
	
	//popup container
	t.$container = null;
	
	//overlay container
	t.$overlay = null;
	
	//outer container
	t.$container_outer = null;

	//header 
	t.$header = null;
	t.$header_title = null;
	t.$header_close = null;

	//footer
	t.$footer = null;

	//footer contents
	t.footer_contents = new Array();

	//container custom classname
	t.className = t.opts.className;

	//visibility flag
	t.visible = false;

	//zindex of pop
	t.zindex = o3_popup_zindex;

	//disabled flag
	t.disabled = t.opts.disabled;

	//on what window width (pixels) should show in mobile view
	t.mobileWndWidthLimit = 640;

	//jquery obj to focus
	t.$focus_obj = null;

	//on after close
	t.onafterclose = t.opts.onafterclose;

	//on before close
	t.onbeforeclose = t.opts.onbeforeclose;

	//on before load url
	t.onafterload = t.opts.onafterload;
	
	//on before load url
	t.onbeforeload = t.opts.onbeforeload;

	//on submit event
	t.onsubmit = t.opts.onsubmit;
	
	//dragable
	t.swffix = t.opts.swffix;

	//animation allowed
	t.animate = t.opts.animate && !t.swffix;

	//dragable
	t.dragable = t.opts.dragable && !t.swffix;

	//to show overlay
	t.showOverlay = t.opts.showOverlay;

	//drag event initialized
	t.is_drag_init = false;

	//is object was moved from default position
	t.is_draged = false;

	//container offset, used at dragging
	t.coffset = { left: 0, top: 0 };

	//container margin
	t.marginTop = 0;
	t.marginLeft = 0;
	t.width = -1;
	t.height = -1;	

	//handle window resize
	t.handle_wnd_resize = function() {	
		if ( t.visible ) { //only update if visible
			var height = '100%',
	  			width = '100%',
	  			left = '0px',
	  			top = '0px',
	  			wnd_height = window.innerHeight ? window.innerHeight : jQuery(window).height(),
	  			wnd_width = window.innerWidth ? window.innerWidth : jQuery(window).width();

	  		t.width = t.width == -1 ? t.$container.outerWidth() : t.width;
	  		t.height = t.height == -1 ? t.$container.outerHeight() : t.height;

			if ( wnd_width > t.mobileWndWidthLimit ) {
				height = wnd_height < t.height ? wnd_height : t.height;			
	  			width = wnd_width < t.width ? wnd_width : t.width;	

	  			//if popup was draged ignore margins
	  			if ( !t.is_draged ) {
		  			t.marginTop = -(height/2); 
			  		t.marginLeft = -(width/2);
			  		left = '50%';
	  				top = '50%';	  				
	  			};	  			
 
			};
			
			var css = { 	
	  			height: height, 
	  			width: width,
	  			marginTop: t.marginTop,
	  			marginLeft: t.marginLeft,
	  			left: left, 
				top: top				 
		  	};
			t.$container.css( css );
			
			//resize body
		  	t.$body.css( { width: width,
	  		               height: t.$container.height() 
	  		               		   - ( t.opts.footer.visible ? t.$footer.height() : 0 ) 
	  		               		   - ( t.opts.header.visible ? t.$header.height() : 0 ) - 2,
	  		               top: t.$header.height() + 1 } );					

		  	//init drag depending on resolution
		  	t.init_drag();

		  	//fix. position fixed width/height
		  	if ( !t.swffix ) {
		  		t.$overlay.css( { 'width': wnd_width, 'height': wnd_height } );
		  	} else {		  		
		  		t.$overlay.css( { 		  			
		  			'left': ( ( wnd_width - width ) / 2 )+'px', 
		  			'top': ( ( wnd_height - height ) / 2 )+'px',
		  			'width': width+'px',
		  			'height': height+'px'
		  		} );
		  	}
		  	t.$container_outer.css( { 'width': wnd_width, 'height': wnd_height } );


		};
	};

	t.showAnimationClass = function( value ) {
	 	value = typeof value != 'undefined' ? value : false;
		return 'o3_popup_container'+( value ? '_do' : '' )+'_'+t.opts.showAnimation+'in';		
	};

	t.hideAnimationClass = function( value ) {
	 	value = typeof value != 'undefined' ? value : false;
		return 'o3_popup_container'+( value ? '_do' : '' )+'_'+t.opts.hideAnimation+'out';
	};

	//constructor
	t.init = function() {
	   	
	   	//create overlay	
	   	t.$overlay = jQuery('<iframe class="o3_popup_overlay" allowtransparency="true"></iframe>').appendTo('body');	
	   	if ( !t.swffix ) {
			t.$overlay.css( { backgroundColor: t.opts.overlayColor, 
		  		              opacity: t.opts.overlayOpacity } );
		} else {
			t.$overlay.addClass('o3_popup_container_iframe').css( { 
				width: t.opts.width, 
	  		    height: t.opts.height
		  	} );
		}

		//t.className += jQuery.trim(t.showAnimationClass()+' '+t.hideAnimationClass());

		t.$container_outer = jQuery('<div class="o3_popup_container_outer"></div>').appendTo('body');		  	
		
	   	//create cotainer
	  	t.$container = jQuery('<div class="o3_popup_container '+t.htmlsafe(t.className)+'"></div>').appendTo(t.$container_outer);	
	  	t.$container.css( { width: t.opts.width, 
	  		                height: t.opts.height } );

		//header
	  	t.$header = jQuery('<div class="o3_popup_container_header"></div>').appendTo(t.$container);
	  	t.$header.css( { height: t.opts.header.height,
	  					 display: t.opts.header.visible === true ? 'block' : 'none' } );

	  	t.$header_title = jQuery('<div class="o3_popup_container_title"><span>'+t.htmlsafe( t.opts.header.title )+'</span></div>').appendTo(t.$header);
	  	t.$header_title.css( { height: t.opts.header.height, 'line-height': t.opts.header.height } );
		
		t.$header_close = jQuery('<button class="o3_popup_container_close" tabindex="10000"><span>x</span></button>').appendTo(t.$header);	  	
	  	t.$header_close.css( { height: t.opts.header.height, 'line-height': t.opts.header.height, 
	  						   'max-width': t.opts.header.height, 'min-width': t.opts.header.height,
	  						   display: t.opts.header.showCloseButton === true ? 'block' : 'none' } );

	  	t.$header_close.bind('click',function(){ t.close(); return false; });

		//footer
		t.$footer = jQuery('<div class="o3_popup_container_footer"></div>').appendTo(t.$container);
	  	t.$footer.css( { height: t.opts.footer.height,
	  					 display: t.opts.footer.visible === true ? 'block' : 'none' } );		

	  	var tabindex = o3_popup_zindex;	  		  	

	  	for( prop in t.opts.footer.content ) {
	  		(function(entry) {

		  		entry = jQuery.extend({ visible: true, //is visible
								   type: 'none', //button, submit, none
								   title: '',
								   position: 'right',
								   tabindex: null,
								   disabled: false,
								   focused: false,
								   className: '',
								   onclick: null //onclick hadler
								 }, entry );

		  		//create item
		  		var $container = null;
		  		switch ( entry.type ) {
		  			case 'button':
		  				$container = jQuery('<input type="button" value="'+t.htmlsafe( entry.title )+'" class="o3_popup_container_button '+entry.className+'" tabindex="'+( entry.tabindex === null ? tabindex : entry.tabindex )+'" />').appendTo(t.$footer);
		  				tabindex++; //next tab index
		  				break;
		  			case 'submit':
		  				$container = jQuery('<input type="submit" value="'+t.htmlsafe( entry.title )+'" class="o3_popup_container_submit '+entry.className+'" tabindex="'+( entry.tabindex === null ? tabindex : entry.tabindex )+'" />').appendTo(t.$footer);
		  				$container.click( function( event ) { t.submit() } );
		  				tabindex++; //next tab index
		  				break;
		  		};

		  		if ( entry.type == 'button' ||  entry.type == 'submit' ) {
			  		//add click event
			  		$container.bind('click',function(event) { 
						if ( entry.onclick != null ) entry.onclick( event, t );
					});

			  		//set focuse object			  		
					if ( entry.focused && !navigator.userAgent.match(/(iPad|iPhone|iPod)/g) )
						t.$focus_obj = $container;
					
					if ( !entry.visible )
						$container.css('display','none');

				};

		  		//set position
		  		if ( ['left','right','none'].indexOf( entry.position ) !== false && $container != null ) {	  			
		  			$container.css('float',entry.position)
		  		};

		  		t.footer_contents.push( $container );

			})(t.opts.footer.content[prop]);
	  	};

	  	/*t.opts.footer.content.forEach(function(entry) {

	  		entry = jQuery.extend({ visible: true, //is visible
							   type: 'none', //button, submit, none
							   title: '',
							   position: 'right',
							   tabindex: null,
							   disabled: false,
							   focused: false,
							   onclick: null //onclick hadler
							 }, entry );

	  		//create item
	  		var $container = null;
	  		switch ( entry.type ) {
	  			case 'button':
	  				$container = jQuery('<input type="button" value="'+t.htmlsafe( entry.title )+'" class="o3_popup_container_button" tabindex="'+( entry.tabindex === null ? tabindex : entry.tabindex )+'" />').appendTo(t.$footer);
	  				tabindex++; //next tab index
	  				break;
	  			case 'submit':
	  				$container = jQuery('<input type="submit" value="'+t.htmlsafe( entry.title )+'" class="o3_popup_container_submit" tabindex="'+( entry.tabindex === null ? tabindex : entry.tabindex )+'" />').appendTo(t.$footer);
	  				$container.click( function( event ) { t.submit() } );
	  				tabindex++; //next tab index
	  				break;
	  		};

	  		if ( entry.type == 'button' ||  entry.type == 'submit' ) {
		  		//add click event
		  		$container.bind('click',function(event) { 
					if ( entry.onclick != null ) entry.onclick( event, t );
				});

		  		//set focuse object
				if ( entry.focused )
					t.$focus_obj = $container;

				//set focuse object
				if ( !entry.visible )
					$container.css('display','none');

			};

	  		//set position
	  		if ( ['left','right','none'].indexOf( entry.position ) !== false && $container != null ) {	  			
	  			$container.css('float',entry.position)
	  		};

	  		t.footer_contents.push( $container );

		});*/

		//create body container
	  	t.$body = jQuery('<div class="o3_popup_body"><div class="o3_popup_body_inner"><div class="o3_popup_body_content"></div></div></div>').appendTo(t.$container);	
	  	t.$body.css( { width: t.opts.width } );

	  	t.$body_container = t.$body.find('.o3_popup_body_content');
	  	switch ( t.opts.body.type ) {
	  		case 'html':
		  		t.$body_container.html( t.opts.body.src );
	  			break;
	  		case 'url':
		  		break;
	  	};

	  	jQuery(window).resize(function(){t.handle_wnd_resize()}); //check on resize

		//add draging
		t.init_drag();

	  	//add closeOnEsc	  	
	  	jQuery(window).bind('keyup',function(event){ 
	  		event = event || window.event;
	  		if ( t.visible && event.keyCode == 27 && t.opts.closeOnEsc && !t.disabled && o3_popup_zindex == t.zindex ) {
    			t.close();
    		};
    	});
    	

	};
	t.init();	
	

	//public functions 

	//store scroll top 
	t.oldScrollPos = { left: 0, top: 0 };

	/**
	* show popup
	*/	
	t.open = t.show = function() {
		if ( !t.visible ) {			

			//after close set back the content
			if ( t.opts.clearOnClose ) {
				if ( t.opts.body.type == 'html' && t.opts.body.src.length > 0 && t.$body_container.html().length == 0 )
					t.$body_container.html( t.opts.body.src );
		  	};

			//reset drag status
			t.is_draged = false;

			//save original scroll position		
			t.oldScrollPos = {  left: Math.max( jQuery('body').scrollLeft(), jQuery('html').scrollLeft() ), top: Math.max( jQuery('body').scrollTop(), jQuery('html').scrollTop() ) };		

			//remove scroll on body
			if ( o3_popup_zindex <= o3_popup_zindex_ref )
				jQuery( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? 'body' : 'body,html' ).addClass('o3_popup_no_overflow');

			
			//show overlay
			if ( !t.swffix ) {
				t.$overlay.stop().css('display',t.showOverlay ? 'block' : 'none').animate({ opacity: t.opts.overlayOpacity }, jQuery(window).width() > t.mobileWndWidthLimit ? t.opts.fadeInSpeed : 0, function() {});			
			} else {
				t.$overlay.css('display', 'block');
			}


			t.$container_outer.css('display', 'block');

			//show container
			if ( t.animate ) {
				t.$container
				.removeClass( t.hideAnimationClass() )
				.removeClass( t.hideAnimationClass( true ) )
				.removeClass( t.showAnimationClass( true ) )
				.addClass( t.showAnimationClass() )			
				.css('display','block');

				setTimeout(function(){
					t.$container.addClass( t.showAnimationClass( true ) );
					setTimeout(function(){
						t.$container.removeClass( t.showAnimationClass() );
					}, 300 );
				},50);
				/*addClass( t.showAnimationClass( true ) )*/;
				/*.animate({ opacity: 1 }, jQuery(window).width() > t.mobileWndWidthLimit ? t.opts.fadeInSpeed : 0, function() {})*/;
			} else {
				t.$container.css( {
					'display': 'block',
					'opacity': 1
				});
			}

			//set visible flag
			t.visible = true;

			//disable all tabindex 
			/*
			if ( o3_popup_zindex <= o3_popup_zindex_ref )
				jQuery('input:not(".o3_popup_container input"),select:not(".o3_popup_container select"),textarea:not(".o3_popup_container textarea"),a:not(".o3_popup_container a")').each(function(){
					 //zindex
					 var t = jQuery(this), tabindex = t.attr('tabindex');
					 if ( typeof tabindex != 'undefined' )
					 	t.attr( 'o3_pop_old_tabindex', tabindex );
					 t.attr( 'tabindex', -1 );

					 //disable
					 var disabled = t.attr('disabled');
					 if ( typeof disabled != 'undefined' )
					 	t.attr( 'o3_pop_old_disabled', disabled );
					 t.attr( 'disabled', true );
				});
			*/

			//dec z index
			o3_popup_zindex++;

			//hide loading
			t.showLoad( false );

			//enable
			t.disable( false );			

			//set zindex of pop
			t.set_zindex( o3_popup_zindex );

			//load url if needed
			t.load();

			//disable windod event
			jQuery(window).bind('click',t.cancel_event);
			//jQuery(window).bind('keydown',t.cancel_event);

		};

		//handle resize
		t.handle_wnd_resize();
	};
	
	//reload pop
	t.reload = function( url ) {
		if ( typeof url == 'string' )
			t.opts.body.src = url;
		t.loaded = false;
		t.load();
	};

	t.loaded = false;
	t.load = function() {
		if ( t.onbeforeload )
			t.onbeforeload.call( t );

		if ( t.opts.body.type == 'url' ) {
			
			if ( !t.loaded ) {
				if ( !t.loading && t.opts.body.src != '' ) {
					//show loading
					t.showLoad();

					//load
					this.$body_container.load( t.opts.body.src, function( responseText, textStatus, XMLHttpRequest ) {

																  //hide loading
																  t.showLoad( false );

																  //flag loaded
																  t.loaded = true;

																  if ( t.onafterload )
																  	 t.onafterload.call( t, responseText, textStatus, XMLHttpRequest );

																});
				};
			} else {
				if ( t.onafterload )
					t.onafterload.call( t );
			};	
		} else {
			if ( t.onafterload )
				t.onafterload.call( t );
		};
	};

	/**
	* hide popup
	*/
	t.hide = function() {	
		if ( t.visible ) {
			
			//set visible flag
			t.visible = false;

			//show overlay
			if ( !t.swffix ) {
				t.$overlay.stop().animate({ opacity: 0 }, jQuery(window).width() > t.mobileWndWidthLimit ? t.opts.fadeOutSpeed : 0, function() { jQuery(this).css('display','none'); });						
			} else {
				t.$overlay.css('display','none');
			}

			//hide container 
			if ( t.animate ) {
				t.$container.removeClass( t.showAnimationClass() ).addClass( t.hideAnimationClass() ).addClass( t.hideAnimationClass( true ) );

				if ( t.opts.hideAnimation != '' ) {
					setTimeout( function() {
						t.$container.css('display','none');
						t.$container_outer.css('display', 'none');
					}, t.opts.hideAnimationTime );
				} else {
					t.$container.css('display','none');
					t.$container_outer.css('display', 'none');
				};

				/*stop().animate({ opacity: 0 }, jQuery(window).width() > t.mobileWndWidthLimit ? t.opts.fadeOutSpeed : 0, function() { jQuery(this).css('display','none'); })*/;	
			} else {
				t.$container.css({
					'display': 'none',
					'opacity': 0
				});
			}

			//inc z index
			o3_popup_zindex--;

			//set zindex of pop
			t.set_zindex( o3_popup_zindex );

			//remove scroll on body
			if ( o3_popup_zindex <= o3_popup_zindex_ref ) {
				if ( jQuery('body').hasClass('o3_popup_no_overflow') ) jQuery('body').removeClass('o3_popup_no_overflow');
				if ( jQuery('html').hasClass('o3_popup_no_overflow') ) jQuery('html').removeClass('o3_popup_no_overflow');
							
				//restore original scroll position, only on mobile devices
				if ( jQuery(window).width() <= t.mobileWndWidthLimit ) {				
					if ( window.scrollTo ) {					
						window.scrollTo( t.oldScrollPos.left, t.oldScrollPos.top );
					} else {
						jQuery('html,body').scrollTop(t.oldScrollPos.top).scrollLeft(t.oldScrollPos.left);						
					};
				};
			};

			//disable windod event
			jQuery(window).unbind('click',t.cancel_event);
			//jQuery(window).unbind('keydown',t.cancel_event);
			
			//remove disabled tabindex
			/*
			if ( o3_popup_zindex <= o3_popup_zindex_ref )
				jQuery('input:not(".o3_popup_container input"),select:not(".o3_popup_container select"),textarea:not(".o3_popup_container textarea"),a:not(".o3_popup_container a")').each(function(){
					 //zindex
					 var t = jQuery(this), tabindex = t.attr('o3_pop_old_tabindex');
					 if ( typeof tabindex != 'undefined' ) {
					 	t.attr( 'tabindex', tabindex );
					 } else {
					 	t.removeAttr( 'tabindex' );
					 };

					 //disabled
					 var disabled = t.attr('o3_pop_old_disabled');
					 if ( typeof disabled != 'undefined' ) {
					 	t.attr( 'disabled', disabled );
					 } else {
					 	t.removeAttr( 'disabled' );
					 };

				});
			*/

		};
	};
	
	/**
	* Same as hide popup, but triggers the close events
	*/
	t.close = function() {

		//run handler
		if ( t.onbeforeclose != null )
			t.onbeforeclose.call( t );

		//hide popup
		t.hide();

		//close finish
		if ( t.opts.hideAnimation != '' ) {
			setTimeout(function(){
				t.after_close();	
			}, t.opts.hideAnimationTime );
		} else {
			t.after_close();
		};

	};

	/**
	* Same as hide popup, but triggers the close events
	*/
	t.after_close = function() {
		if ( t.opts.clearOnClose ) {
			//set empty content on close
			this.$body_container.html('');
			t.loaded = false;
		};
		
		//run handler
		if ( t.onafterclose != null )
			t.onafterclose.call( t );
	};

	/**
	* Cancel click/key event outside of pop
	* @param event object
	* @return boolean/void
	*/
	t.cancel_event = function( event ) {
		var obj = event.target,
			obj_original = t.$container.get(0),
			cancel = true;
		while ( obj && typeof obj.parentNode != 'undefined' ) {
			if ( obj == obj_original ) {
				cancel = false;
				break;	
			};
			obj = obj.parentNode;
		};

		if ( cancel ) {
			if ( window.attachEvent ) {
				event.cancelBubble = true; 
			} else {
				event.stopPropagation();
				event.preventDefault();
			};
			return false;
		};
	};

};


/*********************PRIVATE FUNCTIONS*********************/

/**
* Convert string to html safe
* @param string str String to convert
* @return string The html safe string
*/
o3_popup.prototype.htmlsafe = function( str ) {
	return str.replace(/>/g, '&gt;').replace(/</g, '&lt;');
};

/**
* Set zindex of popup
* @param int
* @return void
*/
o3_popup.prototype.set_zindex = function( value ) {
	this.zindex = value;
	this.$overlay.css('z-index',value);
	this.$container.css('z-index',value);
	this.$container_outer.css('z-index',value);
};		


//event on drag start
o3_popup.prototype.drag_start = function( event ) {
	jQuery('*').addClass('o3_pop_unselectable'); //disable selection todo: add o3_pop class
	this.coffset = this.$container.offset();		
};

//event on drag move
o3_popup.prototype.drag_move = function( event ) {	
	var pos = { left: (event.left-this.coffset.left), top: (event.top-this.coffset.top) };	

	//lt ie 9 user css left top else use css transform
	if ( /MSIE/i.test(navigator.userAgent) && parseFloat((navigator.userAgent.toLowerCase().match(/.*(?:rv|ie)[\/: ](.+?)([ \);]|$)/) || [])[1]) < 9 ) {
		var css = { 'left': pos.left, 'top': pos.top };
		this.$container.css( css );		
	} else {
		var translate = 'translate('+pos.left+'px,'+pos.top+'px)',
			css = { '-ms-transform': translate, '-webkit-transform': translate, 'transform': translate }; 
		this.$container.css( css );		
	};

};

//event on drag end
o3_popup.prototype.drag_end = function( event ) {
	//container was draged
	this.is_draged = true;

	jQuery('*').removeClass('o3_pop_unselectable'); //enable selection
	
	//store offset pos
	this.coffset = this.$container.offset();	

	//reset translate
	var translate = 'none';

	var v = navigator && navigator.appVersion && (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/); //app version

	this.marginTop = ( this.coffset.top - ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) &&  parseInt(v[1], 10) < 7 ? 0 : Math.max( jQuery('body').scrollTop(), jQuery('html').scrollTop() ) ) );
	this.marginLeft = ( this.coffset.left - ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) &&  parseInt(v[1], 10) < 7 ? 0 : Math.max( jQuery('body').scrollLeft(), jQuery('html').scrollLeft() ) ) );

	var css = { 'left': '0px', 
			   'top': '0px',
			   '-ms-transform': translate, 
			   '-webkit-transform': translate, 
			   'transform': translate
			};
	this.$container.css( css );

	this.handle_wnd_resize();
};

/** init drag events */
o3_popup.prototype.init_drag = function() {
	var t = this;
	if ( t.dragable && typeof jQuery.fn.o3_touch == 'function' && jQuery(window).width() >= t.mobileWndWidthLimit ) //check if o3_touch is loaded		
		if ( !t.is_drag_init ) {
			t.is_drag_init = true; //flag that the drag was initialized
			
			//show pointer
			t.$header_title.addClass( 'o3_pop_movecursor' );
			
			//add events
			t.$header_title.o3_touch();	
			t.$header_title.bind( 'dragstart', function (event) { if ( jQuery(window).width() >= t.mobileWndWidthLimit ) t.drag_start( event ); } );
			t.$header_title.bind( 'dragmove', function (event) { if ( jQuery(window).width() >= t.mobileWndWidthLimit ) t.drag_move( event ); } );			
			t.$header_title.bind( 'dragend', function (event) { t.drag_end( event ); } );			
		};
};

/*********************PUBLIC FUNCTIONS*********************/

/**
* Hide the top corner close button
* @param boolean value Show/hide
* @return void
*/
o3_popup.prototype.show_close = function( value ) {
	value = typeof value == 'undefined' ? true : value;
	this.opts.header.showCloseButton = value;	
	this.$header_close.css( 'display', this.opts.header.showCloseButton === true ? 'block' : 'none' );	
};

/**
* Disable the popup
* @param boolean value Disable/enable
* @return void
*/
o3_popup.prototype.disable = function( value ) {
	value = typeof value == 'undefined' ? true : value;
	this.disabled = value;
	
	this.$container.find('input,select,textarea,a,button,submit').attr('disabled',value);

	//focus pop
	if ( !navigator.userAgent.match(/(iPad|iPhone|iPod)/g) )
		this.focus( !value );
};

/**
* Show loader
* @param boolean value Show/hide
* @return void
*/
o3_popup.prototype.showLoad = function( value ) {
	value = typeof value == 'undefined' ? true : value;
	this.loading = value;
	if ( value ) {
		this.$body.addClass('o3_popup_body_loading');
		this.$body_container.css('display','none');
	} else {
		this.$body.removeClass('o3_popup_body_loading');
		this.$body_container.css('display','block');
	};
};

/**
* Trigger submit
* @return void
*/
o3_popup.prototype.submit = function() {
	return typeof this.onsubmit == 'function' ? this.onsubmit.call( this ) : null;
};	

/**
* Focus popup
* @param boolean
* @return void
*/
o3_popup.prototype.focus = function( value ) {
	value = typeof value == 'undefined' ? true : value;
	if ( value ) {
		if ( this.$focus_obj != null )
			this.$focus_obj.focus();
	} else {
		jQuery('*',this.$container).blur();
	};
};


/**
* Set popup title
* @param string
* @return void
*/
o3_popup.prototype.setTitle = function( value ) {
	this.opts.header.title = value;
	this.$header_title.find('span').html( value );
};

/**
* Set popup content
* @param string
* @return void
*/
o3_popup.prototype.setContent = function( value ) {
	this.opts.body.src = value;
	switch ( this.opts.body.type ) {
  		case 'html':
	  		this.$body_container.html( this.opts.body.src );
  			break;
  		case 'url':
	  		break;
  	};		
};	

//alert	
o3_popup_alert = function( title, msg, opts, submitLabel ) {

	submitLabel = typeof submitLabel == 'undefined' ? o3_lang_('OK') : submitLabel;
	
	//options
	options = jQuery.extend({ width: 230,
					  height: 170,
					  body: {
							type: 'html',
							src: '<div class="o3_popup_container_alert"></div><div class="o3_popup_container_alert_msg">'+msg+'</div>'
					  }
					}, opts );

	//update header
	options.header = jQuery.extend( {
						title: title
					}, options.header );

	//update footer
	options.footer = jQuery.extend( {
						content: [{
							type: 'submit',
							title: submitLabel,
							position: 'right',
							focused: true,
							onclick: function( event, pop ) { 
								pop.close();
							}
						}]
					}, options.footer );

	return new o3_popup( options );
};

//confirm
o3_popup_confirm = function( title, msg, opts, cancelLabel, submitLabel ) {

	cancelLabel = typeof cancelLabel == 'undefined' ? o3_lang_('Cancel') : cancelLabel;
	submitLabel = typeof submitLabel == 'undefined' ? o3_lang_('OK') : submitLabel;

	//options
	options = jQuery.extend({ width: 420,
								height: 160,
								/*
								onbeforeclose: function() {	
									//if ( window.o3_pop_delete_alert )						
									//	window.o3_pop_delete_alert.close();						
								},*/
								body: {
									type: 'html',
									src: msg
								}
							}, opts );

	//update header
	options.header = jQuery.extend( {
						title: title
					}, options.header );

	//update footer
	options.footer = jQuery.extend( {
						content: [								
							{
								type: 'submit',
								title: submitLabel,
								position: 'right',
								focused: true,
								tabindex: 11
							},
							{
								type: 'button',
								title: cancelLabel,
								position: 'right',
								tabindex: 10,
								onclick: function( event, pop ) { 
									pop.close();
								}
							}
					]}, options.footer );

	return new o3_popup( options );
};