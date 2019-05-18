<?php

namespace Foundry\Core\Models\Traits;

trait FullTextSearch
{
	/**
	 * Replaces spaces with full text search wildcards
	 *
	 * @param string $term
	 * @return string
	 */
	protected function fullTextWildcards($term)
	{
		// removing symbols used by MySQL
		$reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
		$term = str_replace($reservedSymbols, '', $term);

		$words = explode(' ', $term);

		foreach($words as $key => $word) {
			/*
			 * applying + operator (required word) only big words
			 * because smaller ones are not indexed by mysql
			 */
			if(strlen($word) >= 3) {
				$words[$key] = '+' . $word . '*';
			}
		}

		$searchTerm = implode( ' ', $words);

		return $searchTerm;
	}

	/**
	 * Scope a query that matches a full text search of term.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param string $term
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeSearch($query, $term)
	{
		$columns = implode(',', array_map(function($value) {
			return $this->table . '.' . $value;
		}, $this->searchable));

		$as = $this->table . '_relevance_score';

		$query->selectRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE) AS {$as}", [$this->fullTextWildcards($term)])
		             ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [$this->fullTextWildcards($term)])
		             ->orderByDesc("{$as}");

		return $query;
	}
}