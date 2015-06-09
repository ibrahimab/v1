<?php
include '../admin/vars.php';
$collection = 'accommodations';
$id         = intval($_GET['id']);
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
    <div class="container">
        <h2>Huidige afbeeldingen <a href="#" data-role="save-current">Opslaan</a></h2>
        <div class="previewer">
        <?php if ($id > 0) : ?>
            <?php
            $mongodb = $vars['mongodb']['wrapper'];
            $images  = $mongodb->getFiles($collection, $id);
            ?>
            <ul data-role="sortable-list">
                <?php foreach ($images as $image) : ?>
                <li data-role="sortable-item" data-id="<?php echo $image['_id']; ?>">
                    <img class="preview-image" src="https://www.chalet.nl/pic/cms/<?php echo $image['directory'] . '/' . $image['filename'];?>" />
                    <input type="text" placeholder="Tekst toevoegen" value="<?php echo $image['label']; ?>" />
                    <a draggable="true" data-role="sortable-anchor" data-id="<?php echo $image['_id']; ?>" class="anchor"><img src="<?php echo $vars['path']; ?>uploader/assets/images/drag-icon.png" /></a>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        </div>
    </div>
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
	<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_sorter/main.js"></script>
	<script type="text/javascript">
    jQuery(function() {

        ImageUploader.initialize('[data-role="image-uploader"]', '[data-role="image-uploader-previewer"]', '[data-role="image-upload"]', '[data-role="cropper"]', {

            collection: '<?php echo $collection; ?>',
            file_id:     <?php echo $id; ?>
        });
    });
    </script>
</body>
</html>