<?php

class ImageUploader extends Uploader
{
	const IMAGE_WIDTH              = 1;
	const IMAGE_HEIGHT 	           = 2;
	const IMAGE_MIN_WIDTH          = 3;
	const IMAGE_MIN_HEIGHT         = 4;
	const IMAGE_MAX_WIDTH          = 5;
	const IMAGE_MAX_HEIGHT         = 6;
	const IMAGE_RATIO_WIDTH        = 7;
	const IMAGE_RATIO_HEIGHT       = 8;
	
	const ERROR_IMAGE_WIDTH        = 100;
	const ERROR_IMAGE_HEIGHT 	   = 200;
	const ERROR_IMAGE_MIN_WIDTH    = 300;
	const ERROR_IMAGE_MIN_HEIGHT   = 400;
	const ERROR_IMAGE_MAX_WIDTH    = 500;
	const ERROR_IMAGE_MAX_HEIGHT   = 600;
	const ERROR_IMAGE_RATIO_WIDTH  = 700;
	const ERROR_IMAGE_RATIO_HEIGHT = 800;

	protected $imageSizes;
	protected $errors;
	
	public function __construct($inputName, $multiple = false, $html = [])
	{
		parent::__construct($inputName, $multiple, $html);
		$this->imageSizes = [];
	}
	
	public function check($filesName)
	{
		foreach ($_FILES[$filesName]['tmp_name'] as $inputName => $tmpFile) {
			
			if ($inputName === $this->inputName) {
				
				$this->checkWidth($tmpFile);
				$this->checkHeight($tmpFile);
				$this->checkMinWidth($tmpFile);
				$this->checkMinHeight($tmpFile);
				$this->checkMaxWidth($tmpFile);
				$this->checkMaxHeight($tmpFile);
				$this->checkRatio($tmpFile);
			}
		}
	}
	
	protected function getImageSizes($file) {
		
		if (null === $this->imageSizes[$file]) {
			$this->imageSizes[$tmpFile] = getimagesize($file);
		}
		
		return $this->imageSizes[$tmpFile];
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
			
			$exception = new ImageUploaderException(vsprintf('Width restriction will not allow this file to be uploaded (%d uploaded, has to be %d)', [$width, $widthRestriction]), self::ERROR_IMAGE_WIDTH);
			$exception->setData('width', $width);
			$exception->setData('height', $height);
			$exception->setData('file', $file);
			
			throw $exception;
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
			
			$exception = new ImageUploaderException(vsprintf('Height restriction will not allow this file to be uploaded (%d uploaded, has to be %d)', [$height, $heightRestriction]), self::ERROR_IMAGE_HEIGHT);
			$exception->setData('width', $width);
			$exception->setData('height', $height);
			$exception->setData('file', $file);
			
			throw $exception;
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
		
		if ($width > $minWidthRestriction) {
			
			$exception = new ImageUploaderException(vsprintf('Minimum width restriction will not allow this file to be uploaded (%d uploaded, has to be %d)', [$width, $minWidthRestriction]), self::ERROR_IMAGE_MIN_WIDTH);
			$exception->setData('width', $width);
			$exception->setData('height', $height);
			$exception->setData('file', $file);
			
			throw $exception;
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
		
		if ($height > $minHeightRestriction) {
			
			$exception = new ImageUploaderException(vsprintf('Minimum height restriction will not allow this file to be uploaded (%d uploaded, has to be %d)', [$height, $minHeightRestriction]), self::ERROR_IMAGE_MIN_HEIGHT);
			$exception->setData('width', $width);
			$exception->setData('height', $height);
			$exception->setData('file', $file);
			
			throw $exception;
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
			
			$exception = new ImageUploaderException(vsprintf('Maximum width restriction will not allow this file to be uploaded (%d uploaded, has to be %d)', [$width, $maxWidthRestriction]), self::ERROR_IMAGE_MAX_WIDTH);
			$exception->setData('width', $width);
			$exception->setData('height', $height);
			$exception->setData('file', $file);
			
			throw $exception;
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
			
			$exception = new ImageUploaderException(vsprintf('Maximum height restriction will not allow this file to be uploaded (%d uploaded, has to be %d)', [$height, $maxHeightRestriction]), self::ERROR_IMAGE_MAX_HEIGHT);
			$exception->setData('width', $width);
			$exception->setData('height', $height);
			$exception->setData('file', $file);
			
			throw $exception;
		}
	}
	
	public function checkRatio($file)
	{
		$widthRatioRestriction  = $this->getOption(self::IMAGE_RATIO_WIDTH);
		$heightRatioRestriction = $this->getOption(self::IMAGE_RATIO_HEIGHT);
		
		if (null === $widthRatioRestriction || null === $heightRatioRestriction) {
			
			// ratio cannot be defined
			return;
		}
		
		list($width, $height) = $this->getImageSizes($file);
		$ratioRestriction     = round($widthRatioRestriction / $heightRatioRestriction, 2);
		$ratio			      = round($width / $height, 2);
		
		if ($ratio <> $ratioRestriction) {
			
			$exception = new ImageUploaderException('Ratio restriction will not allow this file to be uploaded (%d uploaded, has to be %d)', [$ratio, $ratioRestriction]);
			$exception->setData('width',  $width);
			$exception->setData('height', $height);
			$exception->setData('ratio',  $ratio);
			$exception->setData('file',   $file);
			
			throw $exception;
		}
	}
}