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
                    
                    xhr.onreadystatechange = function() {
                        
                        if (xhr.readyState === 4) {
                            
                            var response = JSON.parse(xhr.responseText);
                            
                            if (response.type === 'error') {
                                reject(file);
                            } else {
                                resolve(file);
                            }
                        }
                    }

                    xhr.send(file);
                    
                } catch (err) { console.log(err); }
            });
        },

        all: function() {

            var promises = [];
            var promise;
            
            for (var id in ns.approved) {

                if (ns.approved.hasOwnProperty(id)) {
                    
                    promise = ns.upload.send(ns.approved[id])
                                       .then(function(file) {
                                           
                                           ns.events.removeFile(file.id);
                                           ns.views.removePreview(file.id);
                                       })
                                       .catch(function(file) {
                                           jq('[data-role="success-message"][data-id="' + file.id + '"]').text('verhouding is niet 4:3 ');
                                       });
                                       
                    promises.push(promise);
                }
            }
            
            Promise.all(promises)
                   .then(function() {
                       
                       wt_popupmsg('Afbeeldingen zijn succesvol ge&uuml;pload');
                       ns.cropper.events.destroy();
                   })
                   .catch(function() {
                       
                       wt_popupmsg('Sommige afbeeldingen zijn niet succesvol ge&uuml;pload');
                       ns.cropper.events.destroy();
                   });
        }
    };

    return ns;

}(ImageUploader || {}, jQuery, _));