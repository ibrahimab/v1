<?php

namespace Chalet\Encoder;

/**
 * @author  Jeroen Boschman <jeroen@webtastic.nl>
 * @package Chalet
 */
class Encoder implements EncoderInterface
{
    /**
     * @var integer
     */
    const ENCODING_PHP_DEFAULT  = 1;

    /**
     * @var integer
     */
    const ENCODING_WINDOWS_1252 = 2;

    /**
     * @var integer
     */
    const ENCODING_UTF8         = 3;

    /**
     * @var array
     */
    private $allowedEncoding    = [self::ENCODING_PHP_DEFAULT, self::ENCODING_WINDOWS_1252, self::ENCODING_UTF8];

    /**
     * Constructor
     *
     * @param integer|null $encoding
     */
    public function __construct($encoding = self::ENCODING_PHP_DEFAULT)
    {
        $this->encoding = $encoding;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = (in_array($encoding, $this->allowedEncoding) ? $encoding : self::ENCODING_PHP_DEFAULT);
    }

    /**
     * @param string $text
     *
     * @return string|bool
     */
    public function encode($text)
    {
        if (false === is_string($text)) {
            return false;
        }

        $result = false;

        switch ($this->encoding) {

            case self::ENCODING_WINDOWS_1252:
                $result = htmlentities($text, ENT_COMPAT, 'cp1252');
            break;

            case self::ENCODING_UTF8:
                $result = htmlentities($text, ENT_COMPAT, 'UTF-8');
            break;

            case self::ENCODING_PHP_DEFAULT:
            default:
                $result = htmlentities($text);
            break;
        }

        return $result;
    }

    public function fix($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[iconv('CP1252', 'UTF-8', $key)] = (is_array($value) ? self::fix($value) : iconv('CP1252', 'UTF-8', $value));
        }

        return $result;
    }
}