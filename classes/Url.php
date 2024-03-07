<?php

class Url
{
    public static function redirect($path)
    {
        if (isset($_SERVER[ 'HTTPS' ]) && 'off' != $_SERVER[ 'HTTPS' ]) {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }

        header("Location: $protocol://" . $_SERVER[ 'HTTP_HOST' ] . $path);
        exit;
    }

}
