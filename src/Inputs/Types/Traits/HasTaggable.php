<?php

namespace Foundry\Core\Inputs\Types\Traits;

trait HasTaggable
{
	public function setTaggable($value = null)
	{
		$this->setAttribute('taggable', $value);
		return $this;
	}

	public function getTaggable()
	{
		return $this->getAttribute('taggable');
	}

	public function setTaggableUrl($value = null)
	{
		$this->setAttribute('taggableUrl', $value);
		return $this;
	}

	public function getTaggableUrl()
	{
		return $this->getAttribute('taggableUrl');
	}

	public function setTaggableParam($value = null)
	{
		$this->setAttribute('taggableParam', $value);
		return $this;
	}

	public function getTaggableParam()
	{
		return $this->getAttribute('taggableParam');
	}
}