<?php
/**
 * Craft Static Dusk plugin for Craft CMS 3.x
 *
 * Static site builder for Craft and AWS Code Pipelines
 *
 * @link      https://today.design
 * @copyright Copyright (c) 2020 Jason D'Souza
 */

namespace todaydesign\craftstaticdusk\models;

use todaydesign\craftstaticdusk\CraftStaticDusk;

use Craft;
use craft\base\Model;

/**
 * CraftStaticDusk Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Jason D'Souza
 * @package   CraftStaticDusk
 * @since     1.0.1
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $someAttribute = 'Some Default Setting';
    public $webHookUrl = '';
    public $webHookSecret = '';


    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['someAttribute', 'string'],
            ['webHookUrl', 'string'],
            ['webHookSecret', 'string'],
            ['webHookUrl', 'default', 'value' => '$STATIC_BUILD_WEBHOOK_URL'],
            ['webHookSecret', 'default', 'value' => '$STATIC_BUILD_WEBHOOK_SECRET'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}
