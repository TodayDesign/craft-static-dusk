<?php

namespace todaydesign\craftstaticdusk\jobs;

use Craft;
use craft\queue\BaseJob;

class RefreshScoutIndexesJob extends BaseJob
{
    public function execute($queue): void
    {
        \craft\helpers\App::runConsoleCommand('scout/index/refresh', []);
        return;
    }

    public function getDescription(): string
    {
        return 'Refresh Scout Indexes';
    }
}