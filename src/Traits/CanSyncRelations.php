<?php namespace Staskjs\LaravelUtils\Traits;

/*
 * Attempt to replicate Rails accepts_nested_attributes_for
 *
 * Example:
 * User has many products, can edit all products at once
 *
 * User has products 1 and 3
 *
 * $products = [
 *   ['id' => 1, ...],
 *   [...],
 * ];
 *
 * // Will update product 1, remove product 3 and add new product
 * $user->syncRelation('products', $products, $fieldsToUpdate);
 *
 */
trait CanSyncRelations {

    public function syncRelation($relationName, $items, $fields = [], \Closure $onCreate = null) {
        $newItems = collect($items)->filter(function($item) {
            return empty($item['id']);
        });

        $existingItems = collect($items)->filter(function($item) {
            return !empty($item['id']);
        });

        $deletedItems = $this->{$relationName}->pluck('id')->diff($existingItems->pluck('id'));

        $existingItems->each(function($item) use ($relationName, $fields) {
            $existingItem = $this->{$relationName}()->whereId($item['id'])->first();
            if ($existingItem) {
                if (!empty($item['_destroy'])) {
                    $existingItem->delete();
                    return;
                }
                foreach ($fields as $field) {
                    if(isset($item[$field])) {
                        $existingItem->attributes[$field] = $item[$field];
                    }
                }
                $existingItem->save();
            }
        });

        $newItems->each(function($item) use ($relationName, $fields, $onCreate) {
            if (!empty($item['_destroy'])) {
                return;
            }
            $newItem = $this->{$relationName}()->getRelated();
            $newItem = new $newItem;
            foreach ($fields as $field) {
                if(isset($item[$field])) {
                    $newItem->attributes[$field] = $item[$field];
                }
            }

            if(is_callable($onCreate)) {
                $newItem = $onCreate($newItem);
            }
            
            $this->{$relationName}()->save($newItem);
        });

        $this->{$relationName}()->whereIn('id', $deletedItems)->delete();
    }

}
