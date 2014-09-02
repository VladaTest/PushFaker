<?php

abstract class Provider
{
    const KPI_COUNT = 1;

    private $data;

    public static function factory($providerName, $data)
    {
        $className = '\Provider\\' . ucfirst($providerName);
        if (!class_exists($className)) {
            throw new Exception("Provider $className not defined");
        }

        $o = new $className($data);

        return $o;
    }

    public function __construct($data)
    {
        $this->setData($data);
    }

    public function setData($data)
    {
        if (!isset($data['kpis'])) {
            // Generate 100 Kpis
            $faker = Faker\Factory::create();
            for ($i = 0; $i < self::KPI_COUNT; $i++) {
                $kpi            = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $faker->name));
                $data['kpis'][] = trim($kpi, '_');
            }
        }

        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function run($operation)
    {
        $data = $this->data;

        if ($operation === 'push') {
            // generate random data
            $data['data'] = [];

            $start = isset($data['to'])
                ? new \DateTime()
                : new \DateTime($data['from']);

            $end = new \DateTime();

            $rangeInterval = "PT{$data['granularity']}S";
            while ($start <= $end) {
                foreach ($data['kpis'] as $kpi) {
                    $data['data'][] = [
                        'key'   => $kpi,
                        'date'  => $start->format('Y-m-d H:i:s'),
                        'value' => rand(10000, 1000000)
                    ];
                }
                $start->add(new \DateInterval($rangeInterval));
            }
        }

        $method = 'run' . ucfirst($operation);
        if (!method_exists($this, $method)) {
            throw new Exception("Method $method doesn't exist.");
        }

        $this->$method($data);
    }
}
