<?php

/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 *
 * @see https://homepages-mit-zikula.de
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

declare(strict_types=1);

namespace MU\NewsModule\Controller\Base;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\CoreBundle\Controller\AbstractController;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use MU\NewsModule\AppSettings;
use MU\NewsModule\Form\Type\ConfigType;
use MU\NewsModule\Helper\PermissionHelper;

/**
 * Config controller base class.
 */
abstract class AbstractConfigController extends AbstractController
{
    /**
     * This method takes care of the application configuration.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function configAction(
        Request $request,
        PermissionHelper $permissionHelper,
        AppSettings $appSettings,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): Response {
        if (!$permissionHelper->hasPermission(ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        
        $form = $this->createForm(ConfigType::class, $appSettings);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('save')->isClicked()) {
                $appSettings = $form->getData();
                $appSettings->save();
        
                $this->addFlash('status', $this->trans('Done! Configuration updated.', [], 'config'));
                $userName = $currentUserApi->get('uname');
                $logger->notice(
                    '{app}: User {user} updated the configuration.',
                    ['app' => 'ZikulaContentModule', 'user' => $userName]
                );
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', 'Operation cancelled.');
            }
        
            // redirect to config page again (to show with GET request)
            return $this->redirectToRoute('munewsmodule_config_config');
        }
        
        $templateParameters = [
            'form' => $form->createView(),
        ];
        
        // render the config form
        return $this->render('@MUNewsModule/Config/config.html.twig', $templateParameters);
    }
}
