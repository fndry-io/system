<?php

namespace Foundry\System\Inputs\Role\Types;

use Foundry\Core\Inputs\Contracts\Field;

use Foundry\Core\Inputs\Types\TextInputType;
use Foundry\Core\Inputs\Types\Contracts\Inputable;

class Name extends TextInputType implements Field {

	/**
	 *
	 *
	 * @return Inputable|Name
	 */
	static function input( ): Inputable {
		return ( new static(
			'name',
			__( 'Name' ),
			true
		) )
			->setMax( 100 )
			->setSortable( true );
	}

}