/**
*
* O3 table generator from json array
*
* Options:
* obj DOM Element - Container
* scope array JSON data or string to json url
* headers array array of object, object attrs: 
* 	index - string - index from scope item - default: ''
* 	label - string - text to display - default: ''
*   content - string - text with indexes, function or formula, will be procede a eval on the string
*   value - string - this is used for sorting and filtering, if is omitted the value is taken from the content
* 	sort - object - object attrs: 
*		                   allow - boolean - default: true 			
*   					   type - string - value: 'text', 'number' default: 'text'	
*						   is_default - boolean - default: false
*     	                   ascending - boolean - default: true
* search string Initalize the table with this search string
* onUpdate func On table update this function is called default: null
* text_search_placeholder string Placeholder text for search field. default: 'Type here to search...'
* text_empty_search_result string Text to show if the search result is null. default: 'No items match your search.'
* text_empty_table string Text to show if no item to show. default: 'There are no items to show.' 
*
* @author Zoltan Fischer
*/

o3_table = function( opts ) {

	var t = this;
		
	t.opts = jQuery.extend({ container: null,
						scope: [], 
						headers: [], 
						search: '',				
						onUpdate: null, 										
						text_search_placeholder: typeof o3_lang != 'undefined' ? o3_lang._(o3_table_text.text_search_placeholder) : o3_table_text.text_search_placeholder,
						text_empty_search_result: typeof o3_lang != 'undefined' ? o3_lang._(o3_table_text.text_empty_search_result) : o3_table_text.text_empty_search_result,
				        text_empty_table: typeof o3_lang != 'undefined' ? o3_lang._(o3_table_text.text_empty_table) : o3_table_text.text_empty_table,
				        text_loading: typeof o3_lang != 'undefined' ? o3_lang._(o3_table_text.text_loading) : o3_table_text.text_loading,				        
						max_rows: 32 //max rows to display per page
				    }, opts );
			
	//scope url if set	
	t.scope_url = '';

	//not processed scope
	t.original_scope = [];
	
	//ajax loader function
	t.ajax = o3_table_ajax;
	
	//saved scope data
	t.scope = [];		
	
	//container jquery object
	t.$c = jQuery(t.opts.container);
	
	//init defautl sort index and direction
	t.defaut_sort_index = -1;
	t.defaut_sort_ascending = true;

	//main table jQuery object
	t.$main_table = null;
	
	t.refresh = function() {
		t.setScope( t.original_scope );
	};	

	t.search = function(str) {		
		t.$c.find('.o3_table_search_input').val(str).trigger("change");
	};
	
	//add msg to console
	t.errmsg = function( msg ) {
		if ( console && console.log ) console.log(msg);
	};
	
	//table of sort methods
	t.sort_methots = o3_table_sort_methots;
	
	//preccede content with object data
	t.p_content = function( obj, content ) {
		//return empty string
		if ( content == '' ) return '';
			
		var buffer = '';
		for(var propertyName in obj) {
		  buffer = 'var '+propertyName+'=obj.'+propertyName+';';
		  try {
			eval(buffer);
		  } catch (e) {};
		}		

		var r = content;
		try {
			r = eval(content);
		} catch (e) {};
		return r ? r : '';
	};
	
	//max rows to display per page
	t.max_rows = t.opts.max_rows; 
	t.old_search_str = '';

	//knockout app view	
	t.koapp = null;
	t.appview = function() {
	    var self = this;		 
	    self.search_str = ko.observable(t.opts.search);
	    self.header = ko.observableArray(t.opts.headers);
	    self.scope = ko.observableArray();
	    self.full_scope  = [];
	    self.sort_index = ko.observable(t.defaut_sort_index);
	    self.sort_ascending = ko.observable(t.defaut_sort_ascending);
	    self.max_rows = ko.observable(t.max_rows);
	    self.loading = ko.observable(false);
	    
	    //length of the full scope
	    self.count = ko.observable(0);
    
	    //performe a search
	    self.search_func = function() {			

			//sort scope if needed
			if ( self.sort_index() > -1 /*&& t.koapp != null*/ ) {				
				//get sort method
				var sort_method = t.sort_methots[t.opts.headers[self.sort_index()].sort.type];
				if ( sort_method ) {					
					t.scope.sort(function(a,b){
						var a = a.value[self.sort_index()], b = b.value[self.sort_index()];
						return self.sort_ascending() ? sort_method.asc(a,b) : sort_method.desc(a,b);
					});
				};
			};	    	    
		     
			//search in scope and create aux array
			var aux_data = [],
				len = t.scope.length,
				str = self.search_str().toLowerCase();

			//clear full scope
			while ( self.full_scope.length > 0 ) self.full_scope.pop();			
			self.full_scope = [];
				
			for ( var i = 0; i < len; i++ ) {				  	
				if ( str == '' ) { 
					aux_data.push( t.scope[i].data );
					self.full_scope.push( t.scope[i].data );
				} else {
					for ( var j = 0; j < t.opts.headers.length; j++ ) {						
						if ( t.scope[i].search[j].indexOf(str) >= 0 ) {
							aux_data.push( t.scope[i].data );
							self.full_scope.push( t.scope[i].data );
							break;
						}
					}
				}
			};

			//set full scope length
			self.count( aux_data.length );						
			
			while ( aux_data.length > self.max_rows() ) aux_data.pop();			

			//set scope
			self.scope( aux_data );							 
			
			t.trigger_update();
				  
		};
	    
	    self.do_search = ko.computed(function(){ 
	    	
			//if the search word was changed reset max rows
			if ( self.search_str() != t.old_search_str ) 
				self.max_rows(t.max_rows);
			t.old_search_str = self.search_str();
				
			self.search_func(); 
	    }, self);
			
		//display number of rows in the current view
		self.show_count = ko.computed(function() {			
			return self.count()+( self.count() == t.scope.length ? '' : ' / '+t.scope.length );
		}, self );
		
		//set sort index and reset the table		
		self.sort = function(header, event){
			if ( self.sort_index() == header.index ) { //if index is the same just change direction				
				if ( self.sort_ascending() ) {
					//descending sort
					self.sort_ascending( false );
				} else {
					//remove sorting
					self.sort_index( -1 );
					self.sort_ascending( true );
				}
			} else {
				//ascending sort
				self.sort_index( header.index );
				self.sort_ascending( true );
			}
			
			//reset max rows
			self.max_rows(t.max_rows);
			
			self.search_func();
		};
    
	    //check for curret sort index
	    self.is_sort_index = ko.computed(function() {
	    	return self.sort_index();
	    }, self);
    
	    //check for current sort direction
	    self.is_sort_ascending = ko.computed(function() {
	    	return self.sort_ascending();
	    }, self);

		self.remove_entry = function( callback ) {
	    	self.scope.remove( callback );
	    		
	    	//update scope length
	    	self.count( self.scope().length );

	    	//trigger update
	    	t.trigger_update();
	    };
    
    };		
	
	//total scope length
	t.total = 0;

	//number rows to display. In moset cases is the same as total, but on search is the number of find elements
	t.rows = 0;

	//trigger update event	
	t.trigger_update = function() {		
		if ( t.koapp ) {			
			t.total = t.scope.length;
			t.rows = t.koapp.count();
			t.$c.trigger( jQuery.Event( 'update', { rows: t.rows, total: t.total } ) );
			
			//remove loading
			t.koapp.loading(false);
		}
	};
	
	//load scope
	t.ajax_obj = null;		
	t.load_scope = function() {
		t.abort_load_scope();
		t.koapp.loading(true);				
		t.ajax_obj = t.ajax({
	        type: "GET",
	        url: t.scope_url,
	        dataType: "json",
	        success: function (data) {
	        	//set scope data
	        	t.set_scope_data(data);
	        },
	        error: function (error) {
				alert( o3_lang_(O3_ERR_GENERAL) );
				t.trigger_update();
	        }
	    });
	};

	//stop scope loading
	t.abort_load_scope = function() {
		t.koapp.loading(false);
		if ( t.ajax_obj && t.ajax_obj.abort )
			t.ajax_obj.abort();
	};

	//get scope data
	t.get_scope = function() {
		return t.koapp.full_scope;
	};

	//set scope data
	t.set_scope_data = function ( scope ) {
		
		//save original
		t.original_scope = scope;

		//clear scope copy
		while ( t.scope.length > 0 ) t.scope.pop();
		
		var scope_len = scope.length, j = 0;		
		for ( j in scope )
		 	t.scope.push( { search: [],
							value: [],
							index: j,
							data: scope[j] } );
	  
		for ( var i = 0; i < t.opts.headers.length; i++ ) {
			//update scope search and value
			for ( var j = 0; j < scope_len; j++ ) {
				var content = t.p_content( t.scope[j].data, t.opts.headers[i].content ),
					value = t.opts.headers[i].content != t.opts.headers[i].value ? t.p_content( t.scope[j].data, t.opts.headers[i].value ) : content;
				t.scope[j].search.push( content ? content.toString().toLowerCase() : '' );
				t.scope[j].value.push( value ? value : '' );
			}
		}

		//update table
		if ( t.koapp )
			t.koapp.search_func();
	  
	};

	//init table display
	t.init = function() {				
	  				
		//create search input and row count display
		jQuery('<div class="o3_table_top_bar"><input type="text" value="" placeholder="'+t.opts.text_search_placeholder+'" data-bind="value: search_str, valueUpdate: \'keyup\'" class="o3_table_search_input"><div data-bind="{ html: show_count() }" class="o3_table_rows_count"></div></div>').appendTo(  t.$c  );	
		
		//create main table
		var buffer = '<table class="o3_table_table" data-bind="o3_fadeVisible: scope().length > 0">';
			
		//create headers
		buffer += '<tr class="o3_table_head_row" data-bind="foreach: header"><th class="o3_table_head_cell"><div class="o3_table_head_text" data-bind="html: label, css: { o3_table_head_sort: sort.allow, o3_table_head_sort_asc: $parent.is_sort_index() == index && $parent.is_sort_ascending(), o3_table_head_sort_desc: $parent.is_sort_index() == index && !$parent.is_sort_ascending() }, click: sort.allow ? $parent.sort : function(){}"></div></th>';
		var buffer_scope = '<tbody data-bind="foreach: scope"><tr class="o3_table_row">';
		for ( var i = 0; i < t.opts.headers.length; i++ ) {
			t.opts.headers[i] = jQuery.extend({
									index: i,
									content: '',
									value: t.opts.headers[i].content,									
									label: ''																																						
						    }, t.opts.headers[i] );	
			t.opts.headers[i].sort = jQuery.extend( { allow: true, type: 'text', is_default: false, ascending: true }, t.opts.headers[i].sort );
			//set is_default order
			if ( t.opts.headers[i].sort.is_default === true ) {
				t.defaut_sort_index = i;
				t.defaut_sort_ascending = t.opts.headers[i].sort.ascending;
			}						
			
			//data
			buffer_scope += '<td class="o3_table_cell" data-bind="html: '+t.opts.headers[i].content+'"></td>';
			
		}
		buffer_scope += '</tr>';
		buffer += '</tr>'+buffer_scope+'</tbody></table>';
		
		t.$main_table = jQuery(buffer).appendTo( t.$c );
							
		//create messages
		jQuery('<p class="o3_table_text o3_table_empty_search_result" data-bind="o3_fadeVisible: scope().length == 0 && search_str().length > 0 && !loading()">'+t.opts.text_empty_search_result+'</p>').appendTo(  t.$c  );
		jQuery('<p class="o3_table_text o3_table_empty_table" data-bind="o3_fadeVisible: scope().length == 0 && search_str().length == 0 && !loading() ">'+t.opts.text_empty_table+'</p>').appendTo(  t.$c  );
		jQuery('<p class="o3_table_text o3_table_loading" data-bind="o3_fadeVisible: scope().length != count() || loading()"><span class="o3_table_loader"></span> '+t.opts.text_loading+'</p>').appendTo(  t.$c  );

		//store scope url
		if ( typeof t.opts.scope == 'string' ) {
			t.scope_url = t.opts.scope;
			t.opts.scope = []; 
		} else {	
			//create scope copy
			t.set_scope_data( t.opts.scope );
		}
		
		//set update callback if not null
		if ( t.opts.onUpdate )
			t.$c.bind( 'update', t.opts.onUpdate );		
		
		//apply knockout bindings
		t.koapp = new t.appview();		
		ko.applyBindings( t.koapp, t.$c.get(0) );
		
		//load scope url if needed
		if ( t.scope_url != '' ) {
			t.load_scope( t.scope_url );
		} else {
			//trigger updated on init if no scope url 
			t.trigger_update();
		}
		
		//check scrolling/resize/touchmove
		jQuery(window).scroll(function(){t.check_position()});
		jQuery(window).resize(function(){t.check_position()});						
		if ( typeof window.addEventListener == 'function' )
			window.addEventListener( "touchmove", function() { t.check_position() }, false );
						
	};
	
	//generate table on initialization
	t.init();
	
	//check scroll position to show more rows if needed
	t.check_position = function() {
		//check only for scroll position if existing not displayed rows
		if ( t.koapp.scope().length != t.koapp.count() ) {
			var o = t.$main_table.offset(),
				h = t.$main_table.height();

			//add rows if scroll is reached the end of table
			if ( jQuery(window).scrollTop() + jQuery(window).height() > o.top + h )
				t.koapp.max_rows( t.koapp.max_rows() + t.max_rows );			
		}		
	};
	
	//PUBLIC FUNCTIONS
	
	/**
	* Set a new scope data
	* @param scope array JSON data / string to json url
	*/
	t.setScope = function( scope ) {
		if ( typeof scope == 'string' ) {
			//store scope url						
			t.scope_url = scope/*t.opts.scope*/;
			t.opts.scope = [];
			t.load_scope( t.scope_url );
		} else {				
			t.set_scope_data( scope );
		}
	};	

	/**
	* Remove row
	* @param callback function
	*/
	t.removeFromScope = function( callback ) {

		//remove from scope buffer 
		for ( var i = 0; i < t.scope.length; i++ ) {
			if ( callback( t.scope[i].data ) ) {
				t.scope.splice( i, 1 );
			}
		}

		//remove from app view
		t.koapp.remove_entry( callback );		

	};
	
	/**
	* Update row
	* @param callback function
	* @param data
	* @param replace the data or extend. Default is true.
	*/
	t.updateInScope = function( callback, data, replace ) {
		var original_data = data;
		replace = typeof replace == 'undefined' ? true : false;

		//remove from scope buffer 
		for ( var i = 0; i < t.scope.length; i++ ) {
			if ( callback( t.scope[i].data ) ) {
				if ( !replace ) 
					data = jQuery.extend( t.scope[i].data, original_data );				

				//t.scope[i].data = data;		
				t.scope[i] = { search: [],
							   value: [],
							   index: i,
							   data: data };
			  
				for ( var j = 0; j < t.opts.headers.length; j++ ) {
					//update scope search and value
					var content = t.p_content( t.scope[i].data, t.opts.headers[j].content ),
						value = t.opts.headers[j].content != t.opts.headers[j].value ? t.p_content( t.scope[i].data, t.opts.headers[j].value ) : content;						
					t.scope[i].search.push( content ? content.toString().toLowerCase() : '' );
					t.scope[i].value.push( value ? value : '' );
				}

			}
		}

		//remove from app view
		t.koapp.search_func();	

	};

	/**
	* Insert row
	* @param callback function
	*/
	t.insertInScope = function( data ) {

		var i = t.scope.length;
		t.scope.push( { search: [],
					   value: [],
					   index: i,
					   data: data } );
	  
		for ( var j = 0; j < t.opts.headers.length; j++ ) {
			//update scope search and value
			var content = t.p_content( t.scope[i].data, t.opts.headers[j].content ),
				value = t.opts.headers[j].content != t.opts.headers[j].value ? t.p_content( t.scope[i].data, t.opts.headers[j].value ) : content;
			t.scope[i].search.push( content ? content.toString().toLowerCase() : '' );
			t.scope[i].value.push( value ? value : '' );					
		}

		//remove from app view
		t.koapp.search_func();	

	};

};

o3_table_text = { text_search_placeholder: 'Type here to search...',
				  text_empty_search_result: 'No items match your search.',
				  text_empty_table: 'There are no items to show.',
				  text_loading: 'Loading...' };

o3_table_sort_methots = {}; 

//add text sorting method
o3_table_sort_methots.text = { asc: function( a, b ){
										a = a ? a.toString().toLowerCase() : '';
										b = b ? b.toString().toLowerCase() : '';				
							  			return a < b ? -1 : a > b ? 1 : a == b ? 0 : 0;								          
									},		      
							   desc: function( a, b ){
										a = a ? a.toString().toLowerCase() : '';
										b = b ? b.toString().toLowerCase() : '';
										return a > b ? -1 : a < b ? 1 : a == b ? 0 : 0;								          
								    } 
							};

//add text sorting method
o3_table_sort_methots.number = { asc: function( a, b ){
										a = a ? parseInt(a) : 5e-324;
										b = b ? parseInt(b) : 5e-324;			
									    return a < b ? -1 : a > b ? 1 : a == b ? 0 : 0;								          
							      	},		      
								 desc: function( a, b ){
										a = a ? parseInt(a) : 5e-324;
										b = b ? parseInt(b) : 5e-324;
										return a > b ? -1 : a < b ? 1 : a == b ? 0 : 0;								          
					      			} 
							   };
o3_table_ajax = o3_ajax;


/**
* jQuery function, not chainable
*
* @return o3_table object
*/
jQuery.fn.o3_table = function( opts ) {
	
	//if none or more than 1 object selected return false
	if ( jQuery(this).length == 1 ) {
		//set container
		opts.container = jQuery(this).get(0);		
		//create o3 table property
		return (new o3_table( opts ));
	}
	return false;
	
};