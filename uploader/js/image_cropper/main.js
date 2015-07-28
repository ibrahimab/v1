var ImageCropper = (function(ns, jq, undefined) {
    'use strict';

    ns.element = null;
    ns.options = {

        zoomable: false,
        aspectRatio: 4 / 3,
        movable: false,
        rotatable: false,
        zoomable: false,
        mouseWheelZoom: false,
        touchDragZoom: false
    };

    ns.initialize = function(selector, options) {

        ns.hider   = jq('.preview .hide-container');
        ns.element = jq(selector);
        ns.options = jq.extend(ns.options, options);

        jq('body').on('click', '[data-role="close-cropper"]', function(event) {

            event.preventDefault();
            ns.events.destroy();
        });

        return ns;
    };

    ns.events = {

        create: function(image, data) {

            ns.hider.show();

            return ns.element.attr('src', image.attr('src'))
                             .data('id', image.data('id'))
                             .cropper(jq.extend(ns.options, {data: data}))
                             .show();
        },

        destroy: function() {

            ns.hider.hide();
            return ns.element.cropper('destroy').removeAttr('src').hide();
        },

        save: function() {
            return {'id': ns.element.data('id'), 'crop': ns.element.cropper('getData')};
        }
    };

    return ns;

}(ImageCropper || {}, jQuery));