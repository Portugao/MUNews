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

namespace MU\NewsModule\Controller;

use MU\NewsModule\Controller\Base\AbstractMessageController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Zikula\ThemeModule\Engine\Annotation\Theme;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use MU\NewsModule\Entity\MessageEntity;
use MU\NewsModule\Entity\Factory\EntityFactory;
use MU\NewsModule\Form\Handler\Message\EditHandler;
use MU\NewsModule\Helper\ControllerHelper;
use MU\NewsModule\Helper\EntityDisplayHelper;
use MU\NewsModule\Helper\HookHelper;
use MU\NewsModule\Helper\PermissionHelper;
use MU\NewsModule\Helper\ViewHelper;
use MU\NewsModule\Helper\WorkflowHelper;

/**
 * Message controller class providing navigation and interaction functionality.
 */
class MessageController extends AbstractMessageController
{
    /**
     *
     * @Route("/admin/messages",
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminIndexAction(
        Request $request,
        PermissionHelper $permissionHelper
    ): Response {
        return $this->indexInternal(
            $request,
            $permissionHelper,
            true
        );
    }
    
    /**
     *
     * @Route("/messages",
     *        methods = {"GET"}
     * )
     */
    public function indexAction(
        Request $request,
        PermissionHelper $permissionHelper
    ): Response {
        return $this->indexInternal(
            $request,
            $permissionHelper,
            false
        );
    }
    
    /**
     *
     * @Route("/admin/messages/view/{sort}/{sortdir}/{page}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "page" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "page" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminViewAction(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        string $sort,
        string $sortdir,
        int $page,
        int $num
    ): Response {
        return $this->viewInternal(
            $request,
            $router,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $sort,
            $sortdir,
            $page,
            $num,
            true
        );
    }
    
    /**
     *
     * @Route("/messages/view/{sort}/{sortdir}/{page}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "page" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "page" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function viewAction(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        string $sort,
        string $sortdir,
        int $page,
        int $num
    ): Response {
        return $this->viewInternal(
            $request,
            $router,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $sort,
            $sortdir,
            $page,
            $num,
            false
        );
    }
    
    /**
     *
     * @Route("/admin/message/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminEditAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EditHandler $formHandler
    ): Response {
        return $this->editInternal(
            $request,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $formHandler,
            true
        );
    }
    
    /**
     *
     * @Route("/message/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     */
    public function editAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EditHandler $formHandler
    ): Response {
        return $this->editInternal(
            $request,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $formHandler,
            false
        );
    }
    
    /**
     *
     * @Route("/admin/message/delete/{slug}.{_format}",
     *        requirements = {"slug" = "[^/.]+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminDeleteAction(
        Request $request,
        LoggerInterface $logger,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        CurrentUserApiInterface $currentUserApi,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        string $slug
    ): Response {
        return $this->deleteInternal(
            $request,
            $logger,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $entityFactory,
            $currentUserApi,
            $workflowHelper,
            $hookHelper,
            $slug,
            true
        );
    }
    
    /**
     *
     * @Route("/message/delete/{slug}.{_format}",
     *        requirements = {"slug" = "[^/.]+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     */
    public function deleteAction(
        Request $request,
        LoggerInterface $logger,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        CurrentUserApiInterface $currentUserApi,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        string $slug
    ): Response {
        return $this->deleteInternal(
            $request,
            $logger,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $entityFactory,
            $currentUserApi,
            $workflowHelper,
            $hookHelper,
            $slug,
            false
        );
    }
    
    /**
     *
     * @Route("/admin/message/{slug}.{_format}",
     *        requirements = {"slug" = "[^/.]+", "_format" = "html|xml|json|ics|pdf"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminDisplayAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper,
        MessageEntity $message = null,
        string $slug = ''
    ): Response {
        return $this->displayInternal(
            $request,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $entityFactory,
            $entityDisplayHelper,
            $message,
            $slug,
            true
        );
    }
    
    /**
     *
     * @Route("/message/{slug}.{_format}",
     *        requirements = {"slug" = "[^/.]+", "_format" = "html|xml|json|ics|pdf"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function displayAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper,
        MessageEntity $message = null,
        string $slug = ''
    ): Response {
        return $this->displayInternal(
            $request,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $entityFactory,
            $entityDisplayHelper,
            $message,
            $slug,
            false
        );
    }
    
    /**
     * Process status changes for multiple items.
     *
     * @Route("/admin/messages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     * @Theme("admin")
     */
    public function adminHandleSelectedEntriesAction(
        Request $request,
        LoggerInterface $logger,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        CurrentUserApiInterface $currentUserApi
    ): RedirectResponse {
        return $this->handleSelectedEntriesActionInternal(
            $request,
            $logger,
            $entityFactory,
            $workflowHelper,
            $hookHelper,
            $currentUserApi,
            true
        );
    }
    
    /**
     * Process status changes for multiple items.
     *
     * @Route("/messages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     */
    public function handleSelectedEntriesAction(
        Request $request,
        LoggerInterface $logger,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        CurrentUserApiInterface $currentUserApi
    ): RedirectResponse {
        return $this->handleSelectedEntriesActionInternal(
            $request,
            $logger,
            $entityFactory,
            $workflowHelper,
            $hookHelper,
            $currentUserApi,
            false
        );
    }
    
    /**
     *
     * @Route("/message/handleInlineRedirect/{idPrefix}/{commandName}/{id}",
     *        requirements = {"id" = "\d+"},
     *        defaults = {"commandName" = "", "id" = 0},
     *        methods = {"GET"}
     * )
     */
    public function handleInlineRedirectAction(
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper,
        string $idPrefix,
        string $commandName,
        int $id = 0
    ): Response
     {
        return parent::handleInlineRedirectAction(
            $entityFactory,
            $entityDisplayHelper,
            $idPrefix,
            $commandName,
            $id
        );
    }
    
    // feel free to add your own controller methods here
}