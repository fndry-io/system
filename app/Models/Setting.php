<?php

namespace Foundry\System\Models;

use Foundry\Core\Models\Setting as BaseSetting;

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
class Setting extends BaseSetting
{

    protected $table = 'system_settings';

    /**
     * @inheritdoc
     *
     * @return array
     */
    static function settings() : array
    {
        return [
	        'system.admin_emails' => array(
                'label'=> __('Admin E-mail'),
		        'description' => __('Email address for admin notifications'),
		        'default' => 'info@domain.com',
		        'type' => 'string',
                'rules' => 'nullable|email'
	        )
        ];
    }
}
