<?php
class ImageUploaderException extends \Exception
{	
	protected $data;
		
	public function setData($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	public function getData($key)
	{
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}
}