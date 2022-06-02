<?php

namespace todaydesign\craftstaticdusk\managers;

use todaydesign\craftstaticdusk\CraftStaticDusk;
use craft\helpers\FileHelper;
use Craft;

class StaticBuildManager {

    /**
     * Get scheduled static builds for this environment
     *
     * @return mixed
     */
    public function getScheduledBuilds($site)
    {
        $settings = CraftStaticDusk::$plugin->getSettings();

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
            CURLOPT_URL => Craft::parseEnv($settings->webHookUrl) . '/scheduled',
//             CURLOPT_URL => "http://host.docker.internal:3000/static-build/scheduled",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => json_encode((object)$payload),
            CURLOPT_VERBOSE => TRUE,
        ));

        $response = curl_exec($curl);

        if ($response === false) {
            return [];
        }

        $response = json_decode($response);

        curl_close($curl);

        if (property_exists($response, "responseObject")) {

            // Convert unix milliseconds to seconds
            $data = array_map(function ($build) {
                $build->LaunchTime = $build->LaunchTime / 1000;
                return $build;
            }, $response->responseObject);

            return $response->responseObject;
        }

        return [];
    }


    /**
     * Get scheduled static builds for this environment
     *
     * @return mixed
     */
    public function getBuildHistory($site)
    {
        $settings = CraftStaticDusk::$plugin->getSettings();
        $ref = str_replace("refs/heads/", "", Craft::parseEnv($settings->gitRef));

        $payload = [
            'secret' => Craft::parseEnv($settings->webHookSecret),
            'repo' => Craft::parseEnv($settings->gitRepo),
            'ref' => $ref,
            'site' => $site
        ];

        $response = null;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => Craft::parseEnv($settings->webHookUrl) . '/history',
//             CURLOPT_URL => "http://host.docker.internal:3000/static-build/history",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => json_encode((object)$payload),
            CURLOPT_VERBOSE => TRUE,
        ));

        $response = curl_exec($curl);

        if ($response === false) {
            return [];
        }

        $response = json_decode($response);

        curl_close($curl);

        if (property_exists($response, "responseObject")) {
            return $response->responseObject;
        }

        return [];
    }


}