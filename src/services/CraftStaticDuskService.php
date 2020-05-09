<?php
/**
 * Craft Static Dusk plugin for Craft CMS 3.x
 *
 * Static site builder for Craft and AWS Code Pipelines
 *
 * @link      https://today.design
 * @copyright Copyright (c) 2020 Jason D'Souza
 */

namespace todaydesign\craftstaticdusk\services;

use todaydesign\craftstaticdusk\CraftStaticDusk;

use Craft;
use craft\base\Component;

/**
 * CraftStaticDuskService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Jason D'Souza
 * @package   CraftStaticDusk
 * @since     1.0.1
 */
class CraftStaticDuskService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     CraftStaticDusk::$plugin->craftStaticDuskService->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (CraftStaticDusk::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
