<?php

namespace Foundry\System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AuthUser
 *
 * @package Foundry\System\Http\Resources
 */
class AuthUser extends JsonResource {

	public function toArray( $request ) {
		return [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'username' => $this->username,
			'display_name' => $this->display_name,
			'job_title' => $this->job_title,
			'job_department' => $this->job_department,
			'email' => $this->email,
			'active' => $this->active,
			'super_admin' => $this->super_admin,
			'timezone' => $this->timezone,
			'last_login_at' => $this->last_login_at,
			'logged_in' => $this->logged_in,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'deleted_at' => $this->deleted_at,
			'settings' => $this->settings,
			//todo add permissions scopes
		];
	}
}