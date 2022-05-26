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
use craft\helpers\FileHelper;

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

        $settings = CraftStaticDusk::$plugin->getSettings();
        $site = Craft::$app->request->post("siteHandle");

        $curl = curl_init();

        $payload = [
            'secret' => Craft::parseEnv($settings->webHookSecret)
        ];

        if (Craft::parseEnv($settings->webHookType) === 'GH') {
            $payload = array_merge($payload, [
                'repo' => Craft::parseEnv($settings->gitRepo),
                'ref' => Craft::parseEnv($settings->gitRef),
                'envName' => Craft::parseEnv($settings->environmentName),
                'site' => $site
            ]);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => Craft::parseEnv($settings->webHookUrl),
//             CURLOPT_URL => "http://host.docker.internal:3000/static-build",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_VERBOSE => true,
            CURLOPT_POSTFIELDS => json_encode((object)$payload),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        Craft::$app->getSession()->setNotice('Static build initiated.');
    }

    /**
     * Handle a request going to our plugin's build action URL,
     * e.g.: actions/craft-static-dusk/generate/schedule
     *
     * @return mixed
     */
    public function actionSchedule()
    {

        $settings = CraftStaticDusk::$plugin->getSettings();
        $site = Craft::$app->request->post("siteHandle");

        $scheduleDate = Craft::$app->request->post("scheduleDate");
        $scheduleTime = Craft::$app->request->post("scheduleTime");

        // Format time in Australian format
        $formattedTime = str_replace("/", "-", $scheduleDate . " " . $scheduleTime);
        $launchTime = strtotime($formattedTime) * 1000; // Multiple by 1000 to convert to milliseconds


        $curl = curl_init();

        $payload = [
            'secret' => Craft::parseEnv($settings->webHookSecret),
            'repo' => Craft::parseEnv($settings->gitRepo),
            'ref' => Craft::parseEnv($settings->gitRef),
            'envName' => Craft::parseEnv($settings->environmentName),
            'site' => strval($site),
            'launchTime' => $launchTime
        ];


        curl_setopt_array($curl, array(
           CURLOPT_URL => Craft::parseEnv($settings->webHookUrl) . "/schedule",
//             CURLOPT_URL => "http://host.docker.internal:3000/static-build/schedule",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_VERBOSE => true,
            CURLOPT_POSTFIELDS => json_encode((object)$payload),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        $response = json_decode($response);

        curl_close($curl);

        Craft::$app->getSession()->setNotice('Static build scheduled');
    }


    /**
     *
     * Remove scheduled build
     *
     * @return mixed
     */
    public function actionDelete()
    {

        $settings = CraftStaticDusk::$plugin->getSettings();

        $id = Craft::$app->request->post("id");
        $launchTime = Craft::$app->request->post("launchTime");


        $curl = curl_init();

        $payload = [
            'secret' => Craft::parseEnv($settings->webHookSecret),
            'id' => $id,
            'launchTime' => intval($launchTime)
        ];


        curl_setopt_array($curl, array(
           CURLOPT_URL => Craft::parseEnv($settings->webHookUrl) . "/scheduled",
//             CURLOPT_URL => "http://host.docker.internal:3000/static-build/scheduled",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_VERBOSE => true,
            CURLOPT_POSTFIELDS => json_encode((object)$payload),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);
        curl_close($curl);

        Craft::$app->getSession()->setNotice('Static build removed');
    }


}
