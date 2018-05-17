<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Controller;

use MU\NewsModule\Controller\Base\AbstractMessageController;

use RuntimeException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zikula\ThemeModule\Engine\Annotation\Theme;
use MU\NewsModule\Entity\MessageEntity;

/**
 * Message controller class providing navigation and interaction functionality.
 */
class MessageController extends AbstractMessageController
{
    /**
     * @inheritDoc
     *
     * @Route("/admin/messages",
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminIndexAction(Request $request)
    {
        return parent::adminIndexAction($request);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/messages",
     *        methods = {"GET"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }
    /**
     * @inheritDoc
     *
     * @Route("/admin/messages/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     *
     * @param Request $request Current request instance
     * @param string $sort         Sorting field
     * @param string $sortdir      Sorting direction
     * @param int    $pos          Current pager position
     * @param int    $num          Amount of entries to display
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminViewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return parent::adminViewAction($request, $sort, $sortdir, $pos, $num);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/messages/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     *
     * @param Request $request Current request instance
     * @param string $sort         Sorting field
     * @param string $sortdir      Sorting direction
     * @param int    $pos          Current pager position
     * @param int    $num          Amount of entries to display
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function viewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return parent::viewAction($request, $sort, $sortdir, $pos, $num);
    }
    /**
     * @inheritDoc
     *
     * @Route("/admin/message/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by form handler if message to be edited isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function adminEditAction(Request $request)
    {
        return parent::adminEditAction($request);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/message/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by form handler if message to be edited isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function editAction(Request $request)
    {
        return parent::editAction($request);
    }
    /**
     * @inheritDoc
     *
     * @Route("/admin/message/delete/{slug}.{_format}",
     *        requirements = {"slug" = "[^/.]+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     * @ParamConverter("message", class="MUNewsModule:MessageEntity", options = {"repository_method" = "selectBySlug", "mapping": {"slug": "slugTitle"}, "map_method_signature" = true})
     * @Theme("admin")
     *
     * @param Request $request Current request instance
     * @param MessageEntity $message Treated message instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if message to be deleted isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function adminDeleteAction(Request $request, MessageEntity $message)
    {
        return parent::adminDeleteAction($request, $message);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/message/delete/{slug}.{_format}",
     *        requirements = {"slug" = "[^/.]+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     * @ParamConverter("message", class="MUNewsModule:MessageEntity", options = {"repository_method" = "selectBySlug", "mapping": {"slug": "slugTitle"}, "map_method_signature" = true})
     *
     * @param Request $request Current request instance
     * @param MessageEntity $message Treated message instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if message to be deleted isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function deleteAction(Request $request, MessageEntity $message)
    {
        return parent::deleteAction($request, $message);
    }
    /**
     * @inheritDoc
     *
     * @Route("/admin/message/{slug}.{_format}",
     *        requirements = {"slug" = "[^/.]+", "_format" = "html|xml|json|ics|pdf"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     * @ParamConverter("message", class="MUNewsModule:MessageEntity", options = {"repository_method" = "selectBySlug", "mapping": {"slug": "slugTitle"}, "map_method_signature" = true})
     * @Theme("admin")
     *
     * @param Request $request Current request instance
     * @param MessageEntity $message Treated message instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if message to be displayed isn't found
     */
    public function adminDisplayAction(Request $request, MessageEntity $message)
    {
        return parent::adminDisplayAction($request, $message);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/message/{slug}.{_format}",
     *        requirements = {"slug" = "[^/.]+", "_format" = "html|xml|json|ics|pdf"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     * @ParamConverter("message", class="MUNewsModule:MessageEntity", options = {"repository_method" = "selectBySlug", "mapping": {"slug": "slugTitle"}, "map_method_signature" = true})
     *
     * @param Request $request Current request instance
     * @param MessageEntity $message Treated message instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if message to be displayed isn't found
     */
    public function displayAction(Request $request, MessageEntity $message)
    {
        return parent::displayAction($request, $message);
    }

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @Route("/admin/messages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     * @Theme("admin")
     *
     * @param Request $request Current request instance
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function adminHandleSelectedEntriesAction(Request $request)
    {
        return parent::adminHandleSelectedEntriesAction($request);
    }
    
    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @Route("/messages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     *
     * @param Request $request Current request instance
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function handleSelectedEntriesAction(Request $request)
    {
        return parent::handleSelectedEntriesAction($request);
    }
    
    /**
     * This method includes the common implementation code for adminDisplay() and display().
     */
    protected function displayInternal(Request $request, MessageEntity $message, $isAdmin = false)
    {
        if (false === $isAdmin) {
            $entityManager = $this->getDoctrine()->getManager();
            $message->setAmountOfViews($message->getAmountOfViews() + 1);
            $entityManager->flush();
        }

        return parent:: displayInternal($request, $message, $isAdmin);
    }

    // feel free to add your own controller methods here
}
