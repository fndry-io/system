<?php

namespace Foundry\System\Models;

use Foundry\Core\Models\Setting as Base;

/**
 * Class Setting
 *
 * @property $domain
 * @property $name
 * @property $type
 * @property $default
 * @property $value
 *
 */
class Setting extends Base{

    /**
     * @inheritdoc
     *
     * @return array
     */
    static function settings() : array
    {
        return [
	        'system.admin_emails' => array(
                'label'=> __('Admin E-mails'),
		        'description' => __('Comma separated admin emails'),
		        'default' => 'info@domain.com',
		        'type' => 'string',
	        )
        ];
    }
}