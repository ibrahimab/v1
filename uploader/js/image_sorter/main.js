var ImageSorter = (function(ns, jq, _, undefined) {
    'use strict';

    ns.options = {

        anchor: '[data-role="sortable-anchor"]',
        list:   '[data-role="sortable-list"]',
        item:   '[data-role="sortable-item"]'
    };

    ns.initialize = function() {

        jq.event.props.push( "dataTransfer" );

        jq('body').on('dragstart', ns.get('anchor'), ns.start);
        jq('body').on('dragend',   ns.get('anchor'), ns.end);
        jq('body').on('dragover',  ns.get('item'),   ns.over);
        jq('body').on('dragleave', ns.get('item'),   ns.leave);
        jq('body').on('drop',      ns.get('item'),   ns.drop);

        jq('body').on('dragenter', '[data-role="main-images"]', function(event) {
            event.preventDefault();
        });

        jq('body').on('dragover', '[data-role="main-images"]', function(event) {
            event.preventDefault();
        });

        jq('body').on('drop', '[data-role="main-images"]', function(event) {

            if(event.preventDefault) {
                event.preventDefault();
            }
            if(event.stopPropagation) {
                event.stopPropagation();
            }

            var id      = event.dataTransfer.getData('id');
            var element = jq(this);

            element.find('img[data-role="main-image"]').attr('src', jq(ns.get('item') + '[data-id="' + id + '"]').find('img').attr('src'));
            element.find('input').val(id);

        });
    };

    ns.start = function(event) {

        var element = jq(this);
        event.originalEvent.dataTransfer.effectAllowed = 'move';
        event.originalEvent.dataTransfer.setData('id', element.data('id'));
    };

    ns.end = function(event) {
        jq(ns.get('item')).removeClass('over');
    };

    ns.over = function(event) {

        if (event.preventDefault) {
            event.preventDefault();
        }

        event.originalEvent.dataTransfer.dropEffect = 'move';
        jq(this).addClass('over');
        return false;
    };

    ns.leave = function(event) {
        jq(this).removeClass('over');
    };

    ns.drop = function(event) {

        if (event.stopPropagation) {
            event.stopPropagation();
        }

        var a      = jq(this);
        var a_id   = a.data('id');
        var b_id   = event.originalEvent.dataTransfer.getData('id');
        var b      = jq(ns.get('item') + '[data-id="' + b_id + '"]');

        if (a_id === b_id) {
            return false;
        }

        if (b.isBefore(a)) {
            b.insertAfter(a);
        } else {
            b.insertBefore(a);
        }

        var i = 1;
        jq(ns.get('item')).each(function() {

            var element = jq(this);
            element.find('[data-role="rank"]').val(i++);
        });

        return false;
    };

    ns.get = function(option, def) {

        if (undefined === ns.options[option] && undefined === def) {
            throw new Error('Undefined option used: ' + option);
        }

        return (undefined === ns.options[option] ? def : ns.options[option]);
    };

    return ns;

}(ImageSorter || {}, jQuery, _));