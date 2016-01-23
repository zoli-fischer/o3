/**
* Knockout extensions & bindings
*/

//knockout observable to object
function o3_KO2JS( data ) {
    var obj = data.constructor.toString().indexOf("Array") > -1 ? [] : {};
    for( var key in data ) {        
        if ( typeof data[key] == 'function' ) {
            obj[key] = data[key]();            
        } else {
            obj[key] = data[key];
        }
    }
    return obj;
};

//knockout observable to JSON
function o3_KO2JSON( data ) {
    return ko.toJSON( o3_KO2JS( data ) );
};

//make object's chidren observable
function o3_make_children_observables( object ) {
    var obj = !ko.isObservable(object) ? object : object();    

    // Loop through its children
    for ( var child in obj ) {
        if ( !ko.isObservable(obj[child]) ) {                        
            obj[child] = ko.observable( typeof obj[child] != 'object' ? obj[child] : o3_make_children_observables(obj[child]) );
        };
    };

    return obj;
};

//create swap 2 items in observable array
if ( ko && !ko.observableArray.fn.swap ) {
    ko.observableArray.fn.swap = function( index1, index2 ) {
        this.valueWillMutate();

        //swap with use of a temp var
        var temp = this()[index1];
        this()[index1] = this()[index2];
        this()[index2] = temp;

        this.valueHasMutated();
    };
};

// Custom Knockout binding that makes elements shown/hidden via jQuery's fadeIn()/fadeOut() methods
ko.bindingHandlers.o3_fadeVisible = {
    init: function(element, valueAccessor) {        
        // Initially set the element to be instantly visible/hidden depending on the value
        var value = valueAccessor();
        jQuery(element).toggle(ko.unwrap(value)); // Use "unwrapObservable" so we can handle values that may or may not be observable
    },
    update: function(element, valueAccessor) {        
        // Whenever the value subsequently changes, slowly fade the element in or out
        var value = valueAccessor(),
            $element = jQuery(element);
        if ( $element.finish ) {
            $element.finish();
        } else {
            $element.stop( true, true );
        }
        ko.unwrap(value) ? $element.fadeIn() : $element.fadeOut();
    }
};

// Custom Knockout binding that makes elements shown/hidden via jQuery's slideIn()/slideOut() methods
ko.bindingHandlers.o3_slideVisible = {
    init: function(element, valueAccessor) {        
        // Initially set the element to be instantly visible/hidden depending on the value
        var value = valueAccessor();
        jQuery(element).toggle(ko.unwrap(value)); // Use "unwrapObservable" so we can handle values that may or may not be observable
    },
    update: function(element, valueAccessor) {        
        // Whenever the value subsequently changes, slowly slide the element in or out
        var value = valueAccessor(),
            $element = jQuery(element);
        if ( $element.finish ) {
            $element.finish();
        } else {
            $element.stop( true, true );
        }
        ko.unwrap(value) ? $element.slideDown() : $element.slideUp();
    }
};

// Custom Knockout binding that makes elements shown/hidden via jQuery's toggle() methods
// data-bind="o3_toggleVisible: { toggle: 'fade', visible: type() > 0, duration: 1000, complete: function() { alert('Complete') } }"
ko.bindingHandlers.o3_toggleVisible = {
    init: function(element, valueAccessor) {
        // Initially set the element to be instantly visible/hidden depending on the value
        var value = ko.unwrap(valueAccessor()), // Use "unwrapObservable" so we can handle values that may or may not be observable
            visible = false;
        switch ( typeof value ) {
            case 'object':
                visible = value.visible;
                break;
            case 'boolean':
                visible = value;
                break;
        }         
        jQuery(element).toggle( visible );
    },
    update: function(element, valueAccessor) {
        // Whenever the value subsequently changes, slowly fade the element in or out
        var value = ko.unwrap(valueAccessor()),
            $element = jQuery(element),
            display = $element.css('display'),
            visible = false,
            duration = 400,
            complete = function(){},
            toggle = ''; // empty string, slide, fade
        switch ( typeof value ) {
            case 'object':
                visible = value.visible;
                duration = typeof value.duration != 'undefined' ? value.duration : duration; 
                complete = typeof value.complete != 'undefined' ? value.complete : complete;
                toggle = typeof value.toggle != 'undefined' ? value.toggle : toggle;
                break;
            case 'boolean':
                visible = value;
                break;
        }

        if  ( ( visible && display == 'none' ) || ( !visible && display != 'none' ) ) {            
            if ( $element.finish ) {
                $element.finish();
            } else {
                $element.stop( true, true );
            }
            switch ( toggle ) {
                case 'fade':
                    $element.fadeToggle( duration, complete );  
                    break;
                case 'slide':
                    $element.slideToggle( duration, complete );  
                    break;
                default:
                    $element.toggle( duration, complete );  
                    break;
            }        
        }
    }
};

/*knokcoutjs extend & bindings*/
//http://stackoverflow.com/questions/16101763/input-lostfocus-onblur-event-in-knockout
ko.extenders.o3_isValid = function (target, validator) {
    //Use for tracking whether validation should be used
    //The validate binding will init this on blur, and clear it on focus
    //So that editing the field immediately clears errors
    target.isActive = ko.observable(false);

    if (typeof validator !== 'function') {
        validator = function(value) {
            return value !== undefined && value !== null && ( jQuery ? jQuery.trim(value) : value ).length > 0;
        };
    }

    target.o3_isValid = ko.computed(function () {
        return validator(target());
    });

    //Just a convienient wrapper to bind against for error displays
    //Will only show errors if validation is active AND invalid
    target.o3_showError = ko.computed( {
        read: function () {        
            //This intentionally does NOT short circuit, to establish dependency
            if ( target.isActive() )
                return !target.o3_isValid();           
            return false;
        },
        write: function( vaule ) {
            target.isActive( vaule );   
        }
    }).extend({ notify: 'always' });
    
    return target;
};

//extend a ko observable with is valid extension
function o3_isValid( item, validator ) {
    validator = typeof validator != 'undefined' ? validator : true;
    if ( item.o3_showError ) {
        item.isActive( false );
    } else {
        item.extend({ o3_isValid: validator });    
    };
}

//Just activate whatever observable is given to us on first blur
ko.bindingHandlers.o3_validate = {
    init: function (element, valueAccessor) {
        ko.bindingHandlers.value.init.apply(this, arguments); //Wrap value init
        //Active will remain false until we have left the field
        //Starting the input with validation errors is bad
        ko.utils.registerEventHandler(element, 'blur', function() {
            if ( valueAccessor().isActive )
                valueAccessor().isActive(true);
        });
        //Validation should turn off while we are in the field
        ko.utils.registerEventHandler(element, 'focus', function() {
            if ( valueAccessor().isActive )
                valueAccessor().isActive(false);
        });
    },
    update: ko.bindingHandlers.value.update //just wrap the update binding handler
};

//@link http://www.knockmeout.net/2012/05/quick-tip-skip-binding.html
//@link http://jsfiddle.net/rniemeyer/fjj7q/
//let KO know that we will take care of managing the bindings of our children
ko.bindingHandlers.o3_stopBinding = {
    init: function( element, valueAccessor ) {
        var value = ko.unwrap(valueAccessor());
        return { controlsDescendantBindings: value };
    }  
};
//KO 2.1, now allows you to add containerless support for custom bindings
ko.virtualElements.allowedBindings.o3_stopBinding = true;


//check for css class change event on element
// data-bind="o3_cssChange: { 'tab-list-selected': function( hasCss ) { show(hasCss) } }" - if tab-list-selected class is added/removed to dom element the event will be trigger. Event parameter true if the element has the class, false if not
// data-bind="o3_cssChange: function() { show() }" - if class attribute is changed on dom element the event will be trigger
ko.bindingHandlers.o3_cssChange = {
    init: function(element, valueAccessor) {
        var value = valueAccessor(),
            self = this,
            $element = jQuery(element),
            css = '',
            props = [];

        if ( typeof value == 'object' )
            for( var prop in value )
                props[prop] = $element.hasClass(prop);            

        //check for class change
        function checkCssChange() {            
            var element_css = $element.attr('class');
            element_css = jQuery.trim(typeof element_css == 'undefined' ? '' : element_css);            
            if ( element_css != css ) {
                css = element_css;                
                switch ( typeof value ) {
                    case 'function':
                        value();
                        break;
                    case 'object':
                        for( var prop in value ){
                            if ( props[prop] != $element.hasClass(prop) ) {
                                props[prop] = $element.hasClass(prop);
                                if ( typeof value[prop] == 'function' )
                                    value[prop]( props[prop] );
                            }                            
                        }
                        break;
                }   
            }
        }

        //24 frame per second
        setInterval( function() { checkCssChange() }, 33 );
    },
    update: ko.bindingHandlers.value.update //just wrap the update binding handler
};

//on field key press run function
ko.bindingHandlers.onenter = {
    init: function (element, valueAccessor, allBindings, viewModel) {
        var callback = valueAccessor();
        jQuery(element).keypress(function (event) {
            var keyCode = ( event.which ? event.which : event.keyCode );
            if ( keyCode === 13 ) {
                callback.call( viewModel );
                return false;
            }
            return true;
        });
    }
};