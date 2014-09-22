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

        $client = new \GuzzleHttp\Client();
        // $req    = $client->createRequest('POST', 'http://casandra1.cloudapp.net:8080', [
        //     'headers' => ['Content-Type' => 'application/json'],
        //     'auth'    => [$data['id'], ''],
        //     'body'    => $payload
        // ]);
        // $client->sendAll([$req]);

        echo "Space ID: {$data['space_id']}\n";

        $client->post('http://casandra1.cloudapp.net:8080', [
            'headers' => ['Content-Type' => 'application/json'],
            'auth'    => [$data['token'], ''],
            'body'    => $payload
        ]);
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
