<?php
$id              = intval($uploaderData['id']);
$accommodationId = intval($uploaderData['accommodationId']);
$collection      = $uploaderData['collection'];
$mongodb         = $vars['mongodb']['wrapper'];
$images          = $mongodb->getFiles($collection, $id);
$maxRank         = $mongodb->maxRank($collection, $id) + 1;
$i               = 1;
$mainImages      = [];
$placeholder     = 'uploader/assets/images/placeholder.png';

foreach ($images as $image) {

    if (isset($image['type'])) {
        $mainImages[$image['type']] = $image;
    }
}

$accommodations      = $mongodb->getCollection('accommodations');
$accommodationImages = $accommodations->find(['file_id' => $accommodationId, 'under' => true]);
?>
<link rel="stylesheet" href="<?php echo $vars['path'];?>uploader/assets/css/app.css" />
<link rel="stylesheet" href="<?php echo $vars['path'];?>uploader/assets/css/vendor/jquery/jquery.cropper.min.css" />

<div class="container">
	<div class="previewer" data-role="image-uploader-previewer"></div>
	<input type="file" name="images" class="upload-field" data-role="image-uploader" multiple="multiple" /> <br />
	<input type="submit" name="upload" class="wtform_submitbutton" data-role="image-upload" value="Upload" />
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
            <div class="col" style="margin-right: 5px;" data-role="main-images" data-type="big">
                <img style="width: 434px; height: 326px;" src="<?php echo $vars['path'] . (isset($mainImages['big']) ? ('pic/cms/' . $mainImages['big']['directory'] . '/' . $mainImages['big']['filename']) : ($placeholder)); ?>" />
                <input type="hidden" name="main_images[big]"<?php echo (isset($mainImages['big']) ? ' value="' . $mainImages['big']['_id'] . '"' : ''); ?> />
            </div>
            <div class="col">
                <div class="row" data-role="main-images" data-type="small-above">
                    <img style="width: 217px; height: 163px;" src="<?php echo $vars['path'] . (isset($mainImages['small_above']) ? ('pic/cms/' . $mainImages['small_above']['directory'] . '/' . $mainImages['small_above']['filename']) : ($placeholder)); ?>" />
                    <input type="hidden" name="main_images[small_above]"<?php echo (isset($mainImages['small_above']) ? ' value="' . $mainImages['small_above']['_id'] . '"' : ''); ?> />
                </div>
                <div class="row" data-role="main-images" data-type="small-below">
                    <img style="width: 217px; height: 163px;" src="<?php echo $vars['path'] . (isset($mainImages['small_below']) ? ('pic/cms/' . $mainImages['small_below']['directory'] . '/' . $mainImages['small_below']['filename']) : ($placeholder)); ?>" />
                    <input type="hidden" name="main_images[small_below]"<?php echo (isset($mainImages['small_below']) ? ' value="' . $mainImages['small_below']['_id'] . '"' : ''); ?> />
                </div>
            </div>
        </div>
        <div class="clear">&nbsp;</div>
        <?php if (count($images) > 0 || count($accommodationImages) > 0) : ?>
        <a href="#" data-role="remove-all">Alle afbeeldingen verwijderen</a>
        <div class="previewer">
            <input type="hidden" name="collection" value="<?php echo $collection; ?>" />
            <ul data-role="sortable-list">
                <?php foreach ($images as $image) : ?>
                <li class="type-image" data-role="sortable-item" data-id="<?php echo $image['_id']; ?>">
                    <img class="preview-image" src="<?php echo $vars['path']; ?>pic/cms/<?php echo $image['directory'] . '/' . $image['filename'];?>" />
                    <input type="text"   name="label[<?php echo $image['_id']; ?>]" placeholder="Tekst toevoegen" value="<?php echo $image['label']; ?>" />
                    <input type="hidden" name="rank[<?php echo $image['_id']; ?>]" data-role="rank" value="<?php echo $i++; ?>" />
                    <a draggable="true" data-role="sortable-anchor" data-id="<?php echo $image['_id']; ?>" class="anchor"><img src="<?php echo $vars['path']; ?>uploader/assets/images/drag-icon.png" /></a>
                    <a href="#" data-role="remove-image" data-id="<?php echo $image['_id']; ?>" style="vertical-align: top; float: right; display:block;">
                        <img src="<?php echo $vars['path']; ?>pic/class.cms_delete.gif" />
                    </a>
                </li>
                <?php endforeach; ?>
                <?php foreach ($accommodationImages as $accommodationImage) : ?>
                    <li>
                        <img class="preview-image" src="<?php echo $vars['path']; ?>pic/cms/<?php echo $accommodationImage['directory'] . '/' . $accommodationImage['filename']; ?>" />
                        <input type="text" value="<?php echo $accommodationImage['label']; ?>" disabled="disabled" />
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <div class="clear">&nbsp;</div>
        <input type="submit" name="opslaan" class="wtform_submitbutton" data-role="save-current" value="Opslaan" /> <br />
        <span style="display: none; color: green;" data-role="current-saved">Data is succesvol opgeslagen!</span>
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

    ImageUploader.initialize('[data-role="image-uploader"]', '[data-role="image-uploader-previewer"]', '[data-role="image-upload"]', '[data-role="cropper"]', {

        url_path:   '<?php echo $vars['path']; ?>',
        collection: '<?php echo $collection; ?>',
        file_id:     <?php echo $id; ?>,
        maxRank:     <?php echo $maxRank; ?>
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

                setTimeout(function() {
                    saved.fadeOut();
                }, 3000);
            }
        });
    });

    jq('body').on('click', '[data-role="remove-image"]', function(event) {

        event.preventDefault();

        var id = jq(this).data('id');

        jq.ajax({

            url: '<?php echo $vars['path']; ?>uploader/delete.php',
            type: 'post',
            data: {id: id, collection: '<?php echo $collection; ?>'},
            success: function(data) {

                jq('[data-role="sortable-item"][data-id="' + id + '"]').remove();
            }
        });
    });

    jq('body').on('click', '[data-role="remove-all"]', function(event) {

        event.preventDefault();

        jq.ajax({

            url: '<?php echo $vars['path']; ?>uploader/delete_all.php',
            type: 'post',
            data: {id: <?php echo $id; ?>, collection: '<?php echo $collection; ?>'},
            success: function(data) {

                jq('[data-role="sortable-list"] li.type-image').remove();
                jq('[data-role="main-images"] img').attr('src', '<?php echo $placeholder; ?>');
            }
        });
    });

}(jQuery));
</script>