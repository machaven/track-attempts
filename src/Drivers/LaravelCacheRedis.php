<?php

namespace Machaven\TrackAttempts\Drivers;

use Machaven\TrackAttempts\Objects\AttemptObject;
use Machaven\TrackAttempts\TrackAttemptsInterface;
use Machaven\TrackAttempts\Traits\CommonTrait;
use Machaven\TrackAttempts\Traits\ConfigTrait;
use Illuminate\Support\Facades\Cache;

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

    public function increment()
    {

        if (Cache::has($this->redisKey)) {
            $attemptObject = $this->getAttemptObject();
            $attemptObject->attempts++;
            $expiresFromNow = (time() - $attemptObject->expires) * 60;
            Cache::put($this->redisKey, $attemptObject, $expiresFromNow);
        } else {
            $expireSeconds = $this->ttlInMinutes * 60;

            $attemptObject = new AttemptObject();
            $attemptObject->attempts++;
            $attemptObject->expires = time() + $expireSeconds;

            Cache::put($this->redisKey, $attemptObject, $this->ttlInMinutes);
        }
    }

    public function getCount()
    {
        if (Cache::has($this->redisKey)) {
            return $this->getAttemptObject()->attempts;
        }

        return 0;
    }

    public function isLimitReached()
    {
        if (Cache::has($this->redisKey)) {
            if ($this->getCount() >= $this->maxAttempts) {
                return true;
            }
        }

        return false;
    }

    public function getTimeUntilExpired()
    {
        if (Cache::has($this->redisKey)) {
            return $this->getTimeUntilExpireCalculation($this->getAttemptObject()->expires);
        }

        return 0;
    }

    public function clear()
    {
        return Cache::forget($this->redisKey);
    }
}