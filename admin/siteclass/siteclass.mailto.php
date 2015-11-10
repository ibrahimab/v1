<?php

/**
* class to create mailto-links
*
* @author Jeroen Boschman (jeroen@webtastic.nl)
* @since  2015-11-10 13:00
*/
class mailto
{

	private $link;
	private $to;
	private $subject;
	private $body;

	/**
	 * set the to field
	 *
	 * @param string $o
	 * @return $this
	 */
	public function to($to)
	{
		$this->to = $to;

		return $this;

	}

	/**
	 * set the subject field
	 *
	 * @param string $subject
	 * @return $this
	 */
	public function subject($subject)
	{
		$this->subject = $subject;

		return $this;

	}

	/**
	 * set the plaintext body
	 *
	 * @param string $body
	 * @return $this
	 */
	public function body($body)
	{
		$this->body = $body;

		return $this;

	}

	/**
	 * generate the mailto-link
	 *
	 * @return static
	 */
	public static function generate()
	{
		return new static;
	}

	/**
	 * internally generate the mailto-link
	 *
	 * @return string $link
	 */
	public function createLink()
	{
		$link = "mailto:";
		$link .= $this->escape($this->to);
		$link .= "?subject=".$this->escape($this->subject);
		$link .= "&body=".$this->escape($this->body);

		return $link;
	}

	/**
	 * escape to url-safe string
	 *
	 * @param string $string
	 * @return string
	 */
	private function escape($string)
	{

		$escaped_string = $string;

		$escaped_string = preg_replace("/ /","%20",$escaped_string);
		$escaped_string = preg_replace("/\n/","%0D%0A",$escaped_string);
		$escaped_string = preg_replace("/&/","%26",$escaped_string);
		$escaped_string = preg_replace("/\"/","%22",$escaped_string);
		$escaped_string = preg_replace("/'/","%27",$escaped_string);

		return $escaped_string;

	}

	/**
	 * magic method to output as string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->createLink();
	}
}
