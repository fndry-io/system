<?php

namespace Foundry\System\Http\Requests\Auth;

use Foundry\System\Http\Requests\Files\DeleteFileRequest;

class DeleteProfileImageRequest extends DeleteFileRequest
{

    public static function name(): String
    {
        return 'foundry.system.auth.profile.delete';
    }

}
