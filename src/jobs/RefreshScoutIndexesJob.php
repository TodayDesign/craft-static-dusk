<?php

namespace todaydesign\craftstaticdusk\jobs;

use Craft;
use craft\queue\BaseJob;
use rias\scout\ScoutService;
use rias\scout\engines\Engine;
use rias\scout\Scout;

class RefreshScoutIndexesJob extends BaseJob
{
    public function execute($queue): void
    {
        $indexController = new \rias\scout\console\controllers\scout\IndexController('index', Scout::$plugin);
        $indexController->actionRefresh();
        return;
    }

    public function getDescription(): string
    {
        return 'Refresh Scout Indexes';
    }
}