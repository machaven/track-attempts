<?php

namespace Machaven\TrackAttempts\Traits;

use Machaven\TrackAttempts\Objects\AttemptObject;

trait CommonTrait
{

    /**
     * @param int $attempts Initial attempt count
     * @param int $ttl Time to live in seconds
     * @return AttemptObject
     */
    private function createAttemptObject($attempts = 0, $ttl)
    {
        return new AttemptObject($attempts, time() + $ttl);
    }

    /**
     * @param int $expires Unix timestamp of expiry time
     * @return int
     */
    private function getTimeUntilExpireCalculation($expires)
    {
        return ($expires - time() < 0) ? 0 : $expires - time();
    }

    private function invalidateIfExpired()
    {
        if ($this->keyExists() && $this->getTimeUntilExpireCalculation($this->getAttemptObject()->expires) === 0) {
            $this->clear();
        }
    }
}