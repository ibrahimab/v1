<?php
/**
 * LoggerInterface
 *
 * @author  Ibrahim Abdullah <ibrahim@chalet.nl>
 * @package Chalet
 */
interface LoggerInterface
{
    /**
     * @param string $collector
     * @return LoggerInterface
     */
    public function setCollector($collector);

    /**
     * @param string
     */
    public function log($message, $label = 'info');
    public function error($message);
}