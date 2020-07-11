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

declare(strict_types=1);

namespace MU\NewsModule\Helper\Base;

use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Workflow\Registry;
use Symfony\Contracts\Translation\TranslatorInterface;
use Translation\Extractor\Annotation\Desc;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use MU\NewsModule\Entity\Factory\EntityFactory;
use MU\NewsModule\Helper\ListEntriesHelper;
use MU\NewsModule\Helper\PermissionHelper;

/**
 * Helper base class for workflow methods.
 */
abstract class AbstractWorkflowHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var Registry
     */
    protected $workflowRegistry;
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var ListEntriesHelper
     */
    protected $listEntriesHelper;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    public function __construct(
        TranslatorInterface $translator,
        Registry $registry,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi,
        EntityFactory $entityFactory,
        ListEntriesHelper $listEntriesHelper,
        PermissionHelper $permissionHelper
    ) {
        $this->translator = $translator;
        $this->workflowRegistry = $registry;
        $this->logger = $logger;
        $this->currentUserApi = $currentUserApi;
        $this->entityFactory = $entityFactory;
        $this->listEntriesHelper = $listEntriesHelper;
        $this->permissionHelper = $permissionHelper;
    }
    
    /**
     * This method returns a list of possible object states.
     */
    public function getObjectStates(): array
    {
        $states = [];
        $states[] = [
            'value' => 'initial',
            'text' => $this->translator->trans('Initial'),
            'ui' => 'danger'
        ];
        $states[] = [
            'value' => 'waiting',
            'text' => $this->translator->trans('Waiting'),
            'ui' => 'warning'
        ];
        $states[] = [
            'value' => 'approved',
            'text' => $this->translator->trans('Approved'),
            'ui' => 'success'
        ];
        $states[] = [
            'value' => 'suspended',
            'text' => $this->translator->trans('Suspended'),
            'ui' => 'primary'
        ];
        $states[] = [
            'value' => 'archived',
            'text' => $this->translator->trans('Archived'),
            'ui' => 'info'
        ];
        $states[] = [
            'value' => 'trashed',
            'text' => $this->translator->trans('Trashed'),
            'ui' => 'danger'
        ];
        $states[] = [
            'value' => 'deleted',
            'text' => $this->translator->trans('Deleted'),
            'ui' => 'danger'
        ];
    
        return $states;
    }
    
    /**
     * This method returns information about a certain state.
     */
    public function getStateInfo(string $state = 'initial'): ?array
    {
        $result = null;
        $stateList = $this->getObjectStates();
        foreach ($stateList as $singleState) {
            if ($singleState['value'] !== $state) {
                continue;
            }
            $result = $singleState;
            break;
        }
    
        return $result;
    }
    
    /**
     * Retrieve the available actions for a given entity object.
     */
    public function getActionsForObject(EntityAccess $entity): array
    {
        $workflow = $this->workflowRegistry->get($entity);
        $wfActions = $workflow->getEnabledTransitions($entity);
        $currentState = $entity->getWorkflowState();
    
        $actions = [];
        foreach ($wfActions as $action) {
            $actionId = $action->getName();
            $actions[$actionId] = [
                'id' => $actionId,
                'title' => $this->getTitleForAction($currentState, $actionId),
                'buttonClass' => $this->getButtonClassForAction($actionId)
            ];
        }
    
        return $actions;
    }
    
    /**
     * Returns a translatable title for a certain action.
     */
    protected function getTitleForAction(string $currentState, string $actionId): string
    {
        $title = '';
        switch ($actionId) {
            case 'submit':
                $title = $this->translator->trans('Submit');
                break;
            case 'approve':
                $title = 'initial' === $currentState
                    ? $this->translator->trans('Submit and approve')
                    : $this->translator->trans('Approve')
                ;
                break;
            case 'unpublish':
                $title = $this->translator->trans('Unpublish');
                break;
            case 'publish':
                $title = $this->translator->trans('Publish');
                break;
            case 'archive':
                $title = $this->translator->trans('Archive');
                break;
            case 'unarchive':
                $title = $this->translator->trans('Unarchive');
                break;
            case 'trash':
                $title = $this->translator->trans('Trash');
                break;
            case 'recover':
                $title = $this->translator->trans('Recover');
                break;
            case 'delete':
                $title = $this->translator->trans('Delete');
                break;
        }
    
        if ('' === $title) {
            if ('update' === $actionId) {
                $title = $this->translator->trans('Update');
            } elseif ('trash' === $actionId) {
                $title = $this->translator->trans('Trash');
            } elseif ('recover' === $actionId) {
                $title = $this->translator->trans('Recover');
            }
        }
    
        return $title;
    }
    
    /**
     * Returns a button class for a certain action.
     */
    protected function getButtonClassForAction(string $actionId): string
    {
        $buttonClass = '';
        switch ($actionId) {
            case 'submit':
                $buttonClass = 'success';
                break;
            case 'approve':
                $buttonClass = 'success';
                break;
            case 'unpublish':
                $buttonClass = '';
                break;
            case 'publish':
                $buttonClass = '';
                break;
            case 'archive':
                $buttonClass = '';
                break;
            case 'unarchive':
                $buttonClass = '';
                break;
            case 'trash':
                $buttonClass = '';
                break;
            case 'recover':
                $buttonClass = '';
                break;
            case 'delete':
                $buttonClass = 'danger';
                break;
        }
    
        if ('' === $buttonClass && 'update' === $actionId) {
            $buttonClass = 'success';
        }
    
        if (!empty($buttonClass)) {
            $buttonClass = 'btn-' . $buttonClass;
        }
    
        return $buttonClass;
    }
    
    /**
     * Executes a certain workflow action for a given entity object.
     */
    public function executeAction(EntityAccess $entity, string $actionId = '', bool $recursive = false): bool
    {
        $workflow = $this->workflowRegistry->get($entity);
        if (!$workflow->can($entity, $actionId)) {
            return false;
        }
    
        // get entity manager
        $entityManager = $this->entityFactory->getEntityManager();
        $logArgs = ['app' => 'MUNewsModule', 'user' => $this->currentUserApi->get('uname')];
    
        $result = false;
        try {
            if ('delete' === $actionId) {
                $entityManager->remove($entity);
            } else {
                $entityManager->persist($entity);
            }
            // we flush two times on purpose to avoid a hen-egg problem with workflow post-processing
            // first we flush to ensure that the entity gets an identifier
            $entityManager->flush();
            // then we apply the workflow which causes additional actions, like notifications
            $workflow->apply($entity, $actionId);
            // then we flush again to save the new workflow state of the entity
            $entityManager->flush();
    
            $result = true;
            if ('delete' === $actionId) {
                $this->logger->notice('{app}: User {user} deleted an entity.', $logArgs);
            } else {
                $this->logger->notice('{app}: User {user} updated an entity.', $logArgs);
            }
        } catch (Exception $exception) {
            if ('delete' === $actionId) {
                $this->logger->error('{app}: User {user} tried to delete an entity, but failed.', $logArgs);
            } else {
                $this->logger->error('{app}: User {user} tried to update an entity, but failed.', $logArgs);
            }
            // uncomment to reveal Doctrine/SQL error
            // die($exception->getMessage());
            throw new RuntimeException($exception->getMessage());
        }
    
        if (false !== $result && !$recursive) {
            $entities = $entity->getRelatedObjectsToPersist();
            foreach ($entities as $rel) {
                if ('initial' === $rel->getWorkflowState()) {
                    $this->executeAction($rel, $actionId, true);
                }
            }
        }
    
        return false !== $result;
    }
    
    /**
     * Collects amount of moderation items foreach object type.
     */
    public function collectAmountOfModerationItems(): array
    {
        $amounts = [];
    
        // check if objects are waiting for approval
        $state = 'waiting';
        $objectType = 'message';
        if ($this->permissionHelper->hasComponentPermission($objectType, ACCESS_ADD)) {
            $amount = $this->getAmountOfModerationItems($objectType, $state);
            if (0 < $amount) {
                $amounts[] = [
                    'aggregateType' => 'messagesApproval',
                    'description' => $this->translator->trans('Messages pending approval', [], 'message'),
                    'amount' => $amount,
                    'objectType' => $objectType,
                    'state' => $state,
                    /** @Desc("{count, plural,\n  one   {One message is waiting for approval.}\n  other {# messages are waiting for approval.}\n}") */
                    'message' => $this->translator->trans(
                        'plural_n.messages.waiting_for_approval',
                        ['%count%' => $amount],
                        'message'
                    )
                ];
        
                $this->logger->info(
                    '{app}: There are {amount} {entities} waiting for approval.',
                    ['app' => 'MUNewsModule', 'amount' => $amount, 'entities' => 'messages']
                );
            }
        }
    
        return $amounts;
    }
    
    /**
     * Retrieves the amount of moderation items for a given object type
     * and a certain workflow state.
     */
    public function getAmountOfModerationItems(string $objectType = '', string $state = ''): int
    {
        $repository = $this->entityFactory->getRepository($objectType);
        $collectionFilterHelper = $repository->getCollectionFilterHelper();
        $repository->setCollectionFilterHelper(null);
    
        $where = 'tbl.workflowState = \'' . $state . '\'';
        $parameters = ['workflowState' => $state];
    
        $result = $repository->selectCount($where, false, $parameters);
        $repository->setCollectionFilterHelper($collectionFilterHelper);
    
        return $result;
    }
}
