<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'facebook' => [
        'client_id' => '869361533241379',
        'client_secret' => '67ba2c575a96bd793f6879f42626cd84',
        'redirect' => 'http://127.0.0.1/StreamixLive/save-user',
    ],
    /*'twitter' => [
        'client_id' => '4QM3WP6IBOcFX0GQ6vt4BeXy9',
        'client_secret' => '281iyK7QFk2wiNHwxJYDM2ZOBKTJvhzgJhR05Y9YpKwlEEOmYN',
        'redirect' => 'http://127.0.0.1/StreamixLive/save-user',
    ],*/
    'twitter' => [
        'client_id' => 'ToRN8xEppdP2ZeX6T5PfTZIYr',
        'client_secret' => 'IittLm4Qho1RDENb779UX08jv8wKdUpKiRxq6ICVHs14CJuPGg',
        'redirect' => 'http://127.0.0.1/StreamixLive/save-user',
    ],
    'google' => [
        'client_id' => '153450738004-jrq2c2ud6k0j7h8gef1ut08h21juc77s.apps.googleusercontent.com',
        'client_secret' => 'zCANHLmvHmEQd0LYHn7QOI_z',
        'redirect' => 'http://100.10.28.37/StreamixLive/save-user',
    ],
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

];
