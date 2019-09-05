<?php

namespace Foundry\Core\Inputs\Types;

class DateRangeType extends RowType {

	public function __construct() {
		parent::__construct();
		$this->setType( 'date-range' );
	}

	public function setStart(DateTimeInputType $date){
		$this->addChildren($date);
		$this->setAttribute('start', $date->getName());
		return $this;
	}

	public function setEnd(DateTimeInputType $date){
		$this->addChildren($date);
		$this->setAttribute('end', $date->getName());
		return $this;
	}
}