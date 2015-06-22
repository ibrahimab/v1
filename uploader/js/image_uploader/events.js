var ImageUploader = (function(ns, jq, _, undefined) {
    'use strict';

    ns.events = {

        initialize: function() {

            ns.events.change();
            ns.events.dropzone();
            ns.events.remove();
            ns.events.upload.click();
            ns.events.cropper.bind();
        },

        cropper: {

            bind: function() {

                ns.events.cropper.create();
                ns.events.cropper.changed();
            },

            create: function() {

                jq('body').on('click', '[data-role="preview-box"]', function(event) {

                    event.preventDefault();

                    var element = jq(event.target);
                    var file    = ns.files[element.data('id')] || {};

                    ns.cropper.events.destroy();
                    ns.cropper.events.create(element, file['crop'] || {});
                });
            },

            save: function() {

                var data = ns.cropper.events.save();
                var id   = data['id'];
                var crop = data['crop'];

                ns.files[id]['crop'] = crop;
            },

            changed: function() {

                ns.cropper.element.on('dragend.cropper', function(event) {
                    ns.events.cropper.save();
                });

                ns.cropper.element.on('all.cropper', function(event) {
                    ns.events.cropper.save();
                });
            }
        },

        change: function() {

            jq('body').on('change', ns.input.selector, function(event) {

                var files = event.target.files;
                var total = files.length;
                var rank  = ns.get('maxRank') + 1;
                var id;

                for (var i = 0; i < total; i++) {

                    id                = ns.id();
                    ns.files[id]      = files[i];
                    ns.files[id].id   = id;
                    ns.files[id].rank = rank++;
                }

                ns.set('maxRank', rank);
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

        remove: function() {

            jq('body').on('click', '[data-role="delete-preview"]', function(event) {

                event.preventDefault();

                var id = jq(this).data('id');
                delete ns.files[id];

    			ns.approved = {};
    			ns.previews = {};
                ns.preview.empty();
                ns.views.preview();
            });
        }
    };

    return ns;

}(ImageUploader || {}, jQuery, _));