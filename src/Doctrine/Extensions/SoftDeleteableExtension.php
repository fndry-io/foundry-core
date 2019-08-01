<?php

namespace Foundry\Core\Doctrine\Extensions;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\SoftDeleteable\SoftDeleteableListener;
use LaravelDoctrine\Extensions\GedmoExtension;

class SoftDeleteableExtension extends GedmoExtension
{
    /**
     * @param EventManager           $manager
     * @param EntityManagerInterface $em
     * @param Reader                 $reader
     */
    public function addSubscribers(EventManager $manager, EntityManagerInterface $em, Reader $reader = null)
    {
        $subscriber = new SoftDeleteableListener();

        $this->addSubscriber($subscriber, $manager, $reader);
    }

    /**
     * @return array
     */
    public function getFilters()
    {
	    /**
	     * We removed the filter as this caused too large a ramification in the system when retrieving content which was linked to soft deleted items
	     */
        return [];
    }
}
