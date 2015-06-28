/**
* Add touch/pointer event to a dom object
* Events: tap, double tap, drag, pinch 2 finger
*/
(function ( $ ) {
		
	var w = window,
		d = document,
		MSPointerEvent = window.MSPointerEvent,
		pointer = typeof w.onmousedown !='undefined' ? false : window.navigator.pointerEnabled || MSPointerEvent,
		touch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0) || pointer, //check for touch device
		//touch or mouse events needed
		downEvt = touch ? 'mousedown,touchstart' : ( !pointer ? 'mousedown' : ( MSPointerEvent ? 'MSPointerDown' : 'pointerdown' ) ),
		upEvt = touch ? 'mouseup,touchend' : ( !pointer ? 'mouseup' : ( MSPointerEvent ? 'MSPointerUp' : 'pointerup' ) ),
		moveEvt = touch ? 'mousemove,touchmove' : ( !pointer ? 'mousemove' : ( MSPointerEvent ? 'MSPointerMove' : 'pointermove' ) );

	function addEvt( obj, type_, func_, useCapture ) {
		if ( obj ) {
			var types = type_.split(','); 
			for ( var i = 0; i < types.length; i++ ) {
				if ( obj.attachEvent ) {					
					obj.attachEvent( 'on'+types[i], func_ );
				} else {
					obj.addEventListener( types[i], func_, useCapture );
				};
			};
		};
	};
	
	function remEvt( obj, type_, func_, useCapture ) {		
		if ( obj ) {
			var types = type_.split(','); 
			for ( var i = 0; i < types.length; i++ ) {
				if ( obj.attachEvent ) {					
					obj.detachEvent( 'on'+types[i], func_ );
				} else {
					obj.removeEventListener( types[i], func_, useCapture );
				};
			};
		};
	};
	
	function isSameEvent( e1, e2 ) {
		if ( e1 !== null && e2 !== null && (
			 ( e1.type == 'mousedown' && e2.type == 'touchstart' ) ||
		     ( e1.type == 'mouseup' && e2.type == 'touchend' ) ||
		     ( e1.type == 'mousemove' && e2.type == 'touchmove' ) ) ) {
			var a1 = {
				touches: e1.touches,
				clientX: e1.touches ? e1.touches[0].clientX : e1.clientX,
				clientY: e1.touches ? e1.touches[0].clientY : e1.clientY
			},
			a2 = {
				touches: e1.touches,
				clientX: e1.touches ? e1.touches[0].clientX : e1.clientX,
				clientY: e1.touches ? e1.touches[0].clientY : e1.clientY
			};	

			return a1.clientX == a2.clientX && a2.clientY == a2.clientY;
		};

		return false;
	};

	//prevent event default
	function prevDef(e) {
		if ( w.attachEvent ) {
			e.cancelBubble = true; 
		} else {
			e.stopPropagation();
			e.preventDefault();
		};
	};
	
	//cancel event function
	function f_( e ) {
		e = typeof(e) == 'undefined' ?  w.event : e; //ie fix
		prevDef(e);
		return false;
	};
	
	$.fn.o3_touch = function( opts ) {
	
		var p = $.extend({
				        tapMaxDistance: 50, // max distance between taps / clicks
						tapDelay: 500, //delay between taps
						minDirectionChnageDegree: 45, //degrees difference for direction change
						minDistanceForDrag: 1, //distance for start point to trigger drag, if 0 dragstart will trigger without move
						preventDefault: true
				    }, opts );
        
		var t = this;		
		t.o = t.get(0);

		//drag start coord. on the window
		t.lX = 0;
		t.lY = 0;
		
		//drag start coord. on the window
		t.dsX = 0;
		t.dsY = 0;

		//drag start coord. for delta
		t.dsdeltaX = 0; t.deltaX = 0;
		t.dsdeltaY = 0; t.deltaY = 0;
		
		//offset of the obj at drag start
		t.doX = 0;
		t.doY = 0;
		
		//distance coord. between the start coord and the offset
		//on draging we need to know where is the left, top of the object relative to the drag coord.
		t.ddifX = 0;
		t.ddifY = 0;
		
		//distance between the drag coord. and drop. coord, if 0 no drag occured
		t.dtotdist = 0;
		t.ddist = 0;
		t.ddistt = 0;
		
		t.ds = 0;	
		t.dts = 0;
		t.da = -1000;
		
		//is currently draging?
		t.dr = false;
		
		// is drag started?
		t.drs = false;
	
		t.zdist = 0; //distance between 2 touches
		t.zs = 1; //scale value
		t.zrefdist = 0;
		t.zsX = [ 0, 0 ]; //touches x coord
		t.zsY = [ 0, 0 ]; //touches y coord
		t.zoX = 0; //x between touches on x
		t.zoY = 0; //y between touches on y
		t.pi = false; //is currently zoomin? 
		t.zst = false; // is zoom started?
	 	
		
		t.tt = null; //timer for checking the time between the 2 click or tap
		t.tdist = 999999; //distance betwen the 2 click or tap
		t.tst = 0; //tap start timer
		t.tsX = [ 0, 0 ]; //x coord. of the first and second click / tap
		t.tsY = [ 0, 0 ]; //y coord. of the first and second click / tap
		t.tc = 0; //tap count		
		
		//start touch
		t.tchs = 0;
		t.btn = -1; //mouse button for drag, 1 - left, 2 - center, 3 - middle
		t.type = '';
		

		t.emoveing = null;
		t.moveing = function(e) {
			e = typeof(e) == 'undefined' ?  w.event : e; //ie fix

			//check for repeated event
			if ( touch && isSameEvent( e, t.emoveing ) ) {
				t.emoveing = null;
			} else {

				//console.log('event: '+e.type+' '+t.attr('id'));
				
				//store event
				t.emoveing = e;	

				//add ms touches
				if ( touch && typeof(e.touches) == 'undefined' )
					e.touches = t.checkMSTouches(e);
				

				if ( t.type == 'drag' && t.dr ) {						
					
					var t0 = !touch ? e : e.touches[0];
					 
					//check coord. distance 
					l_ = ( t0.clientX - t.ddifX );
					t_ = ( t0.clientY - t.ddifY );
					
					//update distance
					t.ddist = Math.sqrt( Math.pow( ( t.dsX - t0.clientX ), 2 ) + 
																	Math.pow( ( t.dsY - t0.clientY ), 2 ) );
					
					t.dtotdist += Math.sqrt( Math.pow( ( t.lX - t0.clientX ), 2 ) + 
																	Math.pow( ( t.lY - t0.clientY ), 2 ) );
					t.lX = t0.clientX;
					t.lY = t0.clientY;
					
					var a_ = Math.atan2( t.dsY - t0.clientY, t.dsX - t0.clientX ) * 180 / Math.PI,
						adif = Math.abs( a_ - t.da );

					if ( adif >= p.minDirectionChnageDegree && t.ddist > p.minDistanceForDrag ) {
						
						t.da = a_;
						
						t.dsX = touch ? t0.clientX : e.clientX;
						t.dsY = touch ? t0.clientY : e.clientY;
						
						t.ddist = 0;
						t.ddistt = 0;
						t.dts = +new Date();
													
					} else {
						
						t.ddistt = +new Date() - t.dts;							
						t.ds = t.ddist / t.ddistt;
						
					};	

					var pageY = touch ? ( t0.pageY ? t0.pageY : ( t0.clientY + document.documentElement.scrollTop ) ) : ( ( e.pageY ? e.pageY : e.clientY + document.documentElement.scrollTop ) ),
						pageX = touch ? ( t0.pageX ? t0.pageX : ( t0.clientX + document.documentElement.scrollLeft ) ) : ( ( e.pageX ? e.pageX : e.clientX + document.documentElement.scrollLeft ) );
				

					if ( !t.drs ) {
						
						//msexplorer mobile fix. triggers move on touch
						if ( t.ddist > 0 ) {	
							
							if ( t.tc != 0 )
								t.ctap(); //clear tap

							t.drs = true;					
							t.trigger( $.Event( t.type+'start', { event: e, 
																  clientX: touch ? t0.clientX : e.clientX, 
																  clientY: touch ? t0.clientY : e.clientY,
																  pageX: pageX, 
																  pageY: pageY } ) );
						};

					} else {				
							
						var clientX = touch ? t0.clientX : e.clientX, 
							clientY = touch ? t0.clientY : e.clientY;					
						t.deltaX = clientX - t.dsdeltaX; 
						t.deltaY = clientY - t.dsdeltaY;
														  
						t.trigger( $.Event( 'dragmove', { event: e, 
														  left: l_, 
														  top: t_, 
														  distance: t.ddist, 
														  speed: t.ds, 
														  angle: t.da, 
														  button: t.btn,
														  clientX: clientX, 
														  clientY: clientY,
														  deltaX: t.deltaX, 
														  deltaY: t.deltaY,
														  pageX: pageX,
														  pageY: pageY } ) );					
					};
					
				} else if ( t.type == 'zoom' && t.pi ) {
					
					t.zsX[0] = e.touches[0].clientX;
					t.zsY[0] = e.touches[0].clientY;
					
					t.zsX[1] = e.touches[1].clientX;
					t.zsY[1] = e.touches[1].clientY;
					
					t.zdist = Math.sqrt( Math.pow( ( t.zsX[0] - t.zsX[1]), 2 ) + Math.pow( ( t.zsY[0] - t.zsY[1] ), 2 ) );					
					t.zs = t.zdist / t.zrefdist;
					
					var o_ = t.offset();
					t.doX = o_.left;
					t.doY = o_.top;
					
					if ( !t.zst ) {				
						if ( t.tc != 0 )
							t.ctap(); //clear tap
					
						t.zst = true;
						t.trigger( $.Event( t.type+'start', { event: e, clientX: t.zsX, clientY: t.zsY, origo: { left: t.zoX , top : t.zoY } } ) );
					}	else {									
						t.trigger( $.Event( 'zoommove', { event: e, scale: t.zs, distance: t.zdist, startdistance: t.zrefdist, origo: { left: t.zoX , top : t.zoY } } ) );
					};
				}; 	
				
				//prevent touch default
				if ( p.preventDefault )
					prevDef(e);
			};	
		};	

		//old sdown event
		t.esdown = null;

		//on touch start event		
		t.sdown = function(e) {
			//ie fix
			e = typeof(e) == 'undefined' ?  w.event : e;
			
			//check for repeated event
			if ( touch && isSameEvent( e, t.esdown ) ) {
				t.esdown = null;
			} else {

				console.log('event: '+e.type+' '+t.attr('id'));

				//store event
				t.esdown = e;
	
				//add ms touches
				if ( touch && typeof(e.touches) == 'undefined' )
					e.touches = t.checkMSTouches(e);

				var tchs  = !touch ? 1 : ( typeof(e.touches) != 'undefined' ? e.touches.length : 1 );

				//check touch count								
				t.checkdown(e,tchs,e.button);
				
				//out focus all
				$(t.d).focus();

				//cancel the dragstart on the element
				addEvt( t.o, 'dragstart', f_ );
				
				//cancel the select start on document
				addEvt( d, 'selectstart', f_ );
			
				//add drag check event
				//todo:
				//if ( touch ) {
					addEvt( d, moveEvt, t.moveing , false );				
				//} else { 
				//	addEvt( d, moveEvt, t.moveing );
				//};
				
				//if ( p.preventDefault ) prevDef(e);
			};
		};
		
		/*
		t.sup = function(e) {
			//e = typeof(e) == 'undefined' ?  w.event : e; //ie fix	
			//var tchs  = !touch ? 1 : e.touches.length;
		};	
		*/
					
		t.tevt = { target: t.o };	
		t.checkup = function(e,touches,button)  {
			
			if ( t.tc == 1 ) { //if this is the first click / tap													 						
				//start count down for second click / tap
				t.tt = setTimeout( t.ctap, p.tapDelay );
				if ( touch ) {
					if ( e.changedTouches && e.changedTouches.length == 1 ) {
						t.tdist = Math.sqrt( Math.pow( ( t.tsX[0] - e.changedTouches[0].clientX ), 2 ) + Math.pow( ( t.tsY[0] - e.changedTouches[0].clientY ), 2 ) );			
					};
				} else {
					t.tdist = Math.sqrt( Math.pow( ( t.tsX[0] - e.clientX ), 2 ) + Math.pow( ( t.tsY[0] - e.clientY ), 2 ) );			
				}; 
				
				if ( ( +new Date() - t.tst < p.tapDelay ) && ( t.tdist < p.tapMaxDistance )  ) {				

					//if ( p.preventDefault ) prevDef(e);
					//trigger end event if defined for click
					t.tevt = $.extend( t.tevt, e );
					t.clickTimer = setTimeout( t.triggerTap, p.tapDelay );
				};					
			} else if ( t.tc == 2 ) {  //if this is the second click / tap			 		 
				//calculate distance between the 2 click/tap			
				t.tdist = Math.sqrt( Math.pow( ( t.tsX[0] - t.tsX[1]), 2 ) + Math.pow( ( t.tsY[0] - t.tsY[1] ), 2 ) );																			
				//check if second click was betweent the range
				if ( t.tdist < p.tapMaxDistance ) {												
					t.trigger( $.Event( "doubletap", { event: e, distance: t.tdist, clientX: t.tsX, clientY: t.tsY } ) );
					t.ctap();		
					//if ( p.preventDefault ) prevDef(e);
				};										
			};								
			
		};
		
		t.triggerTap = function() {				
			t.ctap(); 
		 	t.trigger( $.Event( "tap", { event: t.tevt } ) );
		};
		
			
		t.checkdown = function(e,touches,button) {			
			
			t.btn = button; 							
			if ( touches == 1 ) { //double tap or drag
								
				//change in touches
				// shift event type by change
				//if ( t.tchs != touches && t.tchs != 0 ) 
				//	t.end_(event,touches,button);
				
				// do not shift event type by change
				if ( t.tchs != touches && t.tchs != 0 ) {					
					t.end_(e,touches,button);		
					t.ctap();	
					return;
				};
				
				if ( t.type != 'drag' ) {

					//store the current coord.
					t.dsdeltaX = t.lX = t.dsX = touch ? e.touches[0].clientX : e.clientX;
					t.dsdeltaY = t.lY = t.dsY = touch ? e.touches[0].clientY : e.clientY;			
					
					//store the offset coord.
					var o_ = t.offset();
					t.doX = o_.left;
					t.doY = o_.top;	
					
					//store the diff. coord.
					t.ddifX = t.dsX - t.doX;
					t.ddifY = t.dsY - t.doY;
					
					//clear drag start time
					t.dts = +new Date();				
					
					t.da = -1000;
					
					//set the drag distance to 0
					t.ddist = 0;
					t.dtotdist = 0;
			
					//we assume there will be draging
					t.dr = true;	
					
					/*
					//store the current coord.
					t.dsX = touch ? e.touches[0].clientX : e.clientX;
					t.dsY = touch ? e.touches[0].clientY : e.clientY;	
					*/		
					
					//store the offset coord.
					var o_ = t.offset();
					t.doX = o_.left;
					t.doY = o_.top;	
					
					/*
					//store the diff. coord.
					t.ddifX = t.dsX - t.doX;
					t.ddifY = t.dsY - t.doY;
					*/
					
					t.dts = +new Date();				
			
					//drag angel to 0
					t.da = 0;
					
					//set the drag distance to 0
					t.ddist = 0;
			
					//we assume there will be draging
					t.dr = true;	
					
					//for TAP
					clearTimeout(t.clickTimer);						
					//set the distance to 0
					t.tdist = 999999;
					
					if ( t.tc == 0 ) {	//if this is the first click / tap				 
						

						//save 1st touch coodinate
						t.tsX[0] = touch ? e.touches[0].clientX : e.clientX;
						t.tsY[0] = touch ? e.touches[0].clientY : e.clientY;			
						
						//startime for click
						t.tst = +new Date();
						
						//start count down
						t.tt = setTimeout( t.ctap, p.tapDelay );					
						
						//add touch count
						t.tc++;

					} else if ( t.tc == 1 ) { //if this is the second click / tap
						
						//save 2st touch coodinate
						t.tsX[1] = touch ? e.touches[0].clientX : e.clientX;
						t.tsY[1] = touch ? e.touches[0].clientY : e.clientY;			
						
						//start count down
						t.tt = setTimeout( t.ctap, p.tapDelay );
						
						//add touch count
						t.tc++;		
						
					} else {
						//clear count and countdown
						t.ctap();
					};	 
					
					
				};
				
				t.type = 'drag';	
				t.tchs = touches;	
				
				//min dist is 0 -> trigger drag start
				if ( p.minDistanceForDrag == 0 ) {
					if ( !t.drs ) {
						
						if ( t.tc != 0 )
							t.ctap(); //clear tap
					
						t.drs = true;
						t.trigger( $.Event( t.type+'start', { event: e, 
																									clientX: touch ? e.touches[0].clientX : e.clientX, clientY: touch ? e.touches[0].clientY : e.clientY,
																									pageX: touch ? e.touches[0].pageX : e.pageX, pageY: touch ? e.touches[0].pageY : e.pageY } ) );								
					};
				};
				
			} else if ( touches == 2 ) { //zoom		
				
				//clear tap
				if ( t.tc != 0 ) 
					t.ctap();
				
				//change in touches
				if ( t.tchs != touches && t.tchs != 0 )
					t.end_(e,touches,button);
				
				if ( t.type != 'zoom' ) {											
					
					t.zsX[0] = e.touches[0].clientX;
					t.zsY[0] = e.touches[0].clientY;
					
					t.zsX[1] = e.touches[1].clientX;
					t.zsY[1] = e.touches[1].clientY;
					
					t.zdist = t.zrefdist = Math.sqrt( Math.pow( ( t.zsX[0] - t.zsX[1]), 2 ) + Math.pow( ( t.zsY[0] - t.zsY[1] ), 2 ) );
								
					var o_ = t.offset();
					t.doX = o_.left;
					t.doY = o_.top;													
										
					t.zoX = ( t.zsX[0] + t.zsX[1] ) / 2 - t.doX;
					t.zoY = ( t.zsY[0] + t.zsY[1] ) / 2 - t.doY;
					
					t.pi = true;		
					
				};
				
				t.type = 'zoom';
				t.tchs = touches;
				
			} else {
				
				t.end_(e,touches,button);					
				
			};
			
		};	

		//clear count and countdown
		t.ctap = function () {			
			clearTimeout( t.tt );		 	
			t.tst = +new Date();		
			t.tdist = 999999;
			t.tsX = [ 0, 0 ];
			t.tsY = [ 0, 0 ];
			t.tc = 0;
		};
		
		t.end_ = function (e,touches,button) {
				
			//remove cancel the dragstart event from the element
			remEvt( t.o, 'dragstart', f_ );						
			 
			//remove the select start from document
			remEvt( d, 'selectstart', f_ );
				
			t.tchs = 0;	
			t.btn = -1;
			
			if ( t.type == 'drag' && t.drs ) {
				
				t.ddistt = +new Date() - t.dts;
				t.ds = t.ddist / t.ddistt;

				t.trigger( $.Event( "dragend", { 
								event: e, 
								distance: t.ddistt, 
								speed: t.ds, 
								angle: t.da == -1000 ? null : t.da,
								deltaX: t.deltaX, 
								deltaY: t.deltaY
							} ) );
				
				t.dsX = 0;
				t.dsY = 0;
				t.doX = 0;
				t.doY = 0;
				t.ddifX = 0;
				t.ddifY = 0;
				t.dtotdist = 0;
				t.ddist = 0; 
				t.ds = 0;
				t.dts = 0;
				t.da = -1000;
				
				t.dr = false;
				t.drs = false;
				
				//remove drag check event
				remEvt( d, moveEvt, t.moveing );	
				
			} else if ( t.type == 'zoom' && t.zst ) {
				
				t.trigger( $.Event( "zoomend", { event: e, 
															scale: t.zs, distance: t.zdist, startdistance: t.zrefdist, origo: { left: t.zoX , top : t.zoY } } ) );
				
				t.zdist = 0;
				t.zs = 1;
				t.zrefdist = 0;
				t.zsX = [ 0, 0 ];
				t.zsY = [ 0, 0 ];
				t.zoX = 0;
				t.zoY = 0;
				t.pi = false;
				t.zst = false; 
				
				//remove drag check event
				remEvt( d, moveEvt, t.moveing );
				
			};

			//if not zoom remove move event
			if ( t.type != 'zoom' )
				//remove drag check event
				remEvt( d, moveEvt, t.moveing );	

			t.type = '';
		
		};
		
		t.cancel = function() {
		
			//event listener for mouse down or finger touch
			remEvt( t.o, downEvt, t.sdown );
		
			//event listener for mouse up or finger touch
			//remEvt( t.o, upEvt, t.sup );
			
			//event listener for mouse up or finger release
			remEvt( d, upEvt, t.end );
			
			//remove cancel the dragstart event from the element
			remEvt( t.o, 'dragstart', f_ );						
			 
			//remove the select start from document
			remEvt( d, 'selectstart', f_ );
				
			t.tchs = 0;	
			t.btn = -1;		 
			
			t.dsX = 0;
			t.dsY = 0;
			t.doX = 0;
			t.doY = 0;
			t.ddifX = 0;
			t.ddifY = 0;
			t.dtotdist = 0;
			t.ddist = 0; 
			t.ds = 0;
			t.dts = 0;
			t.da = -1000;
			
			t.dr = false;
			t.drs = false;
					 
			t.zdist = 0;
			t.zs = 1;
			t.zrefdist = 0;
			t.zsX = [ 0, 0 ];
			t.zsY = [ 0, 0 ];
			t.zoX = 0;
			t.zoY = 0;
			t.pi = false;
			t.zst = false;
				
			//remove drag check event
			remEvt( d, moveEvt, t.moveing );
	
			t.type = '';		
			
		};
		
		t.inMSTouches = function( id ) {
			var key;
			for (key in t.mstouches) {
				if (t.mstouches.hasOwnProperty(key) &&        // These are explained
				    /^0$|^[1-9]\d*$/.test(key) && // and then hidden
				    key <= 4294967294 && // away below
				    t.mstouches[key].touchId == id
			    	) {
			    		return key;
				};
			};
			return false;
		};
	  
		t.removeMSTouch = function( id ) {
			var key, newtouches = new Array();
			for (key in t.mstouches) {
				if (t.mstouches.hasOwnProperty(key)  &&        // These are explained
			    /^0$|^[1-9]\d*$/.test(key) &&    // and then hidden
			    key <= 4294967294 &&               // away below
			    t.mstouches[key].touchId != id
			    ) {	
				    newtouches.push(t.mstouches[key]);
				};
			};
			t.mstouches = newtouches;
		};
	  
		t.mstouches = new Array();
		t.checkMSTouches = function( eventObject ) {
			// stop panning and zooming			
			if (eventObject.preventManipulation)
				eventObject.preventManipulation();
			
			// we are handling this event			
			if ( eventObject.preventDefault )
				eventObject.preventDefault();
			
			// if we have an array of changedTouches, use it, else create an array of one with our eventObject
			var touchPoints = (typeof eventObject.changedTouches != 'undefined') ? eventObject.changedTouches : [eventObject];
			for (var i = 0; i < touchPoints.length; ++i) {	
				var touchPoint = touchPoints[i];
				// pick up the unique touchPoint id if we have one or use 1 as the default

				var touchPointId = (typeof touchPoint.identifier != 'undefined') ? touchPoint.identifier : (typeof touchPoint.pointerId != 'undefined') ? touchPoint.pointerId : 1;

				if (eventObject.type.match(/(down|start)$/i)) {
					
					// process mousedown, MSPointerDown, and touchstart			
					if ( t.inMSTouches( touchPointId ) === false )	{
						t.mstouches.push( { clientX: touchPoint.clientX, clientY: touchPoint.clientY, touchId: touchPointId } );							
					};
								
				} else if (eventObject.type.match(/move$/i)) {

					var index = t.inMSTouches( touchPointId );					
					// process mousemove, MSPointerMove, and touchmove				
					if ( index !== false && t.mstouches[index] && !(t.mstouches[index].clientX == touchPoint.clientX && t.mstouches[index].clientY == touchPoint.clientY)) {
						t.mstouches[index].clientX = touchPoint.clientX;
						t.mstouches[index].clientY = touchPoint.clientY;
					};
				} else if (eventObject.type.match(/(up|end)$/i)) {
					// process mouseup, MSPointerUp, and touchend
					t.removeMSTouch( touchPointId );					
				};
			};

			//return touch changes
			return touchPoints;
				
		};
		
		//old sdown event
		t.eend = null;

		//end touch
		t.end = function(e) {		
			e = typeof(e) == 'undefined' ?  w.event : e; //ie fix

			//check for repeated event
			if ( touch && isSameEvent( e, t.eend ) ) {
				t.eend = null;
			} else {

				//console.log('event: '+e.type+' '+t.attr('id'));
				
				//store event
				t.eend = e;		

				//add ms touches
				if ( touch && typeof(e.touches) == 'undefined' ) {
					e.changedTouches = t.checkMSTouches(e);				
					e.touches = t.mstouches;
				};

				var tchs  = !touch ? 0 : ( typeof(e.touches) != 'undefined' ? e.touches.length : 0 );
				
				t.checkup(e,tchs,e.button);									
				t.checkdown(e,tchs,e.button);		
			};				
		};
		
		//event listener for mouse down or finger touch
		addEvt( t.o, downEvt, t.sdown, false );
			
		//event listener for mouse up or finger touch
		//addEvt( t.o, upEvt, t.sup, false );
		
		//event listener for mouse up or finger release
		addEvt( d, upEvt, t.end, false );
		
		t.css({ 
			'-ms-touch-action': 'none',
			'touch-action': 'none' });
	
		/*
        userSelect: 'none',
		touchSelect: 'none',
		touchCallout: 'none',
		contentZooming: 'none',
		userDrag: 'none',
		tapHighlightColor: 'rgba(0,0,0,0)'
		*/

		return this;

			
	};
}( jQuery ));