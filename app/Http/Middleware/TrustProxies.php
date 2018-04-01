<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    protected $proxies;

    /**
     * The current proxy header mappings.
     *
     * @var array
     */

    // framework changed in 5.6, see framework documentation
    // upgrading from 5.5 to 5.6, 'Trusted Proxies'
    // - leaving the below commented out... If you see this remove it...

//    protected $headers = [
//        Request::HEADER_FORWARDED => 'FORWARDED',
//        Request::HEADER_X_FORWARDED_FOR => 'X_FORWARDED_FOR',
//        Request::HEADER_X_FORWARDED_HOST => 'X_FORWARDED_HOST',
//        Request::HEADER_X_FORWARDED_PORT => 'X_FORWARDED_PORT',
//        Request::HEADER_X_FORWARDED_PROTO => 'X_FORWARDED_PROTO',
//    ];

    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
