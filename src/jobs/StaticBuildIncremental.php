<?php
/**
 * Craft Static Dusk plugin for Craft CMS 3.x
 *
 * Static site builder for Craft and AWS Code Pipelines
 *
 * @link      https://today.design
 * @copyright Copyright (c) 2020 Jason D'Souza
 */

namespace todaydesign\craftstaticdusk\jobs;

use todaydesign\craftstaticdusk\CraftStaticDusk;
use todaydesign\craftstaticdusk\managers\StaticBuildManager;

use Craft;
use craft\queue\BaseJob;

/**
 * StaticBuild job
 *
 * Jobs are run in separate process via a Queue of pending jobs. This allows
 * you to spin lengthy processing off into a separate PHP process that does not
 * block the main process.
 *
 * You can use it like this:
 *
 * use todaydesign\craftstaticdusk\jobs\StaticBuildIncremental as StaticBuildIncrementalJob;
 *
 * $queue = Craft::$app->getQueue();
 * $jobId = $queue->push(new StaticBuildIncrementalJob([
 *     'description' => Craft::t('craft-static-dusk', 'This overrides the default description'),
 *     'someAttribute' => 'someValue',
 * ]));
 *
 * The key/value pairs that you pass in to the job will set the public properties
 * for that object. Thus whatever you set 'someAttribute' to will cause the
 * public property $someAttribute to be set in the job.
 *
 * Passing in 'description' is optional, and only if you want to override the default
 * description.
 *
 * More info: https://github.com/yiisoft/yii2-queue
 *
 * @author    Jason D'Souza
 * @package   CraftStaticDusk
 * @since     1.0.1
 */
class StaticBuildIncremental extends BaseJob
{
    // Public Properties
    // =========================================================================

    /**
     * Site handle to build
     *
     * @var string
     */
    public $siteHandle = '';

    /**
     * Page URI to build
     *
     * @var string
     */
    public $pageUri = '';

    // Public Methods
    // =========================================================================

    /**
     * When the Queue is ready to run your job, it will call this method.
     * You don't need any steps or any other special logic handling, just do the
     * jobs that needs to be done here.
     *
     * More info: https://github.com/yiisoft/yii2-queue
     */
    public function execute($queue)
    {
        $manager = new StaticBuildManager();
        $manager->launchIncrementalBuild($this->siteHandle, $this->pageUri);
    }

    // Protected Methods
    // =========================================================================

    /**
     * Returns a default description for [[getDescription()]], if [[description]] isn’t set.
     *
     * @return string The default task description
     */
    protected function defaultDescription(): string
    {
        return Craft::t('craft-static-dusk', 'Incremental Static build');
    }
}
