<?php

namespace Chalet\Encoder;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
interface EncoderInterface
{
	/**
	 * @param integer $encoding Set encoding using numeric identifiers
	 */
	public function setEncoding($encoding);

	/**
	 * This method encodes $data with the encoding defined with setEncoding.
	 * It will return false if text is either not a string, encoding failed, or
	 * a wrong encoding was defined.
	 *
	 * @param mixed $text
	 *
	 * @return string|bool
	 */
	public function encode($data);
}