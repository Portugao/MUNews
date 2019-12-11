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

namespace MU\NewsModule\Listener\Base;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zikula\Core\Doctrine\EntityAccess;

/**
 * Event subscriber base class for entity lifecycle events.
 */
abstract class AbstractEntityLifecycleListener implements EventSubscriber, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->setContainer($container);
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * Returns list of events to subscribe.
     *
     * @return string[] List of events
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preFlush,
            Events::onFlush,
            Events::postFlush,
            Events::preRemove,
            Events::postRemove,
            Events::prePersist,
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::postLoad
        ];
    }

    /**
     * The preFlush event is called at EntityManager#flush() before anything else.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#preflush
     */
    public function preFlush(PreFlushEventArgs $args)
    {
    }

    /**
     * The onFlush event is called inside EntityManager#flush() after the changes to all the
     * managed entities and their associations have been computed.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#onflush
     */
    public function onFlush(OnFlushEventArgs $args)
    {
    }

    /**
     * The postFlush event is called at the end of EntityManager#flush().
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postflush
     */
    public function postFlush(PostFlushEventArgs $args)
    {
    }

    /**
     * The preRemove event occurs for a given entity before the respective EntityManager
     * remove operation for that entity is executed. It is not called for a DQL DELETE statement.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#preremove
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // create the filter event and dispatch it
        $eventClass = '\\MU\\NewsModule\\NewsEvents';
        $event = $this->createFilterEvent($entity);
        $eventName = constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_PRE_REMOVE');
        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * The postRemove event occurs for an entity after the entity has been deleted. It will be
     * invoked after the database delete operations. It is not called for a DQL DELETE statement.
     *
     * Note that the postRemove event or any events triggered after an entity removal can receive
     * an uninitializable proxy in case you have configured an entity to cascade remove relations.
     * In this case, you should load yourself the proxy in the associated pre event.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postupdate-postremove-postpersist
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        $objectType = $entity->get_objectType();
        
        $uploadFields = $this->getUploadFields($objectType);
        if (0 < count($uploadFields)) {
            $uploadHelper = $this->container->get('mu_news_module.upload_helper');
            foreach ($uploadFields as $fieldName) {
                if (empty($entity[$fieldName])) {
                    continue;
                }
        
                // remove upload file
                $uploadHelper->deleteUploadFile($entity, $fieldName);
            }
        }
        
        $currentUserApi = $this->container->get('zikula_users_module.current_user');
        $logArgs = ['app' => 'MUNewsModule', 'user' => $currentUserApi->get('uname'), 'entity' => $objectType, 'id' => $entity->getKey()];
        $this->logger->debug('{app}: User {user} removed the {entity} with id {id}.', $logArgs);
        
        // create the filter event and dispatch it
        $eventClass = '\\MU\\NewsModule\\NewsEvents';
        $event = $this->createFilterEvent($entity);
        $eventName = constant($eventClass . '::' . strtoupper($objectType) . '_POST_REMOVE');
        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * The prePersist event occurs for a given entity before the respective EntityManager
     * persist operation for that entity is executed. It should be noted that this event
     * is only triggered on initial persist of an entity (i.e. it does not trigger on future updates).
     *
     * Doctrine will not recognize changes made to relations in a prePersist event.
     * This includes modifications to collections such as additions, removals or replacement.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#prepersist
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // create the filter event and dispatch it
        $eventClass = '\\MU\\NewsModule\\NewsEvents';
        $event = $this->createFilterEvent($entity);
        $eventName = constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_PRE_PERSIST');
        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * The postPersist event occurs for an entity after the entity has been made persistent.
     * It will be invoked after the database insert operations. Generated primary key values
     * are available in the postPersist event.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postupdate-postremove-postpersist
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        $currentUserApi = $this->container->get('zikula_users_module.current_user');
        $logArgs = ['app' => 'MUNewsModule', 'user' => $currentUserApi->get('uname'), 'entity' => $entity->get_objectType(), 'id' => $entity->getKey()];
        $this->logger->debug('{app}: User {user} created the {entity} with id {id}.', $logArgs);
        
        // create the filter event and dispatch it
        $eventClass = '\\MU\\NewsModule\\NewsEvents';
        $event = $this->createFilterEvent($entity);
        $eventName = constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_POST_PERSIST');
        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * The preUpdate event occurs before the database update operations to entity data.
     * It is not called for a DQL UPDATE statement nor when the computed changeset is empty.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#preupdate
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // create the filter event and dispatch it
        $eventClass = '\\MU\\NewsModule\\NewsEvents';
        $event = $this->createFilterEvent($entity);
        $eventName = constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_PRE_UPDATE');
        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * The postUpdate event occurs after the database update operations to entity data.
     * It is not called for a DQL UPDATE statement.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postupdate-postremove-postpersist
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        $currentUserApi = $this->container->get('zikula_users_module.current_user');
        $logArgs = ['app' => 'MUNewsModule', 'user' => $currentUserApi->get('uname'), 'entity' => $entity->get_objectType(), 'id' => $entity->getKey()];
        $this->logger->debug('{app}: User {user} updated the {entity} with id {id}.', $logArgs);
        
        // create the filter event and dispatch it
        $eventClass = '\\MU\\NewsModule\\NewsEvents';
        $event = $this->createFilterEvent($entity);
        $eventName = constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_POST_UPDATE');
        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * The postLoad event occurs for an entity after the entity has been loaded into the current
     * EntityManager from the database or after the refresh operation has been applied to it.
     *
     * Note that, when using Doctrine\ORM\AbstractQuery#iterate(), postLoad events will be executed
     * immediately after objects are being hydrated, and therefore associations are not guaranteed
     * to be initialized. It is not safe to combine usage of Doctrine\ORM\AbstractQuery#iterate()
     * and postLoad event handlers.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postload
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // prepare helper fields for uploaded files
        $uploadFields = $this->getUploadFields($entity->get_objectType());
        if (0 < count($uploadFields)) {
            $uploadHelper = $this->container->get('mu_news_module.upload_helper');
            $request = $this->container->get('request_stack')->getCurrentRequest();
            $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath();
        
            $entity->set_uploadBasePath($uploadHelper->getFileBaseFolder($entity->get_objectType()));
            $entity->set_uploadBaseUrl($baseUrl);
        
            // determine meta data if it does not exist
            foreach ($uploadFields as $fieldName) {
                if (empty($entity[$fieldName])) {
                    continue;
                }
        
                if (is_array($entity[$fieldName . 'Meta']) && count($entity[$fieldName . 'Meta'])) {
                    continue;
                }
                $basePath = $uploadHelper->getFileBaseFolder($entity->get_objectType(), $fieldName);
                $fileName = $entity[$fieldName . 'FileName'];
                $filePath = $basePath . $fileName;
                if (!file_exists($filePath)) {
                    continue;
                }
                $entity[$fieldName . 'Meta'] = $uploadHelper->readMetaDataForFile($fileName, $filePath);
            }
        }
        
        // create the filter event and dispatch it
        $eventClass = '\\MU\\NewsModule\\NewsEvents';
        $event = $this->createFilterEvent($entity);
        $eventName = constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_POST_LOAD');
        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * Checks whether this listener is responsible for the given entity or not.
     *
     * @param EntityAccess $entity The given entity
     *
     * @return bool True if entity is managed by this listener, false otherwise
     */
    protected function isEntityManagedByThisBundle($entity)
    {
        $entityClassParts = explode('\\', get_class($entity));

        if ('DoctrineProxy' === $entityClassParts[0] && '__CG__' === $entityClassParts[1]) {
            array_shift($entityClassParts);
            array_shift($entityClassParts);
        }

        return 'MU' === $entityClassParts[0] && 'NewsModule' === $entityClassParts[1];
    }

    /**
     * Returns a filter event instance for the given entity.
     *
     * @param EntityAccess $entity The given entity
     *
     * @return Event The created event instance
     */
    protected function createFilterEvent(EntityAccess $entity)
    {
        $filterEventClass = '\\MU\\NewsModule\\Event\\Filter' . ucfirst($entity->get_objectType()) . 'Event';

        return new $filterEventClass($entity);
    }

    /**
     * Returns list of upload fields for the given object type.
     *
     * @param string $objectType The object type
     *
     * @return string[] List of upload field names
     */
    protected function getUploadFields($objectType = '')
    {
        $uploadFields = [];
        switch ($objectType) {
            case 'message':
                $uploadFields = ['imageUpload1', 'imageUpload2', 'imageUpload3', 'imageUpload4'];
                break;
            case 'image':
                $uploadFields = ['theFile'];
                break;
        }

        return $uploadFields;
    }
}
