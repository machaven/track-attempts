<?php

namespace Machaven\TrackAttempts\Drivers;

use Illuminate\Support\Facades\Cache;
use Machaven\TrackAttempts\TrackAttemptsInterface;
use Machaven\TrackAttempts\Traits\CommonTrait;
use Machaven\TrackAttempts\Traits\ConfigTrait;

class LaravelCache implements TrackAttemptsInterface
{
    use ConfigTrait, CommonTrait;

    public function __construct(array $config)
    {
        $this->config($config);
    }

    private function getAttemptObject()
    {
        return Cache::get($this->trackingKey);
    }

    private function keyExists()
    {
        return Cache::get($this->trackingKey) !== null;
    }

    public function increment()
    {
        $this->invalidateIfExpired();

        if ($this->isLimitReached()) {
            return false;
        }

        if ($this->keyExists()) {
            $attemptObject = $this->getAttemptObject();
            $attemptObject->attempts++;
            $expiresFromNow = $this->getTimeUntilExpireCalculation($attemptObject->expires) / 60;
            Cache::put($this->trackingKey, $attemptObject, $expiresFromNow);
        } else {
            $expireSeconds = $this->ttlInMinutes * 60;
            $attemptObject = $this->createAttemptObject(1, $expireSeconds);

            Cache::put($this->trackingKey, $attemptObject, $this->ttlInMinutes);
        }

        return true;
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
        return Cache::forget($this->trackingKey);
    }
}