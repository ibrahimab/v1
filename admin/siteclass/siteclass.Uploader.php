<?php

class Uploader
{
	protected $inputName;
	protected $html;
	protected $multiple;
	protected $options;
	
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
	
	public function generateField()
	{
		$htmlOptions 			= '';
		$this->html['type']     = 'file';
		$this->html['name'] 	= $this->inputName;
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