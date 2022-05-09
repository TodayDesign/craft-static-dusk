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
    public function getScheduledStaticBuilds()
    {
        $settings = CraftStaticDusk::$plugin->getSettings();
        $site = Craft::$app->request->get("site");

        $payload = [
            'secret' => Craft::parseEnv($settings->webHookSecret),
            'repo' => Craft::parseEnv($settings->gitRepo),
            'ref' => Craft::parseEnv($settings->gitRef),
            'envName' => Craft::parseEnv($settings->environmentName),
            'site' => $site
        ];

        $response = null;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => Craft::parseEnv($settings->webHookUrl),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => json_encode((object)$payload),
            CURLOPT_VERBOSE => TRUE,
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($curl);

        if ($response === false) {
            // Uncomment the following lines if this request is failing

            // $file = Craft::getAlias('@storage/logs/pluginhandle.log');
            // $log = date('Y-m-d H:i:s') . ' ' . json_encode(curl_error($curl), SON_PRETTY_PRINT) . "\n";
            // FileHelper::writeToFile($file, $log, ['append' => true]);
            return [];
        }

        $response = json_decode($response);

        curl_close($curl);

        if (array_key_exists("responseObject", $response)) {
            return $response->responseObject;
        }

        return [];
    }
}
