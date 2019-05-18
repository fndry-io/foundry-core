<?php

namespace Foundry\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Relational Trait
 *
 * A trait that modifies the behaviour of Eloquent to use the series of relation definitions on the class
 * to manage the relation calls.
 *
 * This makes it possible to know how models are linked together without having to instantiate models, which is the
 * Laravel approach.
 *
 * The default functionality of the joins is a left join.
 *
 * Examples on how to use this:
 *  - Uses default left join
 *      `ModelName::query()->withSelect(['model_1_alias.column_1', 'model_2_alias.column_1'])`
 *  - Set inner join as default
 *      `ModelName::query()->withSelect(['model_1_alias.column_1', 'model_2_alias.column_1'], 'inner')`
 *  - Set right join as default
 *      `ModelName::query()->withSelect(['model_1_alias.column_1', 'model_2_alias.column_1'], 'right')`
 *  - Set joins on specific relations. Uses left as the default if relation is not set in the join array.
 *      `ModelName::query()->withSelect(['model_1_alias.column_1', 'model_2_alias.column_1', 'model_3_alias.column_1'],
 *          ['model_2_alias' => 'inner', 'model_3_alias' => 'left'])`
 *
 * Your models then need a $relationships array with keys for each relation type and the value an array of the relation
 * name and its parameters (modelled after the parameter names of the normal Eloquent relation calls)
 *
 * Example:
 * ```
 * public $relationships = [
 *      'hasOne' => [
 *          'model_2_alias' => [
 *              'related' => 'Path\Model\Name',
 *              'foreignKey' => 'foreign_id'
 *          ]
 *      ]
 * ];
 * ```
 *
 * The following properties can be set on the Model to customise the behaviour of the Relational
 *
 * @property string $title The title of the Model, defaults to the Model base class name
 * @property string $table_alias Alias to use in the join statements, defaults to the slug of the Model base class name
 * @property array $labels List of custom labels for the model attributes, defaults to a value based off the column name
 * @property string $displayField The column to use when creating a list ([primaryKey = label]) and should be used as the label
 *
 * @todo Add the ability to create the sub Models attached to the root Model
 * @package App\Models\Concerns
 */
trait Relational {

	/**
	 * A list of joined relationships
	 *
	 * @var array
	 */
	protected $joined = [];

	/**
	 * The support relation types
	 *
	 * @var array
	 */
	private $relation_types = [
		'belongsTo',
		'belongsToMany',
		'hasOne',
		'hasMany',
	];

	protected $mapped_relations = [];

	/**
	 * Relational constructor.
	 *
	 * Instantiates and sets the table_alias if not already set
	 *
	 * @param array $attributes
	 */
	public function __construct( array $attributes = [] ) {
		if ( isset( $this->relationships ) ) {
			foreach ( $this->relationships as $type => $relationships ) {
				foreach ( $relationships as $name => $relationship ) {
					if ( is_string( $relationship ) ) {
						$relationship = [
							'related' => $relationship
						];
					}
					$this->relationships[ $type ][ $name ] = array_merge( $relationship, [
						'type' => $type
					] );

					$this->mapped_relations[ $name ] = &$this->relationships[ $type ][ $name ];
				}
			}
		}
		parent::__construct( $attributes );
	}

	/**
	 * Extract the relation from a dot notated column name
	 *
	 * If the $name does not include a dot, the returned column will be set to the $name
	 *
	 * @param string $name The column name with dot notation
	 *
	 * @return array The extracted relation `['column' => string, 'table' => string|null, 'relation' => string|null], 'parts' => null|array`
	 */
	public static function extractRelation( $name ): array {
		$relation = $column = $table = $parts = null;
		if ( strpos( $name, '.' ) !== false ) {
			$parts  = preg_split( '/\./', $name );
			$column = array_pop( $parts );
			$table  = array_pop( $parts );
			if ( ! empty( $parts ) ) {
				$parts[]  = $table;
				$relation = implode( '.', $parts );
			} else {
				$relation = $table;
			}
		} else {
			$column = $name;
		}

		return array( $column, $table, $relation, $parts );
	}

	/**
	 * Get the alias for the model
	 *
	 * @return null|string
	 */
	public function getAlias() {
		if ( $this->table_alias ) {
			return $this->table_alias;
		}

		return $this->table;
	}

	/**
	 * Get the alias for the model
	 *
	 * @param string $alias The table alias to use
	 *
	 * @return void
	 */
	public function setAlias( $alias ) {
		$this->table_alias = $alias;
	}

	/**
	 * Select the fields using dot notation for related table fields
	 *
	 * E.G.
	 *  - column_name: The column "column_name" on the current model
	 *  - other_model.column_name: The column "column_name" on the "other_model" model
	 *
	 * This currently only supports 1-1 joins as 1-n will return multiple records
	 *
	 * You can optionally provide a closure for the name and it will be past the query object. In this scenario, the key
	 * on that array entry will be passed to the closure as well. I.E. `Closure(Builder $query, $alias)`
	 *
	 *
	 * @param Builder $builder The query builder
	 * @param array $columns Array of column names
	 * @param array|string $join The join that needs to be performed. Can be a string of "inner", "left", or "right", or
	 *                      can be an array with the relations as the index and the type of join.
	 *
	 * @return Builder
	 */
	public function scopeWithSelect( Builder $builder, $columns = [ '*' ], $join = 'left' ): Builder {
		$columns = is_array( $columns ) ? $columns : func_get_args();

		if ( is_null( $this->table_alias ) ) {
			$this->setAlias( camel_case( class_basename( self::class ) ) );
		}

		$builder->from( "{$this->getTable()} as {$this->getAlias()}" );

		foreach ( $columns as $alias => $name ) {
			$this->addSelect( $builder, $name, $alias, $join );
		}

		return $builder;
	}

	/**
	 * Add a column to the result set
	 *
	 * This method will call the withJoin to add joins based on the field being selected
	 *
	 * @param Builder $builder The query builder
	 * @param string $name The name of the column wanted
	 * @param array|string $join The join that needs to be performed. Can be a string of "inner", "left", or "right", or
	 *                      can be an array with the relations as the index and the type of join.
	 * @param $alias
	 */
	protected function addSelect( Builder $builder, $name, $alias, $join = 'left' ): void {
		$relation = null;
		$column   = null;

		if ( is_integer( $alias ) ) {
			$alias = $name;
		}

		//Call the closure
		if ( is_callable( $name ) ) {
			$name( $builder, $alias );

			return;
		}

		$model = $this;

		//Add it directly
		//Extract the relation
		if ( strpos( $name, '.' ) !== false ) {
			list( $column, $table, $relation, $parts ) = self::extractRelation( $name );
			if ( $relation !== $this->getAlias() ) {
				$builder->withJoin( $relation, $join );
				$model = self::getRelationalModel( $relation, $this );
			}
			$name = $table . '.' . $column;
		} else {
			$column = $name;
			$name   = $this->getAlias() . '.' . $name;
		}

		if ( $virtual = $model->getVirtualField( $column ) ) {
			$fields = [];
			foreach ( $virtual as $field ) {
				$fields[] = '`' . $model->getAlias() . '`.`' . $field . '`';
			}
			$column = sprintf( "CONCAT_WS(' ', %s)", implode( ', ', $fields ) );
			$select = DB::raw( "$column as `$name`" );
		} else {
			$select = $name . ' as ' . $alias;
		}
		$builder->addSelect( $select );
	}

	public function getVirtualField( $column ) {
		if ( isset( $this->virtualFields[ $column ] ) ) {
			return $this->virtualFields[ $column ];
		} else {
			return null;
		}
	}

	/**
	 * Apply a where condition
	 *
	 * This method will call the withJoin to add joins based on the given column
	 *
	 * @param Builder $builder The query builder
	 * @param string $name The name of the column to apply the condition to
	 * @param mixed $value The value to apply to the condition
	 *
	 * @return Builder
	 */
	public function scopeWithWhere( Builder $builder, $name, $value ): Builder {
		$relation = null;
		$column   = null;

		//Add it directly
		if ( is_string( $name ) ) {

			//Extract the relation
			if ( strpos( $name, '.' ) !== false ) {
				list( $column, $table, $relation, $parts ) = self::extractRelation( $name );
				if ( $relation !== $this->getAlias() ) {
					$builder->withJoin( $relation, 'inner' );
				}
				$name = $table . '.' . $column;
			} else {
				$name = $this->getAlias() . '.' . $name;
			}

			if ( is_array( $value ) ) {
				$builder->whereIn( $name, $value );
			} else {
				$builder->where( $name, $value );
			}
		} //Call the closure
		else if ( $name instanceof \Closure ) {
			$name( $builder, $value );
		}

		return $builder;
	}


	/**
	 * Finds the requested relationship in the given model
	 *
	 * @param array|string $relation The name of the relation
	 * @param Model $model the model relationship definition
	 * @param boolean $relationships Return an array of the relationships in the dot notation or false to only
	 *      return the last relation
	 *
	 * @return null|array The found relationship, relationships if set to true, or null
	 */
	static function getRelationship( $relation, $model, $relationships = false ) {
		$relationship = null;

		//determine if the relation is chained
		if ( is_string( $relation ) ) {
			if ( strpos( $relation, '.' ) !== false ) {
				$relations = preg_split( '/\./', $relation );
			} else {
				$relations = array_wrap( $relation );
			}
		} else {
			$relations = $relation;
		}

		$relation = array_shift( $relations );
		if ( $relation === $model->getAlias() ) {
			if ( $relations ) {
				$relation = array_shift( $relations );
			}
		}

		if ( isset( $model->relationships ) ) {
			foreach ( $model->relationships as $type => $list ) {
				if ( key_exists( $relation, $list ) ) {
					$relationship          = &$model->relationships[ $type ][ $relation ];
					$relationship['model'] = new $relationship['related']();
					$relationship['model']->setAlias( $relation );
					if ( $relationships ) {
						if ( $relationships === true ) {
							$relationships = [];
						}
						array_push( $relationships, $relationship );
					}
				}
			}
		}

		if ( $relations && $relationship ) {
			return Relational::getRelationship( $relations, $relationship['model'], $relationships );
		} else {
			if ( is_array( $relationships ) ) {
				return $relationships;
			} else {
				return $relationship;
			}
		}
	}

	/**
	 * Add the join
	 *
	 * @param Builder $builder The Query Builder
	 * @param string|array $relations The list of relations we are joining
	 * @param string|array $join The join(s) to perform
	 * @param string $operator The operator to use
	 * @param boolean $withoutTrashed Include delete_at records (without means it will add deleted_at to the conditions)
	 *
	 * @return Builder
	 */
	public function scopeWithJoin( Builder $builder, $relations, $join = 'left', $operator = '=', $withoutTrashed = true ): Builder {
		$relations = array_wrap( $relations );
		foreach ( $relations as $relation ) {
			$this->getJoinSubQuery( $builder, $this, $this->getAlias(), $relation, $join, $operator, $withoutTrashed );
		}

		return $builder;
	}

	/**
	 * Add on the join statement to join the relation
	 *
	 * If the builder has the `SoftDeletingScope::class` scope, it will ensure the join applies the scope the the
	 * related table join if that associated table supports soft deletes
	 *
	 * @param Builder $builder The source Builder
	 * @param Model $model The model being used
	 * @param string $alias The alias to use for the parent table
	 * @param string $relation The relation name to join
	 * @param string $join The join type to use
	 * @param string $operator The operator to use on the join
	 * @param boolean $withoutTrashed Include delete_at records (without means it will add deleted_at to the conditions)
	 *
	 * @throws \Exception
	 */
	protected function getJoinSubQuery( Builder $builder, Model $model, $alias, $relation, $join = 'left', $operator = '=', $withoutTrashed = true ) {
		if ( ! in_array( Relational::class, class_uses( $model ) ) ) {
			throw new \Exception( sprintf( "Class %s must use the Trait %s.", get_class( $model ), Relational::class ) );
		}

		$query = $builder->getQuery();

		//determine if the relation is chained
		$relations = preg_split( '/\./', $relation, 2 );
		$relation  = array_shift( $relations );

		//only process if the join is not for the root model
		if ( $relation !== $model->getAlias() ) {
			//Work out the join method
			$joinMethod = 'left';
			if ( is_array( $join ) ) {
				if ( isset( $join[ $relation ] ) ) {
					$joinMethod = $join[ $relation ];
				}
			} else {
				$joinMethod = $join;
			}
			if ( $joinMethod === 'inner' ) {
				$joinMethod = 'join';
			} elseif ( $joinMethod === 'right' ) {
				$joinMethod = 'rightJoin';
			} else {
				$joinMethod = 'leftJoin';
			}

			$relationship = self::getRelationship( $relation, $model );
			if ( $relationship === null ) {
				throw new \BadMethodCallException( sprintf( "Relationship %s does not exist on %s, cannot join.", $relation, get_class( $model ) ) );
			}

			/**@var Model; */
			$foreign = new $relationship['related']();

			$table = "{$foreign->getTable()} as {$relation}";
			$foreign->setAlias( $relation );

			$withoutTrashed = ( $withoutTrashed && $foreign->hasGlobalScope( SoftDeletingScope::class ) && ! in_array( SoftDeletingScope::class, $builder->removedScopes() ) );

			//check we don't already have this joined
			if ( ! isset( $this->joined[ $relation ] ) ) {
				switch ( $relationship['type'] ) {
					case 'hasOne':
					case 'hasMany':
						$query->{$joinMethod}(
							"{$table}",
							function ( $q ) use ( $relation, $operator, $alias, $relationship, $model, $foreign, $withoutTrashed ) {
								$q->on(
									$relation . "." . ( isset( $relationship['foreignKey'] ) ? $relationship['foreignKey'] : $model->getForeignKey() ),
									$operator,
									$alias . "." . ( isset( $relationship['localKey'] ) ? $relationship['localKey'] : $model->getKeyName() )
								);
								//detect if we must add the soft delete
								if ( $withoutTrashed ) {
									$q->whereNull( $foreign->getQualifiedDeletedAtColumn() );
								}
							}
						);
						break;
					case 'belongsTo':
						$query->{$joinMethod}(
							"{$table}",
							function ( $q ) use ( $relation, $operator, $alias, $relationship, $model, $foreign, $withoutTrashed ) {
								$q->on(
									$relation . "." . ( isset( $relationship['ownerKey'] ) ? $relationship['ownerKey'] : $foreign->getKeyName() ),
									$operator,
									$alias . "." . ( isset( $relationship['foreignKey'] ) ? $relationship['foreignKey'] : $foreign->getKeyName() )
								);
								//detect if we must add the soft delete
								if ( $withoutTrashed ) {
									$q->whereNull( $foreign->getQualifiedDeletedAtColumn() );
								}
							}
						);
						break;
					case 'belongsToMany':
					case 'belongsToThrough':
						$query->{$joinMethod}(
							"{$relationship['table']}",
							$relationship['table'] . "." . ( isset( $relationship['foreignKey'] ) ? $relationship['foreignKey'] : $foreign->getKeyName() ),
							$operator,
							$alias . "." . ( isset( $relationship['relatedKey'] ) ? $relationship['relatedKey'] : $model->getKeyName() )
						);
						$query->{$joinMethod}(
							"{$table}",
							function ( $q ) use ( $relation, $operator, $alias, $relationship, $model, $foreign, $withoutTrashed ) {
								$q->on(
									$relation . "." . $foreign->getKeyName(),
									$operator,
									$relationship['table'] . "." . ( isset( $relationship['relatedPivotKey'] ) ? $relationship['relatedPivotKey'] : $foreign->getForeignKey() )
								);
								//detect if we must add the soft delete
								if ( $withoutTrashed ) {
									$q->whereNull( $foreign->getQualifiedDeletedAtColumn() );
								}
							}
						);
						break;
					//Note: Many values result in multiple records and the relational trait is designed for 1-1 joins
					//case 'morphMany':
					//case 'morphToMany':
					//case 'hasManyThrough':
				}
				$this->joined[ $relation ] = $foreign;
			}
		} else {
			$foreign = $model;
		}

		//continue with other joins if they exist
		if ( $relations ) {
			$this->getJoinSubQuery( $builder, $foreign, $relation, $relations[0], $join, $operator, $withoutTrashed );
		}
	}

	/**
	 * Gets the labels off the model attribute
	 *
	 * @param $attribute
	 *
	 * @return string
	 */
	public function getAttributeLabel( $attribute ) {
		if ( isset( $this->labels[ $attribute ] ) ) {
			return $this->labels[ $attribute ];
		} else {
			return title_case( str_replace( '_', ' ', snake_case( $attribute ) ) );
		}
	}

	/**
	 * Gets the title for the model
	 *
	 * @return string
	 */
	public function getTitle() {
		if ( isset( $this->title ) ) {
			return $this->title;
		} else {
			return title_case( str_replace( '_', ' ', snake_case( $this->getAlias() ) ) );
		}
	}

	/**
	 * Gets the display field for the model
	 *
	 * @return string
	 */
	public function getDisplayField() {
		return $this->displayField;
	}

	/**
	 * Get the table qualified display field.
	 *
	 * @return string
	 */
	public function getQualifiedDisplayField() {
		return $this->qualifyColumn( $this->getDisplayField() );
	}

	/**
	 * @param $relation
	 *
	 * @return bool
	 */
	public function hasRelationship( $relation ) {
		return (
			strpos( $relation, '.' ) !== false && self::getRelationship( $relation, $this ) !== null
			|| $relation === $this->getAlias()
			|| isset( $this->mapped_relations[ $relation ] )
		);
	}

	/**
	 * Generates a key > name list of values in this table
	 *
	 * @param array|\Closure|null $condition The conditions to apply to the list query or a closure if you wish to modify or add conditions to the query before the selection is made
	 * @param \Illuminate\Database\Eloquent\Builder|null (Optional) query to use instead of creating a new one off the current Model
	 * @param Model|null (Optional) Model to use for the table containing the display and key values This is needed with
	 *  using a query which is from the root of a dot notation get list and helps use ensure we use the correct alias
	 *  for the list
	 * @param boolean $withoutTrashed (Optional) To return entries with trashed
	 *
	 * @return array
	 * @throws \Exception
	 */
	static function getList( $condition = null, $query = null, $model = null, $withoutTrashed = true, $relationship = null ) {
		if ( $model == null ) {
			$model = new static;
		}
		if ( $model->getDisplayField() === null ) {
			throw new \Exception( sprintf( "Property displayField must be set on %s.", self::class ) );
		}
		if ( $query === null ) {
			if ( ! $withoutTrashed ) {
				$query = $model->newQueryWithoutScope( SoftDeletingScope::class );
			} else {
				$query = self::query();
			}
		}

		if ( $relationship ) {
			$displayField = $relationship . '.' . $model->getDisplayField();
			$primaryKey   = $relationship . '.' . $model->getKeyName();

		} else {
			$displayField = $model->qualifyColumn( $model->getDisplayField() );
			$primaryKey   = $model->qualifyColumn( $model->getKeyName() );
		}

		if ( is_callable( $condition ) ) {
			call_user_func( $condition, $query );
		} elseif ( is_array( $condition ) ) {
			foreach ( $condition as $field => $value ) {
				if ( is_callable( $value ) ) {
					call_user_func( $value, $query );
				} else {
					$query->withWhere( $field, $value );
				}
			}
		}

		$query
			->withSelect( [ $displayField, $primaryKey ] )
			->whereNotNull( $model->qualifyColumn( $model->getKeyName() ) );

		if ( $virtual = $model->getVirtualField( $model->getDisplayField() ) ) {
			foreach ( $virtual as $field ) {
				$query->orderBy( $model->qualifyColumn( $field ), 'ASC' );
			}
		} else {
			$query
				->orderBy( $model->qualifyColumn( $model->getDisplayField() ), 'ASC' )
				->whereNotNull( $model->qualifyColumn( $model->getDisplayField() ) );
		}

//		dump($query->toSql());
//		die();

		$results = $query->get()->toArray();

		return array_column( $results, $displayField, $primaryKey );
	}

	/**
	 * Qualify the given column name by the model's table.
	 *
	 * @param  string $column
	 *
	 * @return string
	 */
	public function qualifyColumn( $column ) {
		if ( Str::contains( $column, '.' ) ) {
			return $column;
		}
		if ( $this->getTable() !== $this->getAlias() ) {
			return $this->getAlias() . '.' . $column;
		}

		return $this->getTable() . '.' . $column;
	}

	/**
	 * Get the relation Model
	 *
	 * This will search down a dot notated relation to get the bottom associated model for the relation
	 *
	 * @param string $relation The relation to find
	 * @param Model $model The base model to search from
	 *
	 * @return Model
	 * @throws \Exception
	 */
	static function getRelationalModel( $relation, Model $model ) {
		if ( ! in_array( Relational::class, class_uses( $model ) ) ) {
			throw new \Exception( sprintf( "Class %s must use the Trait %s.", get_class( $model ), Relational::class ) );
		}

		//determine if the relation is chained
		$relations = preg_split( '/\./', $relation, 2 );
		$relation  = array_shift( $relations );

		if ( $relation === $model->getAlias() ) {
			if ( empty( $relations ) ) {
				return $model;
			} else {
				$relation = array_shift( $relations );
			}
		}

		$relationship = Relational::getRelationship( $relation, $model );
		if ( $relationship === null ) {
			throw new \Exception( sprintf( "Relationship %s does not exist on %s.", lcfirst( studly_case( $relation ) ), get_class( $model ) ) );
		}
		$class = new $relationship['related']();
		if ( $relations ) {
			return self::getRelationalModel( $relations[0], $class );
		}

		if ( ! in_array( Relational::class, class_uses( $class ) ) ) {
			throw new \Exception( sprintf( "Class %s must use the Trait %s.", get_class( $class ), Relational::class ) );
		}
		$class->setAlias( $relation );

		return $class;
	}

	/**
	 * Handle dynamic method calls into the model and check the call is not for a relationship.
	 *
	 * @param  string $method
	 * @param  array $parameters
	 *
	 * @return mixed
	 */
//	public function __call($method, $parameters)
//	{
//		if ($this->hasRelationship($method) && $relationship = self::getRelationship($method, $this)) {
//			$type = $relationship['type'];
//			unset($relationship['type']);
//			if (in_array($type, $this->relation_types)) {
//				return $this->$type(...$relationship);
//			}
//		}
//		return parent::__call($method, $parameters);
//	}

}