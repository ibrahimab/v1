var ImageUploader = (function(ns, jq, _, undefined) {
    'use strict';

    ns.upload = {

        send: function(file) {

            jq('[data-role="delete-preview"][data-id="' + file.id +'"] img').attr('src', ns.get('url_path') + 'pic/ajax-loader.gif');

            return new Promise(function(resolve, reject) {

                try {
                    
                    var xhr = new XMLHttpRequest();
                    var img;
                    
                    file.label = jq('[data-role="label"][data-id="' + file.id + '"]').val();
                    
                    xhr.open('POST', ns.get('url_path') + ns.get('upload_url'), true);
                    xhr.setRequestHeader('X_FILENAME', file.name);
                    xhr.setRequestHeader('X_CROP_DATA', JSON.stringify(file.crop));
                    xhr.setRequestHeader('X_LABEL', file.label);
                    xhr.setRequestHeader('X_FILE_ID', ns.get('file_id'));
                    xhr.setRequestHeader('X_COLLECTION', ns.get('collection'));
                    xhr.setRequestHeader('X_RANK', file.rank);
                    
                    xhr.onreadystatechange = function() {
                        
                        if (xhr.readyState === 4) {
                            
                            var response = JSON.parse(xhr.responseText);
                            
                            if (response.type === 'error') {
                                reject(file);
                            } else {
                                
                                file.new = {
                                
                                    id: response.image.id,
                                    path: response.image.path
                                };
                                
                                resolve(file);
                            }
                            
                            jq('[data-role="delete-preview"][data-id="' + file.id + '"] img').attr('src', ns.get('url_path') + 'pic/class.cms_delete.gif');
                        }
                    }
                    
                    xhr.send(file);
                    
                } catch (err) {}
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
                                           ns.events.removeApproved(file.id);
                                           ns.views.addExisting(file);
                                       })
                                       .catch(function(file) {
                                           
                                           jq('[data-role="success-message"][data-id="' + file.id + '"]').text('verhouding is niet 4:3 ');
                                           throw new Error(file);
                                       });
                                       
                    promises.push(promise);
                }
            }
            
            Promise.all(promises)
                   .then(function() {
                       
                       wt_popupmsg('Afbeeldingen zijn succesvol ge&uuml;pload');
                       ns.cropper.events.destroy();
                       ns.views.resetRanks();
                   })
                   .catch(function() {
                       
                       wt_popupmsg('Sommige afbeeldingen zijn <strong><u>niet</u></strong> succesvol ge&uuml;pload');
                       ns.cropper.events.destroy();
                       ns.views.resetRanks();
                   });
        }
    };

    return ns;

}(ImageUploader || {}, jQuery, _));