<?php

namespace Foundry\System\Inputs\Role;

use Foundry\Core\Inputs\Types\FormType;
use Foundry\Core\Inputs\Types\SubmitButtonType;

/**
 * Class RoleInput
 *
 * @package Foundry\System\Inputs
 *
 * @property $name
 */
class EditRoleInput extends RoleInput
{
    public function view($request) : FormType
    {
        $form = parent::view($request);

        $form->setTitle(__('Edit Role'));
        $form->setButtons((new SubmitButtonType(__('Edit'), $form->getAction())));

        return $form;
    }

}
