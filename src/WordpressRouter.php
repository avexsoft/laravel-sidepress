<?php

namespace Avexsoft\Sidepress;

use Exception;
use Str;

class WordpressRouter
{
    public function __invoke()
    {
        if (config('sidepress.enabled')) {
            $uri = request()->getRequestUri();

            if (Str::substrCount($uri, 'sidep') > 10) {
                throw new Exception("Too many redirects. Ran the `php artisan sidepress:install` command yet?");
            }

            $uri .= (Str::contains($uri, "?") ? "&" : "?") . "sidep=1";

            return redirect($uri);
        }
    }
}
