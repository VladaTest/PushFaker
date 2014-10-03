<?php

use Z\Calculations\KPIsUpdater;
use Z\Loader\DataLoader;
use Z\Loader\MetricDBLoader;
use Z\DB\Map\Block;
use Z\DB\Map\Board;
use Z\DB\Map\MetricSetting;
use Z\DB\DAOFactory;
use Z\Loader\SettingsLoader;
use Z\Calculations\KPICalculations;
use Z\DB\Map\Metric;

abstract class Provider
{
    private $data;

    private $faker;

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
        $this->faker = Faker\Factory::create();
        $this->setData($data);
    }

    public function setData($data)
    {
        $isNewClient = !isset($data['raw']['values'])
            || !isset($data['raw']['attributes'])
            || !isset($data['names']);

        // Set raw values for keys
        if (!isset($data['raw']['values'])) {
            for ($i = 0; $i < rand(1, config()->raw_key_count); $i++) {
                $keys = array_slice(config()->raw_key_values, rand(0, count(config()->raw_key_values) -1));
                foreach ($keys as $key) {
                    $data['raw']['values'][] = "{$key}$i";
                }
            }
        }

        // Generate attributes
        if (!isset($data['raw']['attributes'])) {
            for ($i = 0; $i < rand(1, config()->raw_attributes_count); $i++) {
                $attribute                   = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $this->faker->name));
                $data['raw']['attributes'][] = trim($attribute, '_');
            }
        }

        if (!isset($data['names'])) {
            $data['names'] = [];
        }

        if ($isNewClient) {
            $data = $this->createBoard($data);
        }

        $this->data = $data;
    }

    protected function createBoard($data)
    {
        $factory   = DAOFactory::getInstance('Doctrine');
        $metricDao = $factory->createMetric();

        $board = new Board();
        $board = $board
            ->setName("testboard")
            ->setCreated(new \DateTime('now'))
            ->setCreatedBy("tester")
            ->setModified(new \DateTime('now'))
            ->setModifiedBy("tester")
            ->setColor("#FFFFFF  ")
            ->setIsRandom(false)
            ->setOrder(1)
            ->setSpaceId($data['space_id'])
            ->setFromTemplate(1);

        $block = new Block();
        $block = $block
            ->setName("testblock")
            ->setType(2)
            ->setCreated(new \DateTime('now'))
            ->setCreatedBy("tester")
            ->setModified(new \DateTime('now'))
            ->setModifiedBy("tester")
            ->setGranularity(86400)
            ->setGranularityPoints(7)
            ->setGranularityIsFixed(true)
            ->setBoard($board);

        $metricSettingDao  = Factory::createMetricSettingDAO();
        $data['board_kpi'] = [];

        foreach ($data['raw']['values'] as $value) {
            $key                 = "{$data['space_access_id']}|" . trim($value, '$');
            $data['board_kpi'][] = $key;
            $metricSetting       = new MetricSetting();
            $metricSetting       = $metricSetting
                ->setBlock($block)
                ->setMetricKey($key)
                ->setGranularity(MetricSetting::GRANULARITY_DAY)
                ->setGranularityPoints(7)
                ->setSpaceId($data['space_id']);

            $metricSettingDao->save($metricSetting);
        }

        return $data;
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

            $payloads = [];
            foreach ($data['raw']['values'] as $key) {
                // Init. null will be replaced with value in next step
                $payloads[$key] = [
                    $key => null
                ];

                // Add attributes
                $attributes = array_slice($data['raw']['attributes'], rand(0, count($data['raw']['attributes']) -1));
                $indx = 0;
                while ($indx < count($attributes)) {
                    $att = $attributes[$indx];
                    if (rand(0,1000) % 2 === 0 && $indx < count($attributes) -1) {
                        $payloads[$key][$att]        = [];
                        $att2                        = $attributes[++$indx];
                        $payloads[$key][$att][$att2] = $this->faker->country;

                        $k = trim($key, '$') . "|{$att}|{$att2}";
                        $v = $payloads[$key][$att][$att2];
                    } else {
                        $payloads[$key][$att] = $this->faker->country;

                        $k = trim($key, '$') . "|{$att}";
                        $v = $payloads[$key][$att];
                    }

                    if (!isset($this->data['names'][$k])) {
                        $this->data['names'][$k][] = $v;
                    }

                    $indx++;
                }
            }

            $rangeInterval = "PT{$data['granularity']}S";
            while ($start <= $end) {
                foreach ($payloads as $valueKey => &$pl) {
                    $pl[$valueKey] = rand(10000, 1000000);
                    $pl['date']    = $start->format('Y-m-d H:i:s');

                    $data['data'][] = $pl;
                }
                $start->add(new \DateInterval($rangeInterval));
            }
        }

        $method = 'run' . ucfirst($operation);
        if (!method_exists($this, $method)) {
            throw new Exception("Method $method doesn't exist.");
        }

        $tStart = microtime(true);
        $this->$method($data);
        $tEnd   = microtime(true) - $tStart;
        $time   = round($tEnd * 1000, 4);

        if (config()->debug) {
            echo sprintf("[%s] %s %s\n",
                date('Y-m-d H:i:s'),
                $this->data['token'],
                $method
            );
        }

        return $time;
    }
}
