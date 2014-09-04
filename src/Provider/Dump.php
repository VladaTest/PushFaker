<?php

namespace Provider;

use Provider;

class Dump extends Provider
{
    public function runPush($data)
    {
        usleep(1000 * rand(500, 5000));
    }

    public function runSelect1($data)
    {
        usleep(1000 * rand(500, 5000));
    }

    public function runSelect2($data)
    {
        usleep(1000 * rand(500, 5000));
    }

    public function runSelect3($data)
    {
        usleep(1000 * rand(500, 5000));
    }
}
