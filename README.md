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

Class Configuration
--------
Minimum Configuration
```
$config = ['driver' => 'predis', userIdentifier' => $username];
$attempts = (new \Machaven\TrackAttempts\TrackAttempts($config))->getDriver();

```
Full Configuration
```
$config = [
    'driver' => 'predis', // Driver to use ('predis' or 'laravel')
    'userIdentifier' => $username, // A variable with a unique identifier for the session/user
    'maxAttempts' => 3, // Max attempts limit
    'systemName' => 'my-website', // System Identifier used in cache key prefix.
    'ttlInMinutes' => 5, // Keep track of attempts in a five minute period.
    'actionName' => 'login', // The name of the action you are tracking.
    ];
$attempts = (new \Machaven\TrackAttempts\TrackAttempts($config))->getDriver();
```

The configuration above will create a key named: my-website:login:$username.

Predis Driver Configuration
--------
The predis driver requires redis settings to be configured in a .env file in your project root folder.

Example .env:
```
REDIS_SCHEME=tcp
REDIS_HOST=localhost
REDIS_PASSWORD=
REDIS_PORT=6379
REDIS_DB=0
REDIS_PROFILE=3.2
``` 

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

Using increment to track and check (example of max limit of 3 attempts)
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