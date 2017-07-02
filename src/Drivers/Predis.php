<?php

namespace Machaven\TrackAttempts\Drivers;

use Dotenv\Dotenv;
use Machaven\TrackAttempts\Objects\AttemptObject;
use Machaven\TrackAttempts\TrackAttemptsInterface;
use Machaven\TrackAttempts\Traits\CommonTrait;
use Machaven\TrackAttempts\Traits\ConfigTrait;
use Predis\Client as PredisClient;

class Predis implements TrackAttemptsInterface
{
    use ConfigTrait, CommonTrait;

    /**
     * @var \Predis\Client;
     */
    private $redis;

    public function __construct(array $config)
    {
        $this->config($config);
        $this->setup();
    }

    private function setup()
    {
        $dotenv = new Dotenv(__DIR__ . '/../../../../../');
        $dotenv->load();

        $this->redis = new PredisClient([
            'scheme' => getenv('REDIS_SCHEME'),
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
        ], [
            'parameters' => [
                'database' => getenv('REDIS_DB'),
            ],
            'profile' => getenv('REDIS_PROFILE'),
        ]);
    }

    private function getAttemptObject()
    {
        return unserialize($this->redis->get($this->redisKey));
    }

    public function increment()
    {
        if ($this->isLimitReached()) {
            return false;
        }

        if ($this->redis->exists($this->redisKey)) {
            $attemptObject = $this->getAttemptObject();
            $attemptObject->attempts++;
            $this->redis->set($this->redisKey, serialize($attemptObject));
        } else {
            $expireSeconds = $this->ttlInMinutes * 60;

            $attemptObject = new AttemptObject();
            $attemptObject->attempts++;
            $attemptObject->expires = time() + $expireSeconds;

            $this->redis->set($this->redisKey, serialize($attemptObject));
            $this->redis->expire($this->redisKey, $expireSeconds);
        }

        return true;
    }

    public function getCount()
    {
        if ($this->redis->exists($this->redisKey)) {
            return $this->getAttemptObject()->attempts;
        }

        return 0;
    }

    public function isLimitReached()
    {
        if ($this->redis->exists($this->redisKey)) {

            if ($this->getCount() >= $this->maxAttempts) {
                return true;
            }
        }

        return false;
    }

    public function getTimeUntilExpired()
    {
        if ($this->redis->exists($this->redisKey)) {
            return $this->getTimeUntilExpireCalculation($this->getAttemptObject()->expires);
        }

        return 0;
    }

    public function clear()
    {
        return (bool)$this->redis->del([$this->redisKey]);
    }
}