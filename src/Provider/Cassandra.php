<?php

namespace Provider;

use Provider;
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

class Cassandra extends Dump
{
    public function runSelect1($data)
    {
        $kpisUpdater = new KPIsUpdater();

        // 2. Create settings loader and apply it to the updater
        $settingsLoader = new SettingsLoader($data['space_id']);
        $kpisUpdater->setSettingsLoader($settingsLoader);

        // 3. create the data loader and add custom loaders in its loaders chain
        $dataLoader = new DataLoader($data['space_id']);
        $warehouseLoader = new MetricDBLoader($data['space_id']);

        // Tukaj boš uporabil loader za Cassandro
        $cassandraLoader = new \Z\Loader\Cassandra\MetricLoader($data['space_id']);
        $dataLoader->addLoaderToChain($cassandraLoader);

        $kpisUpdater->setDataLoader($dataLoader);

        // 5. Create DAO for saving the KPIs and apply it to the updater
        $factory = DAOFactory::getInstance('Doctrine');
        $kpiDAO  = $factory->createKpi();
        $kpisUpdater->setKpiDao($kpiDAO);

        // 6. perform the update
        $kpisUpdater->updateKPIsForKeys($data['board_kpi']);
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
