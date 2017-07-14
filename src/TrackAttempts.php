<?php

namespace Machaven\TrackAttempts;

class TrackAttempts
{
    /**
     * @var Drivers\Predis|Drivers\LaravelCache
     */
    private $trackAttempts;

    /**
     * TrackAttempts constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (!isset($config['driver']) || empty($config['driver'])) {
            throw new \InvalidArgumentException('Missing driver argument.');
        }

        switch (strtolower($config['driver'])) {
            case 'predis':
                $this->trackAttempts = new Drivers\Predis($config);
                break;
            case 'laravel':
                $this->trackAttempts = new Drivers\LaravelCache($config);
                break;
            default:
                throw new \InvalidArgumentException('Unknown driver argument.');
        }
    }

    /**
     * @return Drivers\Predis|Drivers\LaravelCache
     */
    public function getDriver()
    {
        return $this->trackAttempts;
    }
}