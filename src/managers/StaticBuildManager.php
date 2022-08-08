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
     * Get build history for this environment
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


    public function launchIncrementalBuild($siteHandle, $pageUri)
    {

        $isMissingEnvVariables = $this->isMissingEnvVariables();

        if ($isMissingEnvVariables) {
            return;
        }

        $settings = CraftStaticDusk::$plugin->getSettings();

        $curl = curl_init();

        $payload = [
            'secret' => Craft::parseEnv($settings->webHookSecret)
        ];

        if (Craft::parseEnv($settings->webHookType) === 'GH') {
            $payload = array_merge($payload, [
                'repo' => Craft::parseEnv($settings->gitRepo),
                'ref' => Craft::parseEnv($settings->gitRef),
                'envName' => Craft::parseEnv($settings->environmentName),
                'site' => $siteHandle,
                'incrementalBuildUri' => $pageUri,
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