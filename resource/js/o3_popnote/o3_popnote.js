/**
*
* O3 popup notifications
*
* Options:
*
* @author Zoltan Fischer
*/

o3_popnote = function( opts ) {

	var t = this,
		$ = jQuery;

	//options
	t.opts = $.extend({ 
						card_lifetime: 5000,
						card_show_close: true,
						cardspacing: 10,
						close_label: 'x'
					  }, opts );
	
	t.card_lifetime = t.opts.card_lifetime;
	t.card_show_close = t.opts.card_show_close;
	t.cardspacing = t.opts.cardspacing;
	t.close_label = t.opts.close_label;

	//list of cars
	t.cards = [];

	//popup container
	t.$container = null;

	//constructor
	t.init = function() {
	   	
	   	//create cotainer
	  	t.$container = $('<div class="o3_popnote_container"></div>').appendTo('body');	
	  	
	};
	t.init();

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

/** remove card by index */
o3_popnote.prototype.pop = function ( index ) {
	var t = this;
	$card = t.cards[index];

	if ( $card != null ) {

		//move right card
		$card.removeClass('o3_popnote_card_show');//.css('left', $card.offset().left + $card.width() );
		
		//remove dom
		setTimeout( function() {
			if ( $card != null )
				$card.remove();
		 }, 200 );

		t.cards[index] = null;
	};

};

/*********************PUBLIC FUNCTIONS*********************/

/** remove all cards */
o3_popnote.prototype.clear = function () {
	var t = this;
	for ( var i = 0; i < t.cards.length; i++ ) {
		t.pop( i );
	};
};

/** add new card */
o3_popnote.prototype.push = function ( msg, lifetime, show_close ) {
	var t = this,
		$ = jQuery;
	lifetime = typeof lifetime != 'undefined' && lifetime ? lifetime : t.card_lifetime; 
	show_close = typeof show_close != 'undefined' ? show_close : t.card_show_close; 

	//move down the others
	var $objs = $('.o3_popnote_card');

	//create card
	var $card = $('<div class="o3_popnote_card">'+( show_close ? '<a href="javascript:{}" class="o3_popnote_card_close">'+t.close_label+'</a>' : '' )+msg+'</div>').appendTo(t.$container),
		height = $card.outerHeight();
	

	$card.find('.o3_popnote_card_close').click( function() { t.pop( index ) });
	setTimeout( function() { $card.addClass('o3_popnote_card_show'); }, 200 );

	$objs.each(function(){ 
			$(this).css( 'top', $(this).offset().top + t.cardspacing + height - t.$container.offset().top );
		});

	//center card	
	$card.css('left', ( $(window).width() - $card.width() ) / 2 );

	//add card to list
	t.cards.push( $card );

	var index = t.cards.length - 1;

	//set remove card timer
	setTimeout( function() { t.pop( index ) }, lifetime );
	
};