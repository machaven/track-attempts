<?php

namespace Machaven\TrackAttempts\Drivers;

use Illuminate\Support\Facades\Cache;
use Machaven\TrackAttempts\TrackAttemptsInterface;
use Machaven\TrackAttempts\Traits\CommonTrait;
use Machaven\TrackAttempts\Traits\ConfigTrait;

class LaravelCacheRedis implements TrackAttemptsInterface
{
    use ConfigTrait, CommonTrait;

    public function __construct(array $config)
    {
        $this->config($config);
    }

    private function getAttemptObject()
    {
        return Cache::get($this->redisKey);
    }

    private function keyExists()
    {
        return Cache::has($this->redisKey);
    }

    public function increment()
    {
        $this->invalidateIfExpired();

        if ($this->keyExists()) {
            $attemptObject = $this->getAttemptObject();
            $attemptObject->attempts++;
            $expiresFromNow = (time() - $attemptObject->expires) * 60;
            Cache::put($this->redisKey, $attemptObject, $expiresFromNow);
        } else {
            $expireSeconds = $this->ttlInMinutes * 60;
            $attemptObject = $this->createAttemptObject(1, $expireSeconds);

            Cache::put($this->redisKey, $attemptObject, $this->ttlInMinutes);
        }
    }

    public function getCount()
    {
        $this->invalidateIfExpired();

        if ($this->keyExists()) {
            return $this->getAttemptObject()->attempts;
        }

        return 0;
    }

    public function isLimitReached()
    {
        $this->invalidateIfExpired();

        if ($this->keyExists()) {
            if ($this->getCount() >= $this->maxAttempts) {
                return true;
            }
        }

        return false;
    }

    public function getTimeUntilExpired()
    {
        $this->invalidateIfExpired();

        if ($this->keyExists()) {
            return $this->getTimeUntilExpireCalculation($this->getAttemptObject()->expires);
        }

        return 0;
    }

    public function clear()
    {
        return Cache::forget($this->redisKey);
    }
}