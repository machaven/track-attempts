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

    private function setKey($attemptObject, $expireSeconds)
    {
        Cache::put($this->trackingKey, $attemptObject, $expireSeconds);
    }

    public function clear()
    {
        return Cache::forget($this->trackingKey);
    }
}
