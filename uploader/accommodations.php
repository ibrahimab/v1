<?php

$error = include __DIR__ . '/status.php';
if (true === $error) {
    return;
}

$id          = intval($uploaderData['id']);
$collection  = $uploaderData['collection'];
$mongodb     = $vars['mongodb']['wrapper'];
$images      = $mongodb->getFiles($collection, $id);
$maxRank     = $mongodb->maxRank($collection, $id) + 1;
$i           = 1;
$mainImages  = [];
$placeholder = 'uploader/assets/images/placeholder.png';

foreach ($images as $image) {

    if (isset($image['type'])) {
        $mainImages[$image['type']] = $image;
    }
}


?>
<link rel="stylesheet" href="<?php echo $vars['path'];?>uploader/assets/css/app.css" />
<link rel="stylesheet" href="<?php echo $vars['path'];?>uploader/assets/css/vendor/jquery/jquery.cropper.min.css" />

<div class="uploader-container">
	<div class="container">
		<div class="previewer" data-role="image-uploader-previewer"></div>
		<input type="file" name="images" class="upload-field" data-role="image-uploader" multiple="multiple" /> <br />
		<input type="submit" name="upload" class="wtform_submitbutton submit" data-role="image-upload" value="Upload" />
	</div>
	<div class="preview">
	    <div class="hide-container">
	        <hr />
	        <a href="#" data-role="close-cropper"><img src="<?php echo $vars['path']; ?>pic/icon_okay.png" /></a>
	    </div>
	    <img data-role="cropper" style="height: 500px; display: none;" />
	</div>
	<hr />
	<b>Huidige afbeeldingen</b>
	<div data-role="update-form">
	    <div class="container">
	        <div class="main">
	            <div class="col" style="margin-right: 5px; position: relative;" data-role="main-images" data-type="big">
                    <span class="clear-main-image"<?php echo (isset($mainImages['big']) ? '' : ' style="display:none;"') ?>>
                        <a href="#" data-role="clear-main-image" data-id="<?php echo $mainImages['big']['_id']; ?>">
                            <img src="<?php echo $vars['path']; ?>pic/class.cms_delete.gif" class="clear-main-image-icon" />
                        </a>
                    </span>
	                <img data-role="main-image" style="width: 434px; height: 326px;" src="<?php echo $vars['path'] . (isset($mainImages['big']) ? ('pic/cms/' . $mainImages['big']['directory'] . '/' . $mainImages['big']['filename']) : ($placeholder)); ?>" />
	                <input type="hidden" data-main-images-type="big" name="main_images[big]"<?php echo (isset($mainImages['big']) ? ' value="' . $mainImages['big']['_id'] . '"' : ''); ?> />
	            </div>
	            <div class="col">
	                <div style="position: relative;" class="row" data-role="main-images" data-type="small-above">
                        <span class="clear-main-image"<?php echo (isset($mainImages['small_above']) ? '' : ' style="display:none;"') ?>>
                            <a href="#" data-role="clear-main-image" data-id="<?php echo $mainImages['small_above']['_id']; ?>">
                                <img src="<?php echo $vars['path']; ?>pic/class.cms_delete.gif" />
                            </a>
                        </span>
	                    <img data-role="main-image" style="width: 217px; height: 163px;" src="<?php echo $vars['path'] . (isset($mainImages['small_above']) ? ('pic/cms/' . $mainImages['small_above']['directory'] . '/' . $mainImages['small_above']['filename']) : ($placeholder)); ?>" />
	                    <input type="hidden" data-main-images-type="small-above" name="main_images[small_above]"<?php echo (isset($mainImages['small_above']) ? ' value="' . $mainImages['small_above']['_id'] . '"' : ''); ?> />
	                </div>
	                <div style="position: relative;" class="row" data-role="main-images" data-type="small-below">
                        <span class="clear-main-image"<?php echo (isset($mainImages['small_below']) ? '' : ' style="display:none;"') ?>>
                            <a href="#" data-role="clear-main-image" data-id="<?php echo $mainImages['small_below']['_id']; ?>">
                                <img src="<?php echo $vars['path']; ?>pic/class.cms_delete.gif" />
                            </a>
                        </span>
	                    <img data-role="main-image" style="width: 217px; height: 163px;" src="<?php echo $vars['path'] . (isset($mainImages['small_below']) ? ('pic/cms/' . $mainImages['small_below']['directory'] . '/' . $mainImages['small_below']['filename']) : ($placeholder)); ?>" />
	                    <input type="hidden" data-main-images-type="small-below" name="main_images[small_below]"<?php echo (isset($mainImages['small_below']) ? ' value="' . $mainImages['small_below']['_id'] . '"' : ''); ?> />
	                </div>
	            </div>
	        </div>
	        <div class="clear">&nbsp;</div>
	        <?php if (count($images) > 0) : ?>
	        <a href="#" data-role="remove-all">Alle afbeeldingen verwijderen</a>
	        <div class="previewer">
	            <input type="hidden" name="collection" value="<?php echo $collection; ?>" />
	            <ul data-role="sortable-list">
	                <?php foreach ($images as $image) : ?>
	                <li data-role="sortable-item" data-id="<?php echo $image['_id']; ?>">
	                    <img class="preview-image" src="<?php echo $vars['path']; ?>pic/cms/<?php echo $image['directory'] . '/' . $image['filename'];?>?c=<?php echo filemtime($vars["unixdir"] . 'pic/cms/' . $image['directory'] . '/' . $image['filename']); ?>" />
	                    <input type="text"   name="label[<?php echo $image['_id']; ?>]" placeholder="Tekst toevoegen voor nieuwe site" value="<?php echo $image['label']; ?>" />
	                    <div><input type="checkbox" id="under_<?php echo $image['_id']; ?>" name="under[<?php echo $image['_id']; ?>]" style="width: auto;" value="1"<?php echo (isset($image['under']) && true === $image['under'] ? ' checked="checked"' : ''); ?> /> <label for="under_<?php echo $image['_id']; ?>">Altijd onderaan</label></div>
						<?php if (isset($image['type']) && in_array($image['type'], ['big', 'small'])) : ?>
							<div><input type="checkbox" id="always_<?php echo $image['_id']; ?>" name="always[<?php echo $image['_id']; ?>]" style="width: auto;" value="1"<?php echo (isset($image['always']) && true === $image['always'] ? ' checked="checked"' : ''); ?> /> <label for="always_<?php echo $image['_id']; ?>">Ook gewoon tonen (=topfoto)</label></div>
						<?php endif; ?>
	                    <input type="hidden" name="rank[<?php echo $image['_id']; ?>]" data-role="rank" value="<?php echo $i++; ?>" />
	                    <a draggable="true" data-role="sortable-anchor" data-id="<?php echo $image['_id']; ?>" class="anchor"><img src="<?php echo $vars['path']; ?>uploader/assets/images/drag-icon.png" /></a>
	                    <a href="#" data-role="remove-image" data-id="<?php echo $image['_id']; ?>" style="vertical-align: top; float: right; display:block;">
	                        <img src="<?php echo $vars['path']; ?>pic/class.cms_delete.gif" />
	                    </a>
	                </li>
	                <?php endforeach; ?>
	            </ul>
	        </div>
	        <?php endif; ?>
	        <div class="clear">&nbsp;</div>
	        <input type="submit" name="opslaan" class="wtform_submitbutton submit" data-role="save-current" value="Opslaan" />
			<span style="display: none; color: green;" data-role="current-saved">Succesvol opgeslagen.</span>
	    </div>
	</div>
</div>

<script type="text/javascript" src="<?php echo $vars['path'];?>uploader/js/vendor/jquery/jquery.cropper.min.js"></script>
<script type="text/javascript" src="<?php echo $vars['path'];?>uploader/js/vendor/jquery/jquery.beforeafter.min.js"></script>
<script type="text/javascript" src="<?php echo $vars['path'];?>uploader/js/vendor/underscore.js"></script>
<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_cropper/main.js"></script>
<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_sorter/main.js"></script>
<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/main.js"></script>
<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/events.js"></script>
<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/views.js"></script>
<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/validators.js"></script>
<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/upload.js"></script>

<script type="text/javascript">
(function(jq, undefined) {
    'use strict';

	var template = '<li data-role="sortable-item" data-id="{{ id }}">' +
                        '<img class="preview-image" src="{{ url_path }}pic/cms/{{ image }}?c=<?php echo time(); ?>" />' +
                        '<input type="text" name="label[{{ id }}]" placeholder="Tekst toevoegen voor nieuwe site" value="{{ label }}" />' +
                        '<div><input type="checkbox" id="under_{{ id }}" name="under[{{ id }}]" style="width: auto;" value="1" /> <label for="under_{{ id }}">Altijd onderaan</label></div>' +
                        '<input type="hidden" name="rank[{{ id }}]" data-role="rank" value="" />' +
                        '<a draggable="true" data-role="sortable-anchor" data-id="{{ id }}" class="anchor"><img src="{{ url_path }}uploader/assets/images/drag-icon.png" /></a>' +
                        '<a href="#" data-role="remove-image" data-id="{{ id }}" style="vertical-align: top; float: right; display:block;">' +
                            '<img src="{{ url_path }}pic/class.cms_delete.gif" />' +
                        '</a>' +
					'</li>';

    ImageUploader.initialize('[data-role="image-uploader"]', '[data-role="image-uploader-previewer"]', '[data-role="image-upload"]', '[data-role="cropper"]', {

        url_path:   '<?php echo $vars['path']; ?>',
        collection: '<?php echo $collection; ?>',
        file_id:     <?php echo $id; ?>,
        maxRank:     <?php echo $maxRank; ?>,
		template:   template
    });

    jq('body').on('click', '[data-role="save-current"]', function(event) {

        event.preventDefault();

        var data = jq('[data-role="update-form"] :input').serialize();

        jq.ajax({

            url: '<?php echo $vars['path']; ?>uploader/update.php',
            type: 'post',
            data: data,
            success: function(data) {

                var saved = jq('[data-role="current-saved"]');
                saved.show();

                jq('[data-role="main-images"]').each(function() {

                    var element = jq(this);
                    var input   = element.find('input');

                    if (input.val() != '') {
                        element.find('span').show().find('a').data('id', input.val());
                    }
                });

                setTimeout(function() {
                    saved.fadeOut();
                }, 3000);
            }
        });
    });

    jq('body').on('click', '[data-role="remove-image"]', function(event) {

        event.preventDefault();

		if (window.confirm('Afbeelding verwijderen?')) {

	        var id = jq(this).data('id');

	        jq.ajax({

	            url: '<?php echo $vars['path']; ?>uploader/delete.php',
	            type: 'post',
	            data: {id: id, collection: '<?php echo $collection; ?>'},
	            success: function(data) {

	                jq('[data-role="sortable-item"][data-id="' + id + '"]').remove();
	            }
	        });
		}
    });

    jq('body').on('click', '[data-role="remove-all"]', function(event) {

        event.preventDefault();

		if (window.confirm('Alle afbeeldingen verwijderen?')) {

	        jq.ajax({

	            url: '<?php echo $vars['path']; ?>uploader/delete_all.php',
	            type: 'post',
	            data: {id: <?php echo $id; ?>, collection: '<?php echo $collection; ?>'},
	            success: function(data) {

	                jq('[data-role="sortable-list"]').empty();
	                jq('[data-role="main-images"] img').attr('src', '<?php echo $placeholder; ?>');
                    jq('[data-role="main-images"] span').hide();
	            }
	        });
		}
    });

    jq('body').on('click', '[data-role="clear-main-image"]', function(event) {

        event.preventDefault();

        if (window.confirm('Afbeelding niet langer hoofdafbeelding?')) {

            var el   = jq(this);
            var id   = el.data('id');
            var type = el.parents('[data-role="main-images"]').data('type');

            jq.ajax({

                url: '<?php echo $vars['path']; ?>uploader/clear.php',
                type: 'post',
                data: {id: id, type: type, collection: '<?php echo $collection; ?>'},
                success: function(data) {

                    jq('[data-role="main-images"][data-type="' + type + '"] img[data-role="main-image"]').attr('src', '<?php echo $placeholder; ?>');
                    jq('[data-role="main-images"][data-type="' + type + '"] span').hide();
                    jq('[data-main-images-type="' + type + '"]').val('');
                }
            });
        }
    });

}(jQuery));
</script>
