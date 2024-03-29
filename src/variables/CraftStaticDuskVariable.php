<?php
/**
 * Craft Static Dusk plugin for Craft CMS 3.x
 *
 * Static site builder for Craft and AWS Code Pipelines
 *
 * @link      https://today.design
 * @copyright Copyright (c) 2020 Jason D'Souza
 */

namespace todaydesign\craftstaticdusk\variables;

use craft\helpers\FileHelper;
use todaydesign\craftstaticdusk\CraftStaticDusk;

use Craft;
use todaydesign\craftstaticdusk\managers\StaticBuildManager;

/**
 * Craft Static Dusk Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.craftStaticDusk }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Jason D'Souza
 * @package   CraftStaticDusk
 * @since     1.0.1
 */
class CraftStaticDuskVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Get scheduled static builds for this environment
     *
     * @return mixed
     */
    public function getScheduledStaticBuilds($site)
    {
        $manager = new StaticBuildManager();
        $results = $manager->getScheduledBuilds($site);
        return $results;
    }

    /**
     * Get build history for this environment
     *
     * @return mixed
     */
    public function getBuildHistory($site)
    {
        $manager = new StaticBuildManager();
        $results = $manager->getBuildHistory($site);
        return $results;
    }

    /**
     * Check if Environment variables are missing
     *
     * @return boolean
     */
    public function isMissingEnvVariables()
    {
        $settings = CraftStaticDusk::$plugin->getSettings();

        $webHookSecret = Craft::parseEnv($settings->webHookSecret);
        $gitRepo = Craft::parseEnv($settings->gitRepo);
        $gitRef = Craft::parseEnv($settings->gitRef);
        $environmentName = Craft::parseEnv($settings->environmentName);
        $webHookUrl = Craft::parseEnv($settings->webHookUrl);

        return (
            empty($webHookSecret) || $webHookSecret === '$STATIC_BUILD_WEBHOOK_SECRET' ||
            empty($gitRepo) || $gitRepo === '$STATIC_BUILD_GIT_REPO' ||
            empty($gitRef) ||  $gitRef === '$STATIC_BUILD_GIT_REF' ||
            empty($environmentName) || $environmentName === '$STATIC_BUILD_WEBHOOK_URL' ||
            empty($webHookUrl) || $webHookUrl === '$STATIC_BUILD_WEBHOOK_URL'
        );
    }

}
