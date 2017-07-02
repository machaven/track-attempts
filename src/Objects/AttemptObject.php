<?php

namespace Machaven\TrackAttempts\Objects;

class AttemptObject
{
    /**
     * @var int $attempts The number of attempts that have been recorded by the increment function
     */
    public $attempts;

    /**
     * @var int $expires Unix timestamp of when the attempts expire
     */
    public $expires;

    public function __construct($attempts, $expireTime)
    {
        $this->attempts = $attempts;
        $this->expires = $expireTime;
    }
}