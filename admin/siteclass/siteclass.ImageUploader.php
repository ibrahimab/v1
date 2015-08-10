<?php

class ImageUploader extends Uploader
{
	const IMAGE_WIDTH            = 1;
	const IMAGE_HEIGHT 	         = 2;
	const IMAGE_MIN_WIDTH        = 3;
	const IMAGE_MIN_HEIGHT       = 4;
	const IMAGE_MAX_WIDTH        = 5;
	const IMAGE_MAX_HEIGHT       = 6;
	const IMAGE_RATIO_WIDTH      = 7;
	const IMAGE_RATIO_HEIGHT     = 8;
	const IMAGE_FILE_TYPE		 = 9;

	const ERROR_IMAGE_WIDTH      = 100;
	const ERROR_IMAGE_HEIGHT 	 = 200;
	const ERROR_IMAGE_MIN_WIDTH  = 300;
	const ERROR_IMAGE_MIN_HEIGHT = 400;
	const ERROR_IMAGE_MAX_WIDTH  = 500;
	const ERROR_IMAGE_MAX_HEIGHT = 600;
	const ERROR_IMAGE_RATIO      = 700;
	const ERROR_FILE_TYPE		 = 800;
	const ERROR_IMAGE_UPLOAD     = 900;

	protected $imageSizes;
	protected $lastError;
	protected $done;
	protected $uploadRank;

	public function __construct($inputName, $multiple = false, $html = [])
	{
		parent::__construct($inputName, $multiple, $html);
		$this->imageSizes = [];
		$this->done		  = [];
		$this->errors	  = [];
		$this->uploadRank = 1;
	}

	public function validate($name)
	{
		foreach ($_FILES[$name]['tmp_name'] as $inputName => $tmpFiles) {

			if ($inputName === $this->inputName) {

				if (is_array($tmpFiles)) {

					foreach ($tmpFiles as $key => $tmpFile) {

						if (trim($tmpFile) === '') {
							continue;
						}

						$filename  = $_FILES[$name]['name'][$inputName][$key];
						$validated = $this->check($tmpFile, $filename);

						if (true === $validated) {
							$this->upload($tmpFile, $filename);
						}
					}

				} else {

					if (trim($tmpFiles) === '') {
						continue;
					}

					$filename  = $_FILES[$name]['name'][$inputName];
					$validated = $this->check($tmpFiles, $filename);

					if (true === $validated) {
						$this->upload($tmpFiles, $filename);
					}
				}

				// $this->checkFileType($_FILES[$filesName]['name'][$inputName]);
			}
		}

		return null === $this->getLastError();
	}

	public function check($file, $name)
	{
		$errors   = [];
		$errors[] = $this->checkWidth($file);
		$errors[] = $this->checkHeight($file);
		$errors[] = $this->checkMinWidth($file);
		$errors[] = $this->checkMinHeight($file);
		$errors[] = $this->checkMaxWidth($file);
		$errors[] = $this->checkMaxHeight($file);
		$errors[] = $this->checkRatio($file);

		$errors = array_filter($errors, 'is_int');

		if (($errorCount = count($errors)) > 0) {

			foreach ($errors as $error) {
				$this->addError($error, $name);
			}
		}

		return ($errorCount === 0 ? true : $errors);
	}

	public function addError($errorCode, $name)
	{
		$this->errors[] = ['code' => $errorCode, 'name' => $name];
	}

	public function getLastError()
	{
		$errors = $this->errors;
		return array_pop($errors);
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function checkWidth($file)
	{
		$widthRestriction = $this->getOption(self::IMAGE_WIDTH);
		if (null === $widthRestriction) {

			// no width restriction
			return;
		}

		list($width, $height) = $this->getImageSizes($file);

		if ($width > $widthRestriction) {
			return self::ERROR_IMAGE_WIDTH;
		}
	}

	public function checkHeight($file)
	{
		$heightRestriction = $this->getOption(self::IMAGE_HEIGHT);
		if (null === $heightRestriction) {

			// no height restriction
			return;
		}

		list($width, $height) = $this->getImageSizes($file);

		if ($height > $heightRestriction) {
			return self::ERROR_IMAGE_HEIGHT;
		}
	}

	public function checkMinWidth($file)
	{
		$minWidthRestriction = $this->getOption(self::IMAGE_MIN_WIDTH);
		if (null === $minWidthRestriction) {

			// no maximum width restriction
			return;
		}

		list($width, $height) = $this->getImageSizes($file);

		if ($width < $minWidthRestriction) {
			return self::ERROR_IMAGE_MIN_WIDTH;
		}
	}

	public function checkMinHeight($file)
	{
		$minHeightRestriction = $this->getOption(self::IMAGE_MAX_HEIGHT);
		if (null === $minHeightRestriction) {

			// no maximum height restriction
			return;
		}

		list($width, $height) = $this->getImageSizes($file);

		if ($height < $minHeightRestriction) {
			return self::ERROR_IMAGE_MIN_HEIGHT;
		}
	}

	public function checkMaxWidth($file)
	{
		$maxWidthRestriction = $this->getOption(self::IMAGE_MAX_WIDTH);
		if (null === $maxWidthRestriction) {

			// no maximum width restriction
			return;
		}

		list($width, $height) = $this->getImageSizes($file);

		if ($width > $maxWidthRestriction) {
			return self::ERROR_IMAGE_MAX_WIDTH;
		}
	}

	public function checkMaxHeight($file)
	{
		$maxHeightRestriction = $this->getOption(self::IMAGE_MAX_HEIGHT);
		if (null === $maxHeightRestriction) {

			// no maximum height restriction
			return;
		}

		list($width, $height) = $this->getImageSizes($file);

		if ($height > $maxHeightRestriction) {
			return self::ERROR_IMAGE_MAX_HEIGHT;
		}
	}

	public function checkRatio($file)
	{
		$widthRatioRestriction  = $this->getOption(self::IMAGE_RATIO_WIDTH);
		$heightRatioRestriction = $this->getOption(self::IMAGE_RATIO_HEIGHT);

		if (null === $widthRatioRestriction || null === $heightRatioRestriction) {

			// ratio cannot be defined
			return true;
		}

		list($width, $height) = $this->getImageSizes($file);
		$ratioRestriction     = round($widthRatioRestriction / $heightRatioRestriction, 2);
		$ratio			      = round($width / $height, 2);

		if ($ratio <> $ratioRestriction) {
			return self::ERROR_IMAGE_RATIO;
		}
	}

	public function upload($tmpFile, $filename)
	{
		$extension   = pathinfo($filename, PATHINFO_EXTENSION);
		$newFilename = time() . '-' . $this->uploadRank++ . '.' . $extension;

		list($width, $height) = $this->getImageSizes($tmpFile);

		if (true === move_uploaded_file($tmpFile, $this->getDestination() . DIRECTORY_SEPARATOR . $newFilename)) {

			$this->done[] = ['old' => $filename, 'new' => $newFilename, 'width' => $width, 'height' => $height, 'directory' => basename($this->getDestination())];

		} else {

			$this->addError(self::ERROR_IMAGE_UPLOAD, $filename);
		}
	}

	public function getUploadedFiles()
	{
		return $this->done;
	}

	protected function getImageSizes($file) {

		if (null === $this->imageSizes[$file]) {
			$this->imageSizes[$file] = getimagesize($file);
		}

		return $this->imageSizes[$file];
	}
}