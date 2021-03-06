<?php

namespace Foundry\System\Inputs\User\Types;

use Foundry\Core\Inputs\Contracts\Field;

use Illuminate\Database\Eloquent\Model;
use Foundry\Core\Inputs\Types\TextInputType;
use Foundry\Core\Inputs\Types\InputType;
use Foundry\Core\Inputs\Types\Contracts\Inputable;

class FullName extends TextInputType implements Field {

	/**
	 *
	 *
	 * @return Inputable|FullName
	 */
	static function input( ): Inputable {
		return ( new static(
			'full_name',
			__( 'Full Name' ),
			false
		) )
			->setSortable( false );
	}

}