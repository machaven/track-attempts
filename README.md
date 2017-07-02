Track Attempts Library
=========================

A handy library to track any type of action and limit the amount of attempts made in a period of time frame.

Useful things to track and limit:

* Login attempts.
* One Time Pin (OTP) attempts.
* Any thing else you want to limit or monitor.

Features
--------

* Track on any metric you want, like email, username, etc.
* Configurable limits.
* Multiple backend drivers:
    - Redis (Predis Client)
    - Laravel (Cache Facade)

Install
--------

```composer require machaven/track-attempts```

Configuration
--------
Minimum Configuration
```
$config = ['userIdentifier' => $username];
$attempts = new \Machaven\TrackAttempts\Drivers\Predis($config);

```
Full Configuration
```
$config = [
    'userIdentifier' => $username, // A variable with a unique identifier for the session/user
    'maxAttempts' => 3, // Max attempts limit
    'systemName' => 'my-website', // System Identifier used in cache key prefix.
    'ttlInMinutes' => 5, // Keep track of attempts in a five minute period.
    'actionName' => 'login', // The name of the action you are tracking.
    ];
$attempts = new \Machaven\TrackAttempts\Drivers\Predis($config);
```

The configuration above will create a key named: my-website:login:$username. 

Usage
--------

Keeping count
```
>>> $attempts->increment();
=> true
```

Getting the count
```
>>> $attempts->getCount();
=> 1
```

Checking if the limit is reached
```
>>> $attempts->isLimitReached();
=> false
```

Clearing all attempts
```
>>> $attempts->clear();
=> true
```

Checking the time left before the count expires (in seconds)
```
>>> $attempts->getTimeUntilExpired();
=> 188
```

Using increment to track and check (example of max limit of 3 increments)
```
>>> $attempts->increment();
=> true
>>> $attempts->increment();
=> true
>>> $attempts->increment();
=> true
>>> $attempts->increment();
=> false
```