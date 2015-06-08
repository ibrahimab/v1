var ImageUploader = (function(ns, jq, _, undefined) {
    'use strict';

    ns.events = {

        initialize: function() {

            ns.events.change();
            ns.events.dropzone();
            ns.events.upload.click();
            ns.events.croppers();
        },

        croppers: function() {

            jq('body').on('click', '[data-role="preview-box"]', function(event) {

                event.preventDefault();
                jq('[data-role="cropper"]').attr('src', event.target.src).cropper({
                    zoomable: false,
                    aspectRatio: 4 / 3
                });
            });
        },

        change: function() {

            jq('body').on('change', ns.input.selector, function(event) {

                var files = event.target.files;
                var total = files.length;
                var id;

                for (var i = 0; i < total; i++) {

                    id              = ns.id();
                    ns.files[id]    = files[i];
                    ns.files[id].id = id;
                }

                ns.views.preview();
            });
        },

        upload: {

            click: function() {

                jq('body').on('click', ns.submit.selector, function(event) {
                    ns.upload.all();
                });
            },

            progress: function(event, file, cb) {
                cb(file, Math.round(event.loaded / event.total * 100));
            }
        },

        dropzone:  function() {

            jq('body').on('dragenter', ns.input.selector, function(event) {

                event.stopPropagation();
                event.preventDefault();
            });

            jq('body').on('dragover', ns.input.selector, function(event) {

                event.stopPropagation();
                event.preventDefault();
            });

            jq('body').on('drop', ns.input.selector, function(event) {

                event.stopPropagation();
                event.preventDefault();

                ns.files = event.target.files;
                ns.views.preview();
            });
        },
    };

    return ns;

}(ImageUploader || {}, jQuery, _));