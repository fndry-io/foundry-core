<?php

namespace Foundry\Core\Seeders\Concerns;

use Foundry\Core\Models\PickList;
use Foundry\Core\Models\PickListItem;

trait PickListSeed {

	public function seedPickList( $list, $items, $force = false ) {
		if ( $force && $_list = PickList::where( 'identifier', $list['identifier'] )->first() ) {
			/**
			 * @var $list PickList
			 */
			$_list->items()->forceDelete();
			$_list->forceDelete();
		}

		if ( ! $_list = PickList::where( 'identifier', $list['identifier'] )->first() ) {
			$_list             = new PickList();
			$_list->identifier = $list['identifier'];
			$_list->label      = $list['label'];
			$_list->save();
		}

		foreach ( $items as $item ) {
			if ( ! $_item = PickListItem::where( 'identifier', $item['identifier'] )->where( 'pick_list_id', $_list->id )->first() ) {
				$_item               = new PickListItem();
				$_item->identifier   = $item['identifier'];
				$_item->label        = $item['label'];
				$_item->status       = $item['status'];
				$_item->default      = $item['default'];
				$_item->pick_list_id = $_list->id;
				$_item->save();
			}
		}
	}

}