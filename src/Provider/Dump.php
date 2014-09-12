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

        $client = new \Guzzle\Http\Client();
        $req    = $client->post('http://casandra1.cloudapp.net:8080');
        $req->setAuth($data['id'], '');
        $req->setBody($payload);
        $req->setHeader('Content-Type', 'application/json');

        $client->sendAll([$req]);
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
