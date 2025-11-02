<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     *
     * @var array<int, string>
     */
    protected $except = [
    ];

    /**
     *
     * @var array
     */
    protected $except_cookies = [
        'download_finished',
    ];
    
    /**
     *
     * @param  string  $name
     * @return bool
     */
    protected function shouldEncrypt($name)
    {
        return ! in_array($name, $this->except_cookies) && parent::shouldEncrypt($name);
    }
}