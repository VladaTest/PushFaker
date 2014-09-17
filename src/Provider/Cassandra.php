<?php

namespace Provider;

use Provider;

class Dump extends Dump
{
    public function runSelect1($data)
    {
        $table    = str_pad($data['space_id'],6,'0',STR_PAD_LEFT);
        $nodes    = config()->db['ip'];
        $database = new \evseevnn\Cassandra\Database($nodes, 'dbtesting');
        $database->connect();

        foreach ($data["raw"]["values"] as $key) {
            foreach ($data["raw"]["attributes"] as $key_attr) {
                $queries[] = str_replace("$", "", "3|".$key)."_".$key_attr;
            }

            $queries[] = str_replace("$", "", "3|".$key);
        }

        for ($i=0;$i<30;$i++) {
            $final_result =0;
            $key = $queries[rand(0,(count($queries)-1))];
            $from = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -".rand(3,50000)." min"));
            $to = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -".rand(0,500)." min"));
            $sql = 'SELECT v FROM space'.$table.' WHERE k= \''.$key.'\' AND n=\'\' AND d>=\''.$from.'\' AND  d<= \''.$to.'\'';

            //echo "<br />".$sql;
            $data = $database->query($sql, []);
            $final_result = 0;
            foreach($data as $row) {
                $final_result += $row["v"];
            }

            echo "FINAL RESULT : ".$final_result;
        }
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
