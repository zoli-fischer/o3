/*
* Knockout validators
*/

//validation
var o3_form_validators = {			
	'float': 'return o3_valid_float( value )',
	'number': 'return o3_valid_number( value )',
	'email': 'return o3_valid_email( value )',
	'password': 'return o3_valid_password( value )'
};

/*
* Create knockout observables for o3 form
* appview to set the observables
*/
function o3_form_init( $form, appview, validators, defvalue ) {	
	var id = $form.attr('id'),
		action = $form.attr('o3-form-action'),
		method = $form.attr('o3-form-method');
	
	if ( $form.get(0).tagName == 'FORM' && jQuery.trim(id) != '' ) {

		//list of inputs
		if ( typeof appview[id] == 'undefined' )
			appview[id] = {};
		if ( typeof appview[id].field == 'undefined' )
			appview[id].field = {};
		
		//submit action
		appview[id].action = typeof action == 'undefined' || jQuery.trim(action) == '' ? window.location : jQuery.trim(action);
		//submit method
		appview[id].method = typeof method == 'undefined' || jQuery.trim(method) == '' ? 'GET' : jQuery.trim(method);		
		//form id
		appview[id].id = id;
		//form jquery object
		appview[id].$form = $form;

		//create loading handle
		appview[id].loading = ko.observable(false);	

		//create error handle
		appview[id].error = ko.observable('');
		//create set error
		appview[id].set_error = function(text) { 
			//hide loading on error
			appview[id].loading( false );
			//show loading msg
			appview[id].error(text); 
		};

		//create error handle
		appview[id].success = ko.observable('');
		appview[id].success_timeout = null;
		//create set error
		appview[id].set_success = function(text) { 
			//hide loading on error
			appview[id].loading( false );
			//show loading msg
			appview[id].success(text); 
			//set hide timeout
			if ( text != '' ) {
				appview[id].success_timeout = setTimeout(function() {
					appview[id].set_success('');
				}, 4000 );
			} else {
				clearTimeout( appview[id].success_timeout );
			};
		};

		//create validator function
		appview[id].validate = function() { return o3_form_validate(appview[id]) };

		//hide/show validation errors
		appview[id].show_errors = function( value ) { return o3_form_show_errors( appview[id], value ) };
		
		//create enable/disable all fields
		appview[id].disable = function(value) { o3_form_disable(appview[id],value) };

		//create submit
		appview[id].submit = function() { return o3_form_submit(appview[id]) };

		//set custom event handlers to null
		appview[id].on_before_submit = appview[id].on_success = appview[id].on_error = appview[id].on_fail = null;

		//delay submit, without delay the data can return to fast and no loading is showed. This number needs to be between 250 - 500 for a natural loading show
		appview[id].ajax_delay = 500;

		//security code set / get
		appview[id].security_code = function( value ) { if ( typeof value == 'undefined' ) { return $form.attr('o3-form-code') } else { $form.attr('o3-form-code',value) }; };				

		//user define submit data
		appview[id].submit_data = function() { return {}; };
		//subimt data 
		appview[id].submit_data_original = function() {
			var data = {};
			if ( typeof appview[id].field != 'undefined' ) {		
				for( var prop in appview[id].field ){
					data[prop] = appview[id].field[prop]();
				};
			};			
			return data;
		};

		//validation
		appview[id].type_validator = jQuery.extend( o3_form_validators, validators );		

		//default values
		appview[id].type_defvalue = {
			checkbox: function( $t, value ) { return $t.get(0).checked ? value : '' }
		};
		//todo: extend defvalue
	
		//search for input fields
		$form.find('input, textarea, select, *[o3-form-type="radio"]').each(function() {
			var $t = jQuery(this),
				ref_type = typeof $t.attr('o3-form-type') == 'undefined' ? 'text' : $t.attr('o3-form-type');			

			if ( ref_type == 'radio' || ref_type == 'file' || ( typeof $t.attr('data-bind') != 'undefined' && $t.attr('type') != 'submit' && $t.attr('type') != 'button' ) ) {

				var mandatory = typeof $t.attr('o3-form-mandatory') == 'undefined' ? false : true,
					validator = typeof $t.attr('o3-form-validator') == 'undefined' ? '' : $t.attr('o3-form-validator'),
					validate_func = typeof appview[id].type_validator[validator] != 'undefined' ? appview[id].type_validator[validator] : '',
					value = typeof appview[id].type_defvalue[ref_type] != 'undefined' ? appview[id].type_defvalue[ref_type]( $t, $t.attr('value') ) : $t.attr('value');

				//ignore if type is ignore
				if ( ref_type != 'ignore' && ref_type != 'radio-node' ) {
					appview[id].field[$t.attr('id')] = ko.observable(value);
					if ( mandatory || validate_func != '' || validator != '' ) {
						
						//update validate function
						if ( validate_func != '' )
							validate_func = ' function( value ) {'+( !mandatory ? ' if ( value == \'\' ) return true; ' : ' if ( value == \'\' ) return false; ' )+validate_func+'; }';

						//set validate function
						eval('o3_isValid( appview[id].field.'+$t.attr('id')+( validate_func != '' ? ', '+validate_func : '' )+ ' ); ');

					};
				};
			};

			//Create upload
			if ( ref_type == 'file' ) {

				/*
				var file = $t.val().split(':'),
					filename = file[1] ? file[1] : file[0];
				jQuery('#'+$t.attr('data-o3-upload-field')+'_result input').val( filename );
				*/

				//get filename function
				appview[id].field[$t.attr('id')].filename = function() {
					var parts = appview[id].field[$t.attr('id')]().split(O3_FORM_FILE_NAME_PATH_SEP);
					return parts[1] ? parts[1] : o3_basename(parts[0]);
				};

				//progress observable
				appview[id].field[$t.attr('id')].progress = ko.observable( -1 );

				//create upload obj
				appview[id].field[$t.attr('id')].o3ajaxupload = new o3_ajax_upload( {
								name: $t.attr('name'),
								sendonchange: true,
								action: appview[id].action,	
								maxfilesize: parseInt( jQuery('#'+$t.attr('data-o3-upload-field')).attr('data-o3-form-field-file-max-upload') ),							
								onsend: function() {
									if ( this.files[0] ) {
										appview[id].field[$t.attr('id')]( this.files[0].name );									
									};
								},
								onprogress: function( event ) {
									var percent = parseInt( ( event.loaded / event.total ) * 100 );
									appview[id].field[$t.attr('id')].progress( percent );
								},
								oncomplete: function( event ) {
									var error = true;
									if ( this.xhr.responseText ) {
										var response = jQuery.parseJSON(this.xhr.responseText);
										if ( response.data && 
											 response.data.filename && response.data.filename != '' &&
											 response.data.filepath && response.data.filepath != ''
										) {
											appview[id].field[$t.attr('id')]( response.data.filepath+O3_FORM_FILE_NAME_PATH_SEP+response.data.filename );
											error = false;
										};
									};
									appview[id].field[$t.attr('id')].progress( -1 );
								},
								onerror: function( event ) {									
									switch ( event.code ) {
										case this.failCodes.filesize:
											alert( sprintf( o3_lang_(O3_FORM_ERR_UPLOADING_SIZELIMIT), o3_bytes_display(this.opts.maxfilesize) ) );
											break;
										case this.failCodes.filetype:											
											alert( sprintf( o3_lang_(O3_FORM_ERR_UPLOADING_TYPE), this.opts.accept ) );									 		
											break;
										default:
											alert( o3_lang_(O3_FORM_ERR_UPLOADING_GENERAL) );
											break;
									};
								}
							}, jQuery('#'+$t.attr('data-o3-upload-field')).get(0) );
								
				//clear
				appview[id].field[$t.attr('id')].clear = function() {
					appview[id].field[$t.attr('id')].progress( -1 );
					//remove filename
					appview[id].field[$t.attr('id')]('');
				};

				//abort
				appview[id].field[$t.attr('id')].abort = function() {
					appview[id].field[$t.attr('id')].o3ajaxupload.abort();
					appview[id].field[$t.attr('id')].progress( -1 );
					//set back old filename filename
					appview[id].field[$t.attr('id')]('');
				};

				//create cancel/remove function
				appview[id].field[$t.attr('id')].cancel = function() {
					if ( appview[id].field[$t.attr('id')].o3ajaxupload.status == 'sending' ) {
						appview[id].field[$t.attr('id')].abort();
					} else {
						appview[id].field[$t.attr('id')].clear();
					};
				};

				//set path/filename from external script
				appview[id].field[$t.attr('id')].set = function( path, name ) {
					//stop upload
					appview[id].field[$t.attr('id')].cancel();

					//create value
					var arr = [];
					arr.push( path ); //add path
					if ( typeof name != 'undefined' && jQuery.trim(name).length > 0 )
						arr.push( name ); //add filename

					appview[id].field[$t.attr('id')]( arr.join( O3_FORM_FILE_NAME_PATH_SEP ) );
				};

			};


		});	

		//file upload
		/*
		$form.find('.o3-form-field-file-button div').each(function() {
			
			upclick({
				element: jQuery(this).get(0),
				action: window.location,
				dataname: "file",
				onstart: function( filename ) {
					alert(filename);
				},       
				oncomplete: function(response_data) {		
				 	alert(response_data);
				}
			});	

		});
		*/

		return appview[id];
	};
};

/*
* Submit form
*/
function o3_form_submit( appview_form ) {
	if ( appview_form.validate() ) {
		
		if ( appview_form.on_before_submit && typeof appview_form.on_before_submit == 'function' )	
			appview_form.on_before_submit();

		//hide error
		appview_form.error(''); 

		//show loading
		appview_form.loading(true);
		
		var data = {};
		data[appview_form.id] = appview_form.submit_data_original();
		data['o3-form-code'] = appview_form.security_code();		
		data = jQuery.extend( data, typeof appview_form.submit_data === 'function' ? appview_form.submit_data() : appview_form.submit_data );
		data[appview_form.id] = JSON.stringify(data[appview_form.id]);
			
		if ( appview_form.ajax )
			appview_form.ajax.abort();

		//post data
		function run() {
			appview_form.ajax = o3_ajax({
		        type: appview_form.method,
		        url: appview_form.action,
		        data: data,
		       	dataType: "json",
		        success: function (data) {	        	

		        	appview_form.security_code( data['o3-form-code'] );

		        	//redirect if needed
		        	if ( typeof data.redirect != 'undefined' && data.redirect != '' ) {
						window.location = data.redirect;					
		        	} else {
						
						//check if request was error or success and call user functions
			        	if ( typeof data.success != 'undefined' && data.success !== false ) {
							//clear error msg
			        		appview_form.set_error('');

			        		//set success msg
			        		if ( data.success_msg )
			        			appview_form.set_success(data.success_msg);

			        		if ( appview_form.on_success && typeof appview_form.on_success == 'function' )	
			        			appview_form.on_success( data );
			        	} else {

			        		//clear success msg
			        		appview_form.set_success('');
			        		
							//show error
							if ( data.error_msg )
						   		appview_form.set_error( data.error_msg );

							if ( appview_form.on_error && typeof appview_form.on_error == 'function' )	
			        			appview_form.on_error( data );
			        	}; 
			        };
		        
		        },
		        error: function (xhr, text_status, error_thrown) {
		        	console.log('AJAX Status: '+text_status);
		            if ( text_status == 'parsererror' ) console.log('AJAX Response: '+xhr.responseText);
		            console.log('AJAX Error: '+error_thrown);
		        	if ( text_status != 'abort' ) {
		        		if ( appview_form.on_fail && typeof appview_form.on_fail == 'function' )
		        			appview_form.on_fail( xhr, text_status, error_thrown );		        	
						appview_form.set_error( typeof o3_lang != 'undefined' ? o3_lang._(O3_ERR_GENERAL) : O3_ERR_GENERAL  );
					};
		        }
		    });	
		};

		//check if delay needed
		if ( appview_form.ajax_delay > 0 ) {
			setTimeout(function() {			
				run();
			}, appview_form.ajax_delay );
		} else {
			run();
		};

	};
};

/*
* Validate form
*/
function o3_form_validate( appview_form ) {
	var error = false;
	if ( typeof appview_form.field != 'undefined' ) {		
		for( var prop in appview_form.field ) {
			if ( appview_form.field[prop].o3_isValid ) {
				if ( !appview_form.field[prop].o3_isValid() ) {					
					appview_form.field[prop].o3_showError( true );
					error = true;
				};
			};
			//upload
			if ( !error && appview_form.field[prop].o3ajaxupload && appview_form.field[prop].o3ajaxupload.status == 'sending' ) {
				alert(o3_lang_(O3_FORM_ERR_UPLOADING_WAIT_SUBMIT));
				error = true;
			};
		};
		return !error;
	} else {
		console.log('Knockoutjs error! Appview.input not found.');
	};
	return false;
};

/*
* Show/hide validation errors
*/
function o3_form_show_errors( appview_form, value ) {
	if ( typeof appview_form.field != 'undefined' )	
		for( var prop in appview_form.field ) {
			if ( appview_form.field[prop].o3_isValid ) {
				if ( !appview_form.field[prop].o3_isValid() ) {					
					appview_form.field[prop].o3_showError( value );
				};
			};
		};
};

/*
* Disable/enable form
*/
function o3_form_disable( appview_form, value ) {
	if ( typeof appview_form.$form != 'undefined' ) {		
		appview_form.$form.find('input, select, textarea').attr('disabled',value);			
	} else {
		console.log('Knockoutjs error! Appview.$form not found.');
	};
};


//Autoloaded forms
var o3_form_autoload_forms = [];

/*
* Get autoloaded o3 form
*/ 
function o3_form( name ) {
	return o3_form_autoload_forms[name] ? o3_form_autoload_forms[name].form : false;
};

/*
* Get autoloaded o3 form app
*/ 
function o3_form_app( name ) {	
	return o3_form_autoload_forms[name] ? o3_form_autoload_forms[name] : false;
};

/*
* O3 form knockout view app
*/
function o3_formAppView( $form ) {
	this.form = o3_form_init( $form, this );	
};

/*
* Autoload form
*/ 
function o3_form_autoload() {
	if ( jQuery && ko ) {
		var forms = jQuery('form[o3-form-autoload]');
		forms.each(function(){
			var $t = jQuery(this);
			if ( $t.attr('o3-form-autoload') == 'true' && ( typeof $t.attr('o3-form-loaded') == 'undefined' || $t.attr('o3-form-loaded') == 'false' ) ) {
				o3_form_autoload_forms[$t.attr('id')] = new o3_formAppView( $t );
				ko.applyBindings( o3_form_autoload_forms[$t.attr('id')], $t.get(0) );				
				$t.attr('o3-form-loaded','true');
			};
		});
	};
};
window.attachEvent ? window.attachEvent( 'onload', function(){ o3_form_autoload() } ) : window.addEventListener( 'load', function(){ o3_form_autoload() } );

/*
* O3 pop extension
*/
//alert	
o3_popup_form = function( title, template, on_success, on_error, opts, cancelLabel, submitLabel ) {	
	var self = this;
	self.pop = null;
	self.form_app = null;

	self.cancelLabel = typeof cancelLabel == 'undefined' ? o3_lang_('Cancel') : cancelLabel;
	self.submitLabel = typeof submitLabel == 'undefined' ? o3_lang_('OK') : submitLabel;

	function on_before_submit() {
	
		if ( typeof opts.on_before_submit == 'function' ) {			
			var result = opts.on_before_submit();
			if ( typeof result != 'undefined' )
				return result;
		};

		//show loading
		self.pop.showLoad();

		//disable pop
		self.pop.disable();

		self.form_app.form.on_success = function(data) {

			//show loading
			self.pop.close();

			//show message
			if ( window.o3_popnote ) 
				window.o3_popnote.push( data.success_msg );
		
			if ( typeof on_success == 'function' ) 
				on_success( data );

		};

		self.form_app.form.on_fail = self.form_app.form.on_error = function(data) {			

			/** show error */
			var error_pop = new o3_popup_alert(
										o3_lang_('Error'), 
										o3_lang_( data && data.error_msg ? data.error_msg : O3_ERR_GENERAL ),
										{ width: '340px',
										  height: '160px'
										}, o3_lang._('OK') ).show();
			//show loading
			self.pop.showLoad(false);

			//disable pop
			self.pop.disable(false);

			if ( typeof on_error == 'function' ) 
				on_error( data );
		};
		
	};

	//options
	self.opts = jQuery.extend({ width: 520,
						height: 360,
						onbeforeclose: function() {},
						onafterload: function() {
							var t = this, $form = t.$body_container.find('form[o3-form-type="form"]');
							if ( $form.length > 0 ) {
								self.form_app = new o3_formAppView( $form );
								ko.applyBindings( self.form_app, $form.get(0) );	
								self.form_app.form.on_before_submit = on_before_submit;
							} else {
								/** show error */
								new o3_popup_alert(
										o3_lang_('Error'), 
										o3_lang_( O3_ERR_GENERAL ),
										{ width: '340px',
										  height: '160px'
										}, o3_lang._('OK') ).show();
								self.pop.close();
							};
						},
						header: {
							title: title
						},
						body: {
							type: 'url',
							src: template
						},
						footer: {
							content: [								
								{
									type: 'submit',
									title: self.submitLabel,
									position: 'right',
									focused: true,
									tabindex: 11,
									onclick: function( event, pop ) {
										self.form_app.form.submit();
									}
								},
								{
									type: 'button',
									title: self.cancelLabel,
									position: 'right',
									tabindex: 10,
									onclick: function( event, pop ) { 
										pop.close();
									}
								}
						]}
					  }, opts );	

	self.create = function() {
		return self.pop = new o3_popup( self.opts );
	};

	//show
	self.show = function() {		
		if ( self.pop === null )
			self.create();
		self.pop.show();
		return self.pop;
	};
	
	return self;
};