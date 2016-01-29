<?php
/**
 * @author  Ibrahim
 * @package Chalet
 */
class Logger implements \LoggerInterface
{
    /**
     * @var string
     */
    private $collector;

    /**
     * Constructor
     *
     * @param string $collector
     */
    public function __construct($collector = 'messages')
    {
        throw new \Exception('test');
        $this->setCollector($collector);
    }

    /**
     * @param string $collector
     * @return LoggerInterface
     */
    public function setCollector($collector)
    {
        $this->collector = $collector;

        return $this;
    }

    /**
     * @param string
     * @return LoggerInterface
     */
    public function error($message)
    {
        return $this->log($message, 'error');
    }

    /**
     * @param string $message
     * @param string $label
     * @param string $collector
     * @return LoggerInterface
     */
    public function log($message, $label = 'info')
    {
        \wt_debugbar_message($message, $label, $this->collector);

        return $this;
    }
}