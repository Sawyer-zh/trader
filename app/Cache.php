<?php

namespace App;

class Cache
{

    public function set($name, $value)
    {
        file_put_contents($name, $value);
    }


    public function get($name)
    {
        return file_get_contents($name);
    }
}
