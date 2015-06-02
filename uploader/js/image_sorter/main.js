var ImageSorter = (function(ns, jq, _, undefined) {
    'use strict';
    
    ns.options = {
        
        opacity:  0.5,
        selector: '[data-sortable="true"]',
        anchor:   '[data-sortable-anchor="true"]'
    };
    
    ns.initialize = function(options) {
        
        ns.options = jq.extend(ns.options, options);
        ns.bind();
    };
    
    ns.bind = function() {
        
        jq(ns.get('selector')).each(function() {
            
            this.addEventListener('dragstart', ns.start, false);
            this.addEventListener('dragenter', ns.enter, false);
            this.addEventListener('dragover',  ns.over,  false);
            this.addEventListener('dragleave', ns.leave, false);
            this.addEventListener('drop',      ns.drop,  false);
            this.addEventListener('dragend',   ns.end,   false);
        });
    };
    
    ns.start = function(event) {
        
        var element = jq(this);
        
        event.dataTransfer.effectAllowed = 'move';
        
        event.dataTransfer.setData('text/html', element.html());
        event.dataTransfer.setData('id',        element.data('id'));
        event.dataTransfer.setData('select',    element.find('select').val());
        event.dataTransfer.setData('label',     element.find('input').val());
    };
    
    ns.end = function(event) {
        jq(ns.get('selector')).css('opacity', 1).removeClass('over');
    };
    
    ns.over = function(event) {
        
        if (event.preventDefault) {
            event.preventDefault();
        }
        
        event.dataTransfer.dropEffect = 'move';
        return false;
    };
    
    ns.enter = function(event) {
        jq(this).parent().addClass('over');
    };
    
    ns.leave = function(event) {
        jq(this).parent().removeClass('over');
    };
    
    ns.drop = function(event) {
        
        if (event.stopPropagation) {
            event.stopPropagation();
        }
        
        var element   = jq(this);
        var currentId = element.data('id');
        var prevId    = event.dataTransfer.getData('id');

        if (currentId !== prevId) {
            
            var prevElement = jq(ns.get('selector') + '[data-id="' + prevId + '"]');
            if (undefined !== prevElement.get(0)) {
                
                var select = element.find('select').val();
                var label  = element.find('input').val();
                
                prevElement.html(element.html());
                prevElement.find('select').val(select);
                prevElement.find('input').val(label);
            
                element.html(event.dataTransfer.getData('text/html'));
                element.find('select').val(event.dataTransfer.getData('select'));
                element.find('input').val(event.dataTransfer.getData('label'));
            }
        }
        
        return false;
    };
    
    ns.get = function(option, def) {
        
        if (undefined === ns.options[option] && undefined === def) {
            throw new Exception('Undefined option used!');
        }
        
        return (undefined === ns.options[option] ? def : ns.options[option]);
    };
    
    return ns;

}(ImageSorter || {}, jQuery, _));