<?php

namespace Machaven\TrackAttempts\Traits;

trait ConfigTrait
{
    /**
     * @var int $maxAttempts
     */
    private $maxAttempts;

    /**
     * @var string $systemName
     */
    private $systemName;

    /**
     * @var int $ttlInMinutes
     */
    private $ttlInMinutes;

    /**
     * @var string $actionName
     */
    private $actionName;

    /**
     * @var string $userIdentifier
     */
    private $userIdentifier;

    /**
     * @var string $trackingKey
     */
    private $trackingKey;

    /**
     * ConstructorTrait constructor.
     * @param array $config
     *
     * @throws \InvalidArgumentException
     */
    private function config(array $config)
    {
        $this->maxAttempts = (isset($config['maxAttempts']) && is_numeric($config['maxAttempts'])) ? (int)$config['maxAttempts'] : 3;
        $this->systemName = (isset($config['systemName']) && !empty($config['systemName'])) ? $config['systemName'] : 'App';
        $this->ttlInMinutes = (isset($config['ttlInMinutes']) && is_numeric($config['ttlInMinutes'])) ? (int)$config['ttlInMinutes'] : 5;
        $this->actionName = (isset($config['actionName']) && !empty($config['actionName'])) ? $config['actionName'] : 'login';

        if (!isset($config['userIdentifier']) || empty($config['userIdentifier'])) {
            throw new \InvalidArgumentException('Missing user identifier argument.');
        }
        $this->userIdentifier = $config['userIdentifier'];
        $this->trackingKey = $this->systemName . ':' . $this->actionName . ':' . $this->userIdentifier;
    }
}