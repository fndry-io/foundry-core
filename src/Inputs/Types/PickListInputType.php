<?php

namespace Foundry\Core\Inputs\Types;


class PickListInputType extends ChoiceInputType {

    static $cast = 'int';

    /**
     * The input options
     *
     * @param string $identifier The identifier to get the list items
     *
     * @param string $key
     * @param string $label
     * @param string $cast
     *
     * @return array
     */
    static function list( $identifier , $key = 'id', $label='label', $cast = 'int'): array {

        self::$cast = $cast;

        return config('foundry.pick-list-items.model')::query()
            ->select( [ 'pick_list_items.*' ] )
            ->join( 'pick_lists', 'pick_list_items.pick_list_id', '=', 'pick_lists.id' )
            ->where( 'pick_list_items.status', 1 )
            ->where( 'pick_lists.identifier', $identifier )
            ->orderBy( 'pick_list_items.label', 'ASC' )
            ->get()
            ->pluck( $label, $key )
            ->toArray();
    }

    static function cast($item)
    {
        if ($item->isMultiple()) {
            return 'array_int';
        } else {
            return self::$cast;
        }
    }

}
