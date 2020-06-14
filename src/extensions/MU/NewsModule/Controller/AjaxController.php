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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use MU\NewsModule\Controller\Base\AbstractAjaxController;
use MU\NewsModule\Entity\Factory\EntityFactory;
use MU\NewsModule\Helper\ControllerHelper;
use MU\NewsModule\Helper\EntityDisplayHelper;
use MU\NewsModule\Helper\PermissionHelper;

/**
 * Ajax controller implementation class.
 *
 * @Route("/ajax")
 */
class AjaxController extends AbstractAjaxController
{
    
    /**
     *
     * @Route("/getItemListFinder", methods = {"GET"}, options={"expose"=true})
     */
    public function getItemListFinderAction(
        Request $request,
        ControllerHelper $controllerHelper,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper
    ): JsonResponse {
        return parent::getItemListFinderAction(
            $request,
            $controllerHelper,
            $permissionHelper,
            $entityFactory,
            $entityDisplayHelper
        );
    }
    
    /**
     * @Route("/checkForDuplicate", methods = {"GET"}, options={"expose"=true})
     */
    public function checkForDuplicateAction(
        Request $request,
        ControllerHelper $controllerHelper,
        EntityFactory $entityFactory
    ): JsonResponse {
        return parent::checkForDuplicateAction(
            $request,
            $controllerHelper,
            $entityFactory
        );
    }
    
    /**
     * @Route("/toggleFlag", methods = {"POST"}, options={"expose"=true})
     */
    public function toggleFlagAction(
        Request $request,
        LoggerInterface $logger,
        EntityFactory $entityFactory,
        CurrentUserApiInterface $currentUserApi
    ): JsonResponse {
        return parent::toggleFlagAction(
            $request,
            $logger,
            $entityFactory,
            $currentUserApi
        );
    }
    
    /**
     * @Route("/updateSortPositions", methods = {"POST"}, options={"expose"=true})
     */
    public function updateSortPositionsAction(
        Request $request,
        EntityFactory $entityFactory
    ): JsonResponse {
        return parent::updateSortPositionsAction(
            $request,
            $entityFactory
        );
    }

    // feel free to add your own ajax controller methods here
}
