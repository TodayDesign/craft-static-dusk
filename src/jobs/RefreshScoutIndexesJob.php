<?php

namespace todaydesign\craftstaticdusk\jobs;

use Craft;
use craft\queue\BaseJob;

class RefreshScoutIndexesJob extends BaseJob
{
    public function execute($queue): void
    {
        Craft::$app->runAction('scout/index/refresh');
        return;
    }
}