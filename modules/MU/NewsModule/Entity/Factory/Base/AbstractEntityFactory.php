<?php

/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @see https://homepages-mit-zikula.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Entity\Factory\Base;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use MU\NewsModule\Entity\Factory\EntityInitialiser;
use MU\NewsModule\Entity\MessageEntity;
use MU\NewsModule\Entity\ImageEntity;
use MU\NewsModule\Helper\CollectionFilterHelper;
use MU\NewsModule\Helper\FeatureActivationHelper;

/**
 * Factory class used to create entities and receive entity repositories.
 */
abstract class AbstractEntityFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityInitialiser
     */
    protected $entityInitialiser;

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    public function __construct(
        EntityManagerInterface $entityManager,
        EntityInitialiser $entityInitialiser,
        CollectionFilterHelper $collectionFilterHelper,
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->entityManager = $entityManager;
        $this->entityInitialiser = $entityInitialiser;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }

    /**
     * Returns a repository for a given object type.
     *
     * @param string $objectType Name of desired entity type
     *
     * @return EntityRepository The repository responsible for the given object type
     */
    public function getRepository($objectType)
    {
        $entityClass = 'MU\\NewsModule\\Entity\\' . ucfirst($objectType) . 'Entity';

        /** @var EntityRepository $repository */
        $repository = $this->getEntityManager()->getRepository($entityClass);
        $repository->setCollectionFilterHelper($this->collectionFilterHelper);

        if (in_array($objectType, ['message'], true)) {
            $repository->setTranslationsEnabled(
                $this->featureActivationHelper->isEnabled(FeatureActivationHelper::TRANSLATIONS, $objectType)
            );
        }

        return $repository;
    }

    /**
     * Creates a new message instance.
     *
     * @return MessageEntity The newly created entity instance
     */
    public function createMessage()
    {
        $entity = new MessageEntity();

        $this->entityInitialiser->initMessage($entity);

        return $entity;
    }

    /**
     * Creates a new image instance.
     *
     * @return ImageEntity The newly created entity instance
     */
    public function createImage()
    {
        $entity = new ImageEntity();

        $this->entityInitialiser->initImage($entity);

        return $entity;
    }

    /**
     * Returns the identifier field's name for a given object type.
     *
     * @param string $objectType The object type to be treated
     *
     * @return string Primary identifier field name
     */
    public function getIdField($objectType = '')
    {
        if (empty($objectType)) {
            throw new InvalidArgumentException('Invalid object type received.');
        }
        $entityClass = 'MUNewsModule:' . ucfirst($objectType) . 'Entity';
    
        $meta = $this->getEntityManager()->getClassMetadata($entityClass);
    
        return $meta->getSingleIdentifierFieldName();
    }
    
    /**
     * Returns the entity manager.
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
    
    /**
     * Sets the entity manager.
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return void
     */
    public function setEntityManager(EntityManagerInterface $entityManager = null)
    {
        if ($this->entityManager !== $entityManager) {
            $this->entityManager = $entityManager;
        }
    }
    
    /**
     * Returns the entity initialiser.
     *
     * @return EntityInitialiser
     */
    public function getEntityInitialiser()
    {
        return $this->entityInitialiser;
    }
    
    /**
     * Sets the entity initialiser.
     *
     * @param EntityInitialiser $entityInitialiser
     *
     * @return void
     */
    public function setEntityInitialiser(EntityInitialiser $entityInitialiser = null)
    {
        if ($this->entityInitialiser !== $entityInitialiser) {
            $this->entityInitialiser = $entityInitialiser;
        }
    }
}