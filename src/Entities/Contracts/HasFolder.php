<?php

namespace Foundry\Core\Entities\Contracts;

interface HasFolder
{

	/**
	 * @return string
	 * @throws \ReflectionException
	 */
	public function getFolderName(): string;

	/**
	 * @return null|IsFolder
	 */
	public function getFolderParent(): ?IsFolder;

	/**
	 * @return HasFolder
	 */
	public function getFolderableEntity(): ?HasFolder;

	/**
	 * @param IsFolder $folder
	 */
	public function setFolder(IsFolder $folder): void;

	/**
	 * @return IsFolder|null
	 */
	public function getFolder(): ?IsFolder;

	/**
	 * @return IsFolder
	 */
	public function makeFolder(): IsFolder;


}