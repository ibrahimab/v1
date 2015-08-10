/**
 * ImageUploader
 *
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 * @version 0.0.2
 * @since   0.0.2
 */
var ImageUploader = (function(ns, cp, so, jq, _, undefined) {
    'use strict';

    ns._id      = Math.floor(1 + Math.random() * 9999);
    ns.input    = null;
    ns.preview  = null;
    ns.submit   = null;
    ns.files    = {};
    ns.previews = {};
    ns.approved = {};
    ns.options  = {

        maxRank: 1,
        multiple: false,
        allowed_types: ['image/jpg', 'image/jpeg', 'image/png'],
        max_size: 2000 * 1024, // kb
        upload_url: 'uploader/upload.php',
        url_path: '/chalet/',
        not_allowed: 'uploader/assets/images/not_allowed.png',
        collection: null,
        file_id: null,
        template: '',
        progress: {

            class_name:     'progress',
            bar_class_name: 'progress-bar',
        },
        thumbnail: {

            width:  100,
            height: 100
        }
    };

    ns.initialize = function(input, preview, submit, cropper, options) {

        ns.input    = jq(input);
        ns.preview  = jq(preview);
        ns.submit   = jq(submit);
        ns.options  = jq.extend(ns.options, options);
        ns.cropper  = cp.initialize(cropper.selector || cropper, cropper.options || {});
        ns.sortable = so.initialize();

        // binding events
        ns.events.initialize();

        return this;
    };

    ns.get = function(option, def) {

        if (undefined === ns.options[option] && undefined === def) {
            throw new Exception('Undefined option used!');
        }

        return (undefined === ns.options[option] ? def : ns.options[option]);
    };

    ns.set = function(option, newValue) {

        ns.options[option] = newValue;
        return newValue;
    };

    ns.id = function() {
        return ns._id += 1;
    };

    ns.debug = function() {
        console.log(ns);
    };

    return ns;

}(ImageUploader || {}, ImageCropper, ImageSorter, jQuery, _));