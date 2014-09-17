<?php

namespace Provider;

use Provider;

class Percona extends Provider
{
    private $ip;
    private $username;
    private $password;

    public function __construct($data)
    {
        parent::__construct($data);

        $this->ip       = config()->db['ip'];
        $this->username = config()->db['username'];
        $this->password = config()->db['password'];
    }

    public function runPush($data)
    {
        $con = mysqli_connect($this->ip, $this->username, $this->password);
        if (!$con) {
            die('Could not connect: ' . mysql_error());
        }

        mysqli_select_db($con, 'test');
        mysqli_set_charset($con,'utf8');

        $source_id = $data['token'];
        foreach ($data["data"] as $record) {
            $sql = " INSERT INTO rawdata (`space_id`, `datetime`, `key`,`value`)
                     VALUES ('".$source_id."','".$record["date"]."','".$record["key"]."','".$record["value"]."') ";
            var_dump($sql);
            $res = mysqli_query($con, $sql);
            var_dump($res);
        }
    }

    public function runSelect1($data)
    {
        $con = mysqli_connect($this->ip, $this->username, $this->password);
        if (!$con) {
            die('Could not connect: ' . mysql_error());
        }

        mysqli_select_db($con, 'test');
        mysqli_set_charset($con,'utf8');

        $space_id = $data['token'];
        $sql      = " SELECT SUM(`value`) as a FROM rawdata WHERE `key`= '".$data["kpis"][0]."' AND space_id='".$space_id."' GROUP BY `key` ";
        $res      = mysqli_query($con, $sql);

        var_dump($sql);
        var_dump($res);
    }

    public function runSelect2($data)
    {
        $con = mysqli_connect($this->ip, $this->username, $this->password);
        if (!$con) {
            die('Could not connect: ' . mysql_error());
        }

        mysqli_select_db($con, 'test');
        mysqli_set_charset($con,'utf8');

       $space_id = $data['token'];
       $sql      = " SELECT COUNT(`value`) as b FROM rawdata WHERE `key`= '".$data["kpis"][0]."' AND space_id='".$space_id."' GROUP BY `key` ";
       $res      = mysqli_query($con, $sql);

       var_dump($sql);
       var_dump($res);
    }

    public function runSelect3($data)
    {
        $con = mysqli_connect($this->ip, $this->username, $this->password);
        if (!$con) {
            die('Could not connect: ' . mysql_error());
        }

        mysqli_select_db($con, 'test');
        mysqli_set_charset($con,'utf8');

        $space_id = $data['token'];
        $sql      = " SELECT MAX(`value`) as c FROM rawdata WHERE `key`= '".$data["kpis"][0]."' AND space_id='".$space_id."' GROUP BY `key` ";
        $res      = mysqli_query($con, $sql);

        var_dump($sql);
        var_dump($res);
    }
}
