<?php

use Foundry\System\Mix;

if (! function_exists('theme_mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $theme
     * @param  string  $path
     * @param  string  $manifestDirectory
     * @return \Illuminate\Support\HtmlString|string
     *
     * @throws \Exception
     */
    function theme_mix($theme, $path, $manifestDirectory = '')
    {
        return app(Mix::class)(...func_get_args());
    }
}
