<?php
/**
 * Craft Static Dusk plugin for Craft CMS 3.x
 *
 * Static site builder for Craft and AWS Code Pipelines
 *
 * @link      https://today.design
 * @copyright Copyright (c) 2020 Jason D'Souza
 */

namespace todaydesign\craftstaticdusk\controllers;

use todaydesign\craftstaticdusk\CraftStaticDusk;

use Craft;
use craft\web\Controller;

/**
 * Generate Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Jason D'Souza
 * @package   CraftStaticDusk
 * @since     1.0.1
 */
class GenerateController extends Controller
{

    

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's build action URL,
     * e.g.: actions/craft-static-dusk/generate/build
     *
     * @return mixed
     */
    public function actionBuild()
    {
        // $result = 'Welcome to the DefaultController actionIndex() method';

        $settings = CraftStaticDusk::$plugin->getSettings();

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => Craft::parseEnv($settings->webHookUrl),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_VERBOSE => true,
        CURLOPT_POSTFIELDS =>"{\"secret\": \"".Craft::parseEnv($settings->webHookSecret)."\"}",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;

        Craft::$app->getSession()->setNotice('Static build initiated.');

        // return $result;
    }

}
