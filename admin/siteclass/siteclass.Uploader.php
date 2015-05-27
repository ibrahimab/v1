<?php

abstract class Uploader
{
	protected $inputName;
	protected $html;
	protected $multiple;
	protected $options;
	protected $destination;

	public function __construct($inputName, $multiple = false, $html = [])
	{
		$this->inputName = $inputName;
		$this->multiple  = $multiple;
		$this->html      = $html;
		$this->options	 = [];
	}

	public function setOption($option, $value)
	{
		$this->options[$option] = $value;
	}

	public function getOption($option, $default = null)
	{
		return isset($this->options[$option]) ? $this->options[$option] : $default;
	}

	public function setDestination($destination)
	{
		// normalizing destination
		$this->destination = rtrim(trim($destination), DIRECTORY_SEPARATOR);

		if (false === file_exists($this->destination)) {
			mkdir($this->destination);
		}
	}

	public function getDestination()
	{
		return $this->destination;
	}

	public function generateField()
	{
		$htmlOptions 			= '';
		$this->html['type']     = 'file';
		$this->html['name'] 	= $this->inputName . ($this->multiple === true ? '[]' : '');
		$this->html['multiple'] = true === $this->multiple;

		foreach ($this->html as $attribute => $value) {

			if (is_bool($value) && true === $value) {
				$htmlOptions .= vsprintf('%s=%s ', [$attribute, $attribute]);
			} else if (false === is_bool($value)) {
				$htmlOptions .= vsprintf('%s=%s ', [$attribute, $value]);
			}
		}

		return '<input ' . trim($htmlOptions) . ' />';
	}
}