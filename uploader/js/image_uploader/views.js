var ImageUploader = (function(ns, jq, _, undefined) {
    'use strict';

    ns.views = {

        anchor: function () {

            var anchor = document.createElement('div');
            anchor.className = 'anchor';
            anchor.setAttribute('data-sortable-anchor', true);

            return anchor;
        },

        image: function(file, dimensions) {

            if (false === (file instanceof File)) {
                throw new Exception('Cannot create thumbnail from this object (' + file.toString() + ')');
            }

            var thumbnail  = document.createElement('img');
            thumbnail.file = file;
            thumbnail.setAttribute('data-id', file.id);

            var reader    = new FileReader();
            reader.onload = (function(thumb) {

                return function(event) {

                    var image = new Image();
                    image.onload = function() {
                        file.crop = {x: 0, y: 0, width: this.width, height: this.height};
                    };

                    image.src = event.target.result;
                    thumb.src = event.target.result;
                };

            }(thumbnail));

            var data    = reader.readAsDataURL(file);
            var canvas  = document.createElement('canvas');
            var context = canvas.getContext('2d');

            thumbnail.onload = function() {
                context.drawImage(thumbnail, dimensions.width, dimensions.height);
            };

            return thumbnail;
        },

        notAllowed: function(file, dimensions) {

            var thumbnail    = document.createElement('img');
            thumbnail.src    = ns.get('url_path') + ns.get('not_allowed');
            thumbnail.width  = dimensions.width;
            thumbnail.height = dimensions.height;

            return thumbnail;
        },

        preview: function() {

            var image         = null;
            var images        = [];
            var dimensions    = ns.get('thumbnail');
            var allowed_types = ns.get('allowed_types');
            var max_size      = ns.get('max_size') * 1024;
            var file;

            for (var id in ns.files) {

                if (ns.files.hasOwnProperty(id)) {

                    file = ns.files[id];

                    if (true === _.contains(ns.previews, file.name)) {
                        continue;
                    }

                    switch (false) {

                        case ns.validators.mime(file.type, allowed_types):
                        case ns.validators.size(file.size, max_size):
                            images.push({img: ns.views.notAllowed(file, dimensions), file: file});
                        break;

                        default:
                            images.push({img: ns.views.image(file, dimensions), file: file});
                            ns.approved[id] = file;
                    }

                    ns.previews[id] = file.name;
                }
            }

            ns.views.renderList(images);
        },

        renderList: function(list) {

            var prev  = ns.preview.find('ul');
            var ul    = prev.length > 0 ? prev.get(0) : document.createElement('ul');

            for (var i in list) {

                if (list.hasOwnProperty(i)) {
                    ul.appendChild(ns.views.render(list[i].img, list[i].file.id, list[i].file));
                }
            }

            ns.preview.append(ul);
        },

        render: function(image, id, file) {

            var li = document.createElement('li');
            li.setAttribute('data-role', 'preview-box');
            li.setAttribute('data-sortable', true);
            li.setAttribute('data-id', id);
            li.setAttribute('draggable', true);
            li.appendChild(image);
            li.appendChild(ns.views.anchor());
            li.appendChild(ns.views.kinds(file));
            li.appendChild(ns.views.label(file));
            li.appendChild(ns.views.progressBar(file));

            return li;
        },

        filename: function(file) {

            var span         = document.createElement('span');
            span.textContent = file.name;

            return span;
        },

        progressBar: function(file) {

            var settings       = ns.get('progress');
            var progress       = document.createElement('div');
            progress.className = settings.class_name;

            var bar = document.createElement('div');
            bar.className = settings.bar_class_name;
            bar.setAttribute('data-role', 'progress-bar');
            bar.setAttribute('data-id', file.id);

            progress.appendChild(bar);
            progress.appendChild(ns.views.filename(file));

            return progress;
        },

        progress: function(file, percentage) {

            var bar = document.querySelector('[data-role="progress-bar"][data-id="' + file.id + '"]');
            if (null !== bar) {
                bar.style.width = percentage + '%';
            }
        },

        removePreview: function(id) {

            var li = document.querySelector('[data-role="preview-box"][data-id="' + id + '"]');
            if (null !== li) {
                li.remove();
            }
        },

        clearPreviews: function() {

            ns.files    = {};
            ns.approved = {};
            ns.previews = {};

            ns.preview.empty();
        },

        clearInput: function() {
            ns.input.val('');
        },

        kinds: function(file) {

            var select  = document.createElement('select');
            select.name = 'kind[]'

            var kinds  = ns.get('kinds');
            var total  = kinds.length;

            for (var i = 0; i < total; i++) {
                select.appendChild(ns.views.kind(kinds[i]));
            }

            return select;
        },

        kind: function(kind) {

            var option   = document.createElement('option');
            option.value = kind['value'];
            option.text  = kind['text'];

            return option;
        },

        label: function(file) {

            var input         = document.createElement('input');
            input.type        = 'text';
            input.placeholder = 'Tekst toevoegen';

            return input;
        },

        markAsDone: function(files) {

            var li;
            var done;
            var selector;
            var img;
            var total = files.length;

            for (var i = 0; i < total; i++) {

                selector = '[data-role="preview-box"][data-id="' + files[i].id + '"]';
                li       = document.querySelector(selector);
                img      = document.querySelector(selector + ' img');

                if (null !== li && null !== img) {
                    ns.views.done(li, img);
                }
            }
        },

        done: function(li, img) {

            done = document.createElement('span');
            done.className = 'done';

            li.insertBefore(done, img);
        }
    };

    return ns;

}(ImageUploader || {}, jQuery, _));