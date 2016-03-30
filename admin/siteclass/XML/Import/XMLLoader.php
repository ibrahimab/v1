<?php
namespace Chalet\XML\Import;

class XMLLoader
{
	/**
	 * @param string $xml
	 *
	 * @return SimpleXMLElement
	 * @throws InvalidArgumentException
	 */
	public static function load($xml)
	{
		// supress php errors
		libxml_use_internal_errors(true);

		// try to load xml from string
		$xml = simplexml_load_string($xml);

		// getting errors
		$errors = libxml_get_errors();

		// and clearing the errors
		libxml_clear_errors();

		// enable php warnings for other parts of the system
		libxml_use_internal_errors(false);

		// throw exception if error arises
		if (count($errors) > 0) {
			throw new \InvalidArgumentException('XML string being loaded is not valid');
		}

		return $xml;
	}
}