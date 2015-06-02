<?php
include '../admin/vars.php';
?>
<!doctype html>
<html>
<head>
	<title>CMS-105</title>
	<link rel="stylesheet" href="<?php echo $vars['path'];?>uploader/assets/css/app.css" />
</head>
<body>
	<div class="container">
		<h1>Upload images</h1>
		<div class="previewer" data-role="image-uploader-previewer">
		</div>
		<input type="file" name="images" class="upload-field" data-role="image-uploader" multiple="multiple" /> <br />
		<a href="#" data-role="image-upload">Upload</a>
	</div>
	<script type="text/javascript" src="<?php echo $vars['path'];?>uploader/js/vendor/jquery/jquery-2.1.4.min.js"></script>
	<script type="text/javascript" src="<?php echo $vars['path'];?>uploader/js/vendor/underscore.js"></script>
	<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/main.js"></script>
	<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/events.js"></script>
	<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/views.js"></script>
	<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/validators.js"></script>
	<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_uploader/upload.js"></script>
	<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/image_sorter/main.js"></script>
	<script type="text/javascript" src="<?php echo $vars['path']; ?>uploader/js/app.js"></script>
</body>
</html>