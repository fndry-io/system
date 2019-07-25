<?php

namespace Foundry\System\Http\Requests\Users;

use Foundry\Core\Requests\Response;
use Foundry\Core\Requests\Traits\HasEntity;
use Foundry\System\Entities\User;
use Foundry\System\Services\UserService;
use LaravelDoctrine\ORM\Facades\EntityManager;

class RestoreUserRequest extends UserRequest
{
	public static function name(): String {
		return 'foundry.system.users.restore';
	}

	/**
	 * @param mixed $id
	 *
	 * @return null|User|object
	 */
	public function findEntity($id)
	{
		return parent::findEntity($id);
	}

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return !!($this->user());
	}

	/**
	 * Handle the request
	 *
	 * @return Response
	 */
	public function handle() : Response
	{
		return UserService::service()->restore($this->getEntity());
	}

}