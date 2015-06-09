var ImageCropper = (function(ns, jq, undefined) {
    'use strict';

    ns.element = null;
    ns.options = {

        zoomable: false,
        aspectRatio: 4 / 3
    };

    ns.initialize = function(selector, options) {

        ns.element = jq(selector);
        ns.options = jq.extend(ns.options, options);

        return ns;
    };

    ns.events = {

        create: function(image, data) {

            return ns.element.attr('src', image.attr('src'))
                             .data('id', image.data('id'))
                             .cropper(jq.extend(ns.options, {data: data}));
        },

        destroy: function() {
            return ns.element.cropper('destroy').removeAttr('src');
        },

        save: function() {
            return {'id': ns.element.data('id'), 'crop': ns.element.cropper('getData')};
        }
    };

    return ns;

}(ImageCropper || {}, jQuery));