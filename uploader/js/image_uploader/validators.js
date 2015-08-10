var ImageUploader = (function(ns, jq, _, undefined) {
    'use strict';
    
    ns.validators = {
        
        mime: function(input, allowed) {
            return _.indexOf(allowed, input) !== -1;
        },
        
        size: function(input, allowed) {
            return  input < allowed;
        }
    };
    
    return ns;
    
}(ImageUploader || {}, jQuery, _));