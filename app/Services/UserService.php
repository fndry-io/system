<?php

namespace Foundry\System\Services;

use Carbon\Carbon;
use Foundry\Core\Auth\TokenGuard;
use Foundry\Core\Entities\Contracts\HasApiToken;
use Foundry\Core\Inputs\Inputs;
use Foundry\Core\Requests\Response;
use Foundry\Core\Services\BaseService;
use Foundry\Core\Services\Traits\HasRepository;
use Foundry\System\Entities\User;
use Foundry\System\Inputs\User\ForgotPasswordInput;
use Foundry\System\Inputs\User\ResetPasswordInput;
use Foundry\System\Inputs\User\UserEditInput;
use Foundry\System\Inputs\User\UserInput;
use Foundry\System\Inputs\User\UserLoginInput;
use Foundry\System\Inputs\User\UserRegisterInput;
use Foundry\System\Repositories\UserRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class UserService extends BaseService {

	use HasRepository;

	public function __construct(UserRepository $repository) {
		$this->setRepository($repository);
	}

	/**
	 * @param UserLoginInput $input
	 *
	 * @return Response
	 */
	public function login(UserLoginInput $input) : Response
	{
		$inputs = $input->inputs();

		/**
		 * @var Guard|StatefulGuard $guard
		 */
		$guard = Auth::guard($input->guard);

		//if current logged in user, log them out first
		if ($guard->check() && $guard instanceof StatefulGuard) {
			$guard->logout();
		}

		/**
		 * @var UserProvider $provider
		 */
		$provider = $guard->getProvider();

		/**
		 * @var $user User
		 */
		if($user = $provider->retrieveByCredentials([
			'email' => $inputs['email']
		])){
			if ($provider->validateCredentials($user, $inputs)) {
				//detect if the user is longer active
				if ($user->isActive()) {
					$guard->setUser($user);
					return $this->returnGuardUser($guard);
				} else {
					return Response::error(__("Account no longer active, please contact the Support Team"), 403);
				}
			}
		}
		return Response::error(__("Permission denied, wrong password and username combination"), 401);
	}

	/**
	 * Log the user out of the given guard
	 *
	 * If no guard is supplied, it will use the system default
	 *
	 * @param null $guard
	 *
	 * @return Response
	 */
	public function logout($guard = null)
	{
		$guard = Auth::guard($guard);
		if ($guard->check()) {

			//if the guard is an api guard, remove the token
			if ($guard instanceof TokenGuard) {
				/**
				 * @var User $user
				 */
				$user = $guard->user();
				$guard->clearToken($user);
				$this->repository->save($user);
			}

			//if current logged in user, log them out first
			if ($guard instanceof StatefulGuard) {
				Auth::logout();
				Session::invalidate();
			}

		}
		return Response::success();
	}

	/**
	 * @param Guard $guard The guard which contains the user to return
	 * @param Boolean $setToken Generate or Refresh the token assigned to the user
	 *
	 * @return Response
	 */
	public function returnGuardUser(Guard $guard = null, $setToken = true) : Response
	{
		/**
		 * @var $user User
		 */
		$user = $guard->user();
		$user->logged_in = true;
		$user->last_login_at = new Carbon();

		$data = [
			'user' => $user->only(['id', 'uuid', 'username', 'display_name', 'email'])
		];

		if ($guard instanceof TokenGuard && $user instanceof HasApiToken) {

			if ($setToken) {
				$token = $guard->setToken($user);
			} else {
				$token = $guard->getToken($user);
			}
			$data['token'] = $token;
		}

		$this->repository->save($user);

		return Response::success($data);
	}

	/**
	 * @param UserRegisterInput|Inputs $input
	 *
	 * @return Response
	 */
	public function register(UserRegisterInput $input) : Response
	{
		$user = new User($input->inputs());
		$user->setActive(true);
		$user->setPassword($input->password);
		if ($input->super_admin === true) {
			$user->setSuperAdmin(true);
		}
		$this->repository->save($user);
		return Response::success($user);
	}

	/**
	 * Request link to reset password
	 *
	 * @param ForgotPasswordInput $input
	 * @return Response
	 */
	public function forgotPassword(ForgotPasswordInput $input)
	{
		$response = $this->broker()->sendResetLink(
			$input->toArray()
		);

		if ($response == Password::RESET_LINK_SENT){
			return Response::success([], __('Please check your email for reset instructions.'));
		}
		else{
			$user = $this->repository->findOneBy(['email' => $input->email]);
			return $user? Response::error(__("Requested resource was not found"), 400)
				: Response::error(__("Account with provided E-mail address not found!"), 404);
		}
	}

	/**
	 * Reset Password
	 *
	 * @param ResetPasswordInput $input
	 * @return Response
	 */
	public function resetPassword(ResetPasswordInput $input)
	{
		$response = $this->broker()->reset( $input->toArray(), function ($user, $password) {
			/**
			 * @var $user User
			 */
			$user->setPassword($password);
			$user->setRememberToken(Str::random(60));
			$this->repository->save($user);
			event(new PasswordReset($user));
		} );

		if($response === Password::PASSWORD_RESET){
			return Response::success();
		} else {
			return Response::error(__("Account with provided E-mail address not found!"), 404);
		}
	}


	/**
	 * @param UserInput|Inputs $input
	 *
	 * @return Response
	 */
	public function add(UserInput $input) : Response
	{
		$user = new User();
		$user->fill($input);
		$user->setPassword($input->password);
		if ($input->password) {
			$user->setPassword($input->password);
		}
		if (auth_user()->isSuperAdmin()) {

			//todo change to control this in the form
			$user->setActive(true);

			if ($input->super_admin === true) {
				$user->setSuperAdmin(true);
			} elseif ($input->super_admin) {
				$user->setSuperAdmin(false);
			}
		}
		$this->repository->save($user);
		return Response::success($user);
	}

	/**
	 * @param UserEditInput|Inputs $input
	 * @param User $user
	 *
	 * @return Response
	 */
	public function edit(UserEditInput $input, User $user) : Response
	{
		$user->fill($input);
		if ($input->password) {
			$user->setPassword($input->password);
		}
		if (auth_user()->isSuperAdmin()) {

			//todo change to control this in the form
			$user->setActive(true);

			if ($input->super_admin === true) {
				$user->setSuperAdmin(true);
			} elseif ($input->super_admin) {
				$user->setSuperAdmin(false);
			}
		}

		if (!$user->isSuperAdmin() && $input->offsetExists('active')) {
			$user->setActive((bool) $input->active);
		}

		$this->repository->save($user);
		return Response::success($user);
	}

	/**
	 * Delete a user
	 *
	 * @param User $user
	 *
	 * @return Response
	 */
	public function delete(User $user) : Response
	{
		if ($user->isSuperAdmin()) {
			return Response::error(__('You cannot delete a Super User'), 408);
		}
		$this->repository->delete($user);
		return Response::success();
	}

	/**
	 * Get the broker to be used during password reset.
	 *
	 * @return \Illuminate\Contracts\Auth\PasswordBroker
	 */
	public function broker()
	{
		return Password::broker();
	}



}