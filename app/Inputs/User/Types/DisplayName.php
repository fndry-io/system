<?php

namespace Foundry\System\Inputs\User\Types;

use Foundry\Core\Inputs\Contracts\Field;

use Illuminate\Database\Eloquent\Model;
use Foundry\Core\Inputs\Types\TextInputType;
use Foundry\Core\Inputs\Types\InputType;
use Foundry\Core\Inputs\Types\Contracts\Inputable;

class DisplayName extends TextInputType implements Field {

	/**
	 *
	 *
	 * @return Inputable|DisplayName
	 */
	static function input( ): Inputable {
		return ( new static(
			'display_name',
			__( 'Display Name' ),
			true
		) )
			->setHelp(__('A unique name that is displayed to other users in the system. This will typically be your First and Last name.'))
			->setMax( 100 )
			->setSortable( true );
	}

}