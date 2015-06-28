/*
* Turn input[type=file] to uploader
*/
o3_ajax_upload = function( opts, container ) {

	var self = this;
	
	//for compatibility mode
	var $ = jQuery;

	//container
	self.$container = $(container); 
	self.container = self.$container.get(0);

	//XMLHttpRequest
	self.xhr = null;

	//options
	self.opts = $.extend( {
		name: typeof self.$container.attr('name') != 'undefined' ? self.$container.attr('name') : 'file',
		accept: typeof self.$container.attr('accept') != 'undefined' ? self.$container.attr('accept') : '',
		multiple: typeof self.$container.attr('multiple') != 'undefined' && self.$container.attr('multiple') !== false ? true : false,
		action: typeof self.$container.attr('action') != 'undefined' ? self.$container.attr('action') : '',
		ignorefiletype: false, //true - don't check file type before upload
		ignorefilesize: false, //true - don't check file size before upload
		maxfilesize: 0,
		sendonchange: false,
		onsend: null,
		onprogress: null,
		onload: null,
		onerror: null,
		onabort: null,
		oncomplete: null,
		onfail: null
	}, opts );

	self.status = ''; //sending, completed, failed

	//0 - no fail, 
	//1 - file size bigger than allowed upload size
	//2 - file type not accepted
	//10 - general fail
	self.failCode = 0;

	//list of fail/error codes
	self.failCodes = {
		empty: 0,
		filesize: 1,
		filetype: 2,
		general: 10
	};

	//on upload progress
	self.onprogress = function( event ) {
		if ( event.lengthComputable ) {
			if ( self.opts.onprogress !== null )
				self.opts.onprogress.call( self, event );
		};
	};

	//on load
	self.onload = function( event ) {
		//alert(self.xhr.responseText);
		if ( self.xhr.status === 200 ) {
			self.oncomplete( event );
		} else {
			self.onfail( self.failCodes.general, event );
		}

		if ( self.opts.onload !== null )
			self.opts.onload.call( self, event );		
	};

	//on upload failed / error
	self.onfail = function( failCode, event ) {
		self.status = 'failed';
		if ( self.opts.onerror !== null )
			self.opts.onerror.call( self, { code: failCode, event: event } );		
	};

	//on upload complete
	self.oncomplete = function( event ) {
		self.status = 'completed';
		if ( self.opts.oncomplete !== null )
			self.opts.oncomplete.call( self, event );		
	};

	//on abort
	self.onabort = function( event ) {
		self.status = '';
		if ( self.opts.onabort !== null )
			self.opts.onabort.call( self, event );		
	};

	//on send
	self.onsend = function() {
		self.status = 'sending';
		if ( self.opts.onsend !== null )
			self.opts.onsend.call( self );		
	};

	/** Constructor */
	self.constructor__ = function() {

		//set option attr
		self.$container.attr('accept',self.opts.accept);
		if ( self.opts.multiple === false ) {
			self.$container.removeAttr('multiple');
		} else {
			self.$container.attr('multiple','multiple');
		};

		//add on change
		if ( self.opts.sendonchange )
			self.$container.bind( 'change', function() {
				//send on change
				self.send();
				//clear field
				self.$container.val('');
			});

		if ( typeof XMLHttpRequest != 'undefined' ) {

			//Set up the request
			self.xhr = new XMLHttpRequest();

			//Set up a upload progress
			if ( self.xhr.upload ) {
				self.xhr.upload.onprogress = self.onprogress;
			} else {
				self.xhr.onprogress = self.onprogress;
			}

			//Set up a handler for when the request finishes
			self.xhr.onload = self.onload;

			//Set up error handler
			self.xhr.onerror = self.onerror;

			//Set up abort handler
			self.xhr.onabort = self.onabort;


		} else {
			console.log('O3 Ajax Upload: XMLHttpRequest is missing!');
		};

	};
	self.constructor__();

	//abort upload
	self.abort = function() {
		self.xhr.abort();
	};

	//files
	self.files = [];

	//sent the request
	self.send = function() {
		if ( typeof FormData != 'undefined' ) {
			//Get the selected files from the input
			self.files = self.container.files;

			if ( self.files.length > 0 ) {
				//Create a new FormData object
				var formData = new FormData();

				//Loop through each of the selected files
				for (var i = 0; i < self.files.length; i++) {
					var file = self.files[i];

					//Check the file type
					if ( self.opts.ignorefiletype === false ) {
						if ( self.opts.accept != '' && file.type != '' ) {
							if ( self.opts.accept.match(file.type) === null ) {
								self.onfail( self.failCodes.filetype );
								return;
							};
						};
					};

					//Check file size
					if ( self.opts.ignorefilesize === false ) {
						if ( file.size > self.opts.maxfilesize && self.opts.maxfilesize > 0 ) {
							self.onfail( self.failCodes.filesize );
							return;
						};
					};

					// Add the file to the request
					formData.append( self.opts.name+'[]', file, file.name );
				};

				//Open the connection
				self.xhr.open( 'POST', self.opts.action == '' ? window.location : self.opts.action, true );
				
				//Send the data
				self.xhr.send( formData );

				//trigger onsend event
				self.onsend();
			};

		} else {
			console.log('O3 Ajax Upload: FormData is missing!');
		};
	};

};

//create console if not exist
console = typeof console != 'undefined' ? console : { log: function(){} };

/**
* Chainable jQuery function
*/
if ( typeof jQuery != 'undefined' ) {
	jQuery.fn.o3ajaxupload = function( opts ) {
		
		//check for containers	
		if ( jQuery(this).length == 0 )
			return console.log('O3 Ajax Upload: Container must not be empty!') || false;

		//return object with list of o3ajaxuploads
		var o3ajaxupload_array = {
			o3ajaxuploads: new Array(),
			send: function() {
				for (var i = 0;i<this.o3ajaxuploads.length;i++)
					this.o3ajaxuploads[i].send();
			},
			abort: function() {
				for (var i = 0;i<this.o3ajaxuploads.length;i++)
					this.o3ajaxuploads[i].abort();
			}
		};

		//create objects
		jQuery(this).each( function() {
			if ( typeof jQuery(this).get(0).o3ajaxupload == 'undefined' )
				jQuery(this).get(0).o3ajaxupload = new o3_ajax_upload( opts, this );
			o3ajaxupload_array.o3ajaxuploads.push( jQuery(this).get(0).o3ajaxupload );
		});	
		
		return o3ajaxupload_array;
	};
} else {
	console.log('O3 Uplclick: jQuery missing!');
}