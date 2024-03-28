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
 * BuildSettings Model
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
class BuildSettings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some model attribute
     *
     * @var string
     */
    public $someAttribute = 'Some Build Default';

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
    public function rules(): array
    {
        return [
            ['someAttribute', 'string'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}
