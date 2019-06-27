<?php

namespace Foundry\System\Inputs\User;

use Foundry\Core\Inputs\Inputs;
use Foundry\Core\Inputs\Types\TextInputType;
use Foundry\Core\Support\InputTypeCollection;

/**
 * Class UsersFilterInput
 *
 * @package Foundry\System\Inputs
 *
 * @property $search
 */
class UsersFilterInput extends Inputs {

	protected $fillable = [
		'search'
	];

	public function types() : InputTypeCollection
	{
		return InputTypeCollection::fromTypes([
			(new TextInputType('search', 'Search', false))
		]);
	}

}