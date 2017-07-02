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
        return ($expires - time() <= 0) ? 0 : $expires - time();
    }

    private function invalidateIfExpired()
    {
        if ($this->keyExists() && $this->getTimeUntilExpireCalculation($this->getAttemptObject()->expires) === 0) {
            $this->clear();
        }
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
            $expiresFromNow = $this->getTimeUntilExpireCalculation($attemptObject->expires);
        } else {
            $expiresFromNow = $this->ttlInMinutes * 60;
            $attemptObject = $this->createAttemptObject(1, $expiresFromNow);
        }

        $this->setKey($attemptObject, $expiresFromNow);

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
}