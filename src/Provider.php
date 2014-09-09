<?php

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
                    } else {
                        $payloads[$key][$att] = $this->faker->country;
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

        $this->$method($data);

        if (config()->debug) {
            echo sprintf("[%s] %s %s\n",
                date('Y-m-d H:i:s'),
                $this->data['id'],
                $method
            );
        }
    }
}
