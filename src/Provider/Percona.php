<?php

namespace Provider;

use Provider;

class Percona extends Provider
{
    public function runPush($data)
    {
        var_dump($data);exit;
        usleep(rand(1000, 1000000));
    }

    public function runSelect1($data)
    {
        usleep(rand(1000, 1000000));
    }

    public function runSelect2($data)
    {
        usleep(rand(1000, 1000000));
    }

    public function runSelect3($data)
    {
        usleep(rand(1000, 1000000));
    }
}
