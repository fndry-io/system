<?php

namespace Foundry\System\Inputs\PickListItem\Types;

use Foundry\Core\Inputs\Contracts\Field;

use Foundry\Core\Inputs\Types\TextInputType;
use Foundry\Core\Inputs\Types\Contracts\Inputable;

class Label extends TextInputType implements Field {

	/**
	 *
	 *
	 * @return Inputable|Name
	 */
	static function input( ): Inputable {
		return ( new static(
			'label',
			__( 'Label' ),
			true
		) )
			->setMax( 50 )
			->setSortable( true );
	}

}