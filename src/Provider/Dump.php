<?php

namespace Provider;

use Provider;

class Dump extends Provider
{
    public function runPush($data)
    {
        usleep(1000 * rand(500, 5000));

        $payload = json_encode([
            'data' => $data['data']
        ]);

        $filename = '/tmp/' . time() . '.json';

        file_put_contents($filename, $payload);

        // exec("curl -X POST http://requestb.in/10quws91 --data @$filename");
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
