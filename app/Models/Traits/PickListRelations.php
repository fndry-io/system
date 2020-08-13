<?php

namespace Foundry\System\Models\Traits;

use Foundry\System\Models\PickListItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait HasPickListRelations
 *
 * @package Foundry\System\Models\Traits
 */
trait PickListRelations
{
    /**
     * @return MorphToMany
     */
    public function morphToManyPickListItems()
    {
        return $this->morphToMany(PickListItem::class, 'relatable', 'picklist_item_relations', 'relatable_id', 'picklist_item_id');
    }

    /**
     * @param string $related
     * @return BelongsTo
     */
    public function belongsToPickListItem($related = PickListItem::class, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        return $this->belongsTo($related, $foreignKey, $ownerKey, $relation);
    }
}
