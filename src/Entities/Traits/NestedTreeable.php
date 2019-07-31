<?php
namespace Foundry\Core\Entities\Traits;

use Foundry\Core\Entities\Contracts\IsNestedTreeable;

trait NestedTreeable {

	/**
	 * @Gedmo\TreeLeft
	 * @ORM\Column(name="lft", type="integer")
	 */
	private $lft;

	/**
	 * @Gedmo\TreeLevel
	 * @ORM\Column(name="lvl", type="integer")
	 */
	private $lvl;

	/**
	 * @Gedmo\TreeRight
	 * @ORM\Column(name="rgt", type="integer")
	 */
	private $rgt;

	/**
	 * @Gedmo\TreeRoot
	 * @ORM\ManyToOne(targetEntity="Category")
	 * @ORM\JoinColumn(name="tree_root", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $root;

	/**
	 * @Gedmo\TreeParent
	 * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $parent;

	/**
	 * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
	 * @ORM\OrderBy({"lft" = "ASC"})
	 */
	private $children;

	public function getRoot()
	{
		return $this->root;
	}

	public function setParent(IsNestedTreeable $parent = null)
	{
		$this->parent = $parent;
	}

	public function getParent()
	{
		return $this->parent;
	}
}