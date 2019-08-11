<?php

namespace Foundry\System\Inputs\Types;

use Foundry\Core\Entities\Entity;
use Foundry\Core\Inputs\Contracts\Field;
use Foundry\Core\Inputs\Types\Contracts\Inputable;
use Foundry\Core\Inputs\Types\FileInputType;

class File extends FileInputType implements Field {

	/**
	 * @param Entity|null $entity
	 *
	 * @return Inputable|File
	 */
	static function input( Entity &$entity = null ): Inputable {
		return (new static(
			'file',
			__('File'),
			true
		))
			->setPlaceholder(__('Click to browse for a file'))
			->addRule('file')
			->setAction(resourceUri('foundry.system.files.upload'))
			->setDeleteUrl(resourceUri('foundry.system.files.delete'))
			;
	}

}