<?php
namespace Machaven\TrackAttempts;

/**
 * Interface TrackAttemptsInterface
 * @package Machaven\TrackAttempts
 *
 * @todo: Document functions here
 */
interface TrackAttemptsInterface
{
    public function increment();
    public function getCount();
    public function isLimitReached();
    public function getTimeUntilExpired();
    public function clear();
}