var ImageUploader = (function(ns, jq, _, undefined) {
    'use strict';

    ns.upload = {

        send: function(file) {

            return new Promise(function(resolve, reject) {

                try {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', ns.get('url_path') + ns.get('upload_url'), true);
                xhr.setRequestHeader('X_FILENAME', file.name);
                xhr.setRequestHeader('X_CROP_DATA', JSON.stringify(file.crop));
                xhr.setRequestHeader('X_LABEL', jq('[data-role="label"][data-id="' + file.id + '"]').val());
                xhr.setRequestHeader('X_FILE_ID', ns.get('file_id'));
                xhr.setRequestHeader('X_COLLECTION', ns.get('collection'));
                xhr.setRequestHeader('X_RANK', file.rank);
                xhr.upload.addEventListener('progress', function(event) {

                    ns.events.upload.progress(event, file, ns.views.progress);

                    var percentage = Math.round(event.loaded / event.total * 100);
                    if (percentage === 100) {
                        resolve(file);
                    }
                });

                xhr.send(file);
                } catch (err) { console.log(err); }
            });
        },

        all: function() {

            var promises = [];
            for (var id in ns.approved) {

                if (ns.approved.hasOwnProperty(id)) {
                    promises.push(ns.upload.send(ns.approved[id]));
                }
            }

            Promise
                .all(promises)
                .then(function(files) {

                    ns.views.markAsDone(files);
                    ns.views.clearInput();
                    ns.cropper.events.destroy();
                })
                .catch(function(error) {});
        }
    };

    return ns;

}(ImageUploader || {}, jQuery, _));