<?php

namespace Foundry\System\Inputs\Types;

use Foundry\Core\Inputs\Contracts\Field;

use Foundry\Core\Inputs\Types\TelInputType;
use Foundry\Core\Inputs\Types\Contracts\Inputable;

class Phone extends TelInputType implements Field {

	/**
	 *
	 *
	 * @return Inputable|Phone
	 */
	static function input( ): Inputable {
		return ( new static(
			'phone',
			__( 'Phone' ),
			false
		) )
			->setMax( 16 )
			->setPlaceholder('+1 ...')
			->setHelp(__('An international contact number starting with a dialing code. E.G. +1 555 555 5555'))
			;
	}

}