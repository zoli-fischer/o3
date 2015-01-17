//onload
(function($) {
	$(document).ready(function () {					
				
		//resize nav menu
		function resize_nav_menu() { $('#admin_menud').height(1).height($('#admin_menu').height()); };
		$(window).resize( resize_nav_menu );
		resize_nav_menu();
		
		$('#data_frame').bind('load',function(){
			$('#data_frame').addClass('iframe_show');
			$('.page').addClass('page-noload');
		});

		//init anchor change checker
		initAnchoreCheck( function(data) {
			var data = typeof data == 'undefined' ? 'menu-overview' : data;
			
			if ( data.search("menu-") == 0 ) {
				//remove sel menu
				$('.menud A').removeClass('sel');				
				
				//select current menu
				$('#'+data).addClass('sel');

				//truncate data
				data = data.substr(5,256);

				//load content
				$('.page').removeClass('page-noload');
				$('#data_frame').removeClass('iframe_show').attr('src','index.php?load='+escape(data))
			}

		});

	
	});
})(jQuery);

/*start check url anchore changes*/
function initAnchoreCheck(callb) {
	setInterval(function(){ checkAnchore(callb); },50);
}

var Anchore = '';
function checkAnchore(callb) {
	var l = window.location.toString().split('#');
	if ( ( l.length > 1 && Anchore != l[1] ) || ( l.length == 1 && Anchore != l[1] ) ) {
		Anchore = l[1];
		if ( callb != null )
			callb(Anchore);
	}
}
/*end check url anchore changes*/

/*push notifications*/

//notification object
push_notification_item = function( text ) {

	var t  = this;

	//prepend notification to the list
	t.$obj = jQuery('<div class="item">'+text+'</div>').prependTo('.push_notification');

	//hide the item
	this.hide = function() {
		t.$obj.removeClass('item_show');		
	};

	//show the item
	this.show = function() {
		t.$obj.addClass('item_show');
		setTimeout( t.hide, 3000 );
	};
	
	//after 200 millisecond show the item
	setTimeout( t.show, 200 );

};

function push_notification_msg( obj ) {
	var obj = jQuery.parseJSON( obj );
	for ( var i = 0; i < obj.length; i++ ) {
		new push_notification_item( obj[i] );
	};
};

/*push notifications*/