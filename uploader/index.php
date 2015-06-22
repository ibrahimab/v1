<?php
include '../admin/vars.php';
$collection = 'accommodations';
$id         = intval($_GET['id']);
if ($id > 0) {

    $mongodb = $vars['mongodb']['wrapper'];
    $images  = $mongodb->getFiles($collection, $id);
    $maxRank = $mongodb->maxRank($collection, $id) + 1;
    $i       = 1;
    $mainImages = [];

    foreach ($images as $image) {

        if (isset($image['type'])) {
            $mainImages[$image['type']] = $image;
        }
    }
}
?>
<!doctype html>
<html>
<head>
	<title>CMS-105</title>
	<link rel="stylesheet" href="<?php echo $vars['path'];?>uploader/assets/css/app.css" />
    <link rel="stylesheet" href="<?php echo $vars['path'];?>uploader/assets/css/vendor/jquery/jquery.cropper.min.css" />
</head>
<body>
	<div class="container">
		<h1>Upload images</h1>
		<div class="previewer" data-role="image-uploader-previewer">
		</div>
		<input type="file" name="images" class="upload-field" data-role="image-uploader" multiple="multiple" /> <br />
		<a href="#" data-role="image-upload">Upload</a>
	</div>
    <div class="preview">
        <img data-role="cropper" />
    </div>
    <form data-role="update-form">
        <div class="container">
            <h2>Huidige afbeeldingen <a href="#" data-role="save-current">Opslaan</a></h2>
            <div class="main">
                <div class="col" style="margin-right: 5px;" data-role="main-images" data-type="big">
                    <img style="width: 450px; height: 282px;" src="<?php echo $vars['path'] . (isset($mainImages['big']) ? ('pic/cms/' . $mainImages['big']['directory'] . '/' . $mainImages['big']['filename']) : ('uploader/assets/images/placeholder.jpg')); ?>" />
                    <input type="hidden" name="main_images[big]"<?php echo (isset($mainImages['big']) ? ' value="' . $mainImages['big']['_id'] . '"' : ''); ?> />
                </div>
                <div class="col">
                    <div class="row" data-role="main-images" data-type="small-above">
                        <img style="width: 221px; height: 139px;" src="<?php echo $vars['path'] . (isset($mainImages['small_above']) ? ('pic/cms/' . $mainImages['small_above']['directory'] . '/' . $mainImages['small_above']['filename']) : ('uploader/assets/images/placeholder.jpg')); ?>" />
                        <input type="hidden" name="main_images[small_above]"<?php echo (isset($mainImages['small_above']) ? ' value="' . $mainImages['small_above']['_id'] . '"' : ''); ?> />
                    </div>
                    <div class="row" data-role="main-images" data-type="small-below">
                        <img style="width: 221px; height: 139px;" src="<?php echo $vars['path'] . (isset($mainImages['small_below']) ? ('pic/cms/' . $mainImages['small_below']['directory'] . '/' . $mainImages['small_below']['filename']) : ('uploader/assets/images/placeholder.jpg')); ?>" />
                        <input type="hidden" name="main_images[small_below]"<?php echo (isset($mainImages['small_below']) ? ' value="' . $mainImages['small_below']['_id'] . '"' : ''); ?> />
                    </div>
                </div>
            </div>
            <div class="clear">&nbsp;</div>
            <div class="previewer">
            <?php if ($id > 0) : ?>
                <input type="hidden" name="collection" value="<?php echo $collection; ?>" />
                <ul data-role="sortable-list">
                    <?php foreach ($images as $image) : ?>
                    <li data-role="sortable-item" data-id="<?php echo $image['_id']; ?>">
                        <img class="preview-image" src="<?php echo $vars['path']; ?>pic/cms/<?php echo $image['directory'] . '/' . $image['filename'];?>" />
                        <input type="text"   name="label[<?php echo $image['_id']; ?>]" placeholder="Tekst toevoegen" value="<?php echo $image['label']; ?>" />
                        <input type="hidden" name="rank[<?php echo $image['_id']; ?>]" data-role="rank" value="<?php echo $i++; ?>" />
                        <a draggable="true" data-role="sortable-anchor" data-id="<?php echo $image['_id']; ?>" class="anchor"><img src="<?php echo $vars['path']; ?>uploader/assets/images/drag-icon.png" /></a>
                        <a href="#" data-role="remove-image" data-id="<?php echo $image['_id']; ?>" style="vertical-align: top; float: right; display:block;">(x)</a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            </div>
        </div>
    </form>
    <script type="text/javascript" src="<?php echo $vars['path'];?>uploader/js/vendor/jquery/jquery-1.11.3.min.js"></script>
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
            jq.ajax({

                url: '<?php echo $vars['path']; ?>uploader/update.php',
                type: 'post',
                data: jq('[data-role="update-form"]').serialize(),
                success: function(data) {}
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
    }(jQuery));
    </script>
</body>
</html>
