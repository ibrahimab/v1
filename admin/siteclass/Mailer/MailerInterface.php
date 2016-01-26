<?php

namespace Chalet\Mailer;

/**
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
interface MailerInterface
{
    /**
     * @param string $subject
     */
    public function setSubject($subject);

    /**
     * @param string      $to
     * @param string|null $name
     */
    public function setTo($to, $name = null);

    /**
     * @param string      $from
     * @param string|null $name
     */
    public function setFrom($from, $name = null);

    /**
     * @param string $body
     * @param string $multipart
     */
    public function setBody($body, $multipart);

    /**
     * @return bool
     */
    public function send();
}