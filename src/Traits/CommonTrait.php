<?php

namespace Machaven\TrackAttempts\Traits;

trait CommonTrait
{
    /**
     * @param int $expires Unix timestamp of expiry time
     * @return int
     */
    private function getTimeUntilExpireCalculation($expires)
    {
        return ($expires - time() < 0) ? 0 : $expires - time();
    }
}