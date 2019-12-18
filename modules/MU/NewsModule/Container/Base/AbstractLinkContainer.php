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

namespace MU\NewsModule\Container\Base;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use MU\NewsModule\Helper\ControllerHelper;
use MU\NewsModule\Helper\PermissionHelper;

/**
 * This is the link container service implementation class.
 */
abstract class AbstractLinkContainer implements LinkContainerInterface
{
    use TranslatorTrait;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        VariableApiInterface $variableApi,
        ControllerHelper $controllerHelper,
        PermissionHelper $permissionHelper
    ) {
        $this->setTranslator($translator);
        $this->router = $router;
        $this->variableApi = $variableApi;
        $this->controllerHelper = $controllerHelper;
        $this->permissionHelper = $permissionHelper;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns available header links.
     *
     * @param string $type The type to collect links for
     *
     * @return array List of header links
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $contextArgs = ['api' => 'linkContainer', 'action' => 'getLinks'];
        $allowedObjectTypes = $this->controllerHelper->getObjectTypes('api', $contextArgs);

        $permLevel = LinkContainerInterface::TYPE_ADMIN === $type ? ACCESS_ADMIN : ACCESS_READ;

        // Create an array of links to return
        $links = [];

        if (LinkContainerInterface::TYPE_ACCOUNT === $type) {
            if (!$this->permissionHelper->hasPermission(ACCESS_OVERVIEW)) {
                return $links;
            }

            if (true === $this->variableApi->get('MUNewsModule', 'linkOwnMessagesOnAccountPage', true)) {
                $objectType = 'message';
                if ($this->permissionHelper->hasComponentPermission($objectType, ACCESS_READ)) {
                    $routeArgs = ['own' => 1];
                    $routeName = 'munewsmodule_' . strtolower($objectType) . '_view';
                    $links[] = [
                        'url' => $this->router->generate($routeName, $routeArgs),
                        'text' => $this->__('My messages', 'munewsmodule'),
                        'icon' => 'list-alt'
                    ];
                }
            }

            if ($this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('munewsmodule_message_adminindex'),
                    'text' => $this->__('News Backend', 'munewsmodule'),
                    'icon' => 'wrench'
                ];
            }

            return $links;
        }

        $routeArea = LinkContainerInterface::TYPE_ADMIN === $type ? 'admin' : '';
        if (LinkContainerInterface::TYPE_ADMIN === $type) {
            if ($this->permissionHelper->hasPermission(ACCESS_READ)) {
                $links[] = [
                    'url' => $this->router->generate('munewsmodule_message_index'),
                    'text' => $this->__('Frontend', 'munewsmodule'),
                    'title' => $this->__('Switch to user area.', 'munewsmodule'),
                    'icon' => 'home'
                ];
            }
        } else {
            if ($this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('munewsmodule_message_adminindex'),
                    'text' => $this->__('Backend', 'munewsmodule'),
                    'title' => $this->__('Switch to administration area.', 'munewsmodule'),
                    'icon' => 'wrench'
                ];
            }
        }
        
        if (
            in_array('message', $allowedObjectTypes, true)
            && $this->permissionHelper->hasComponentPermission('message', $permLevel)
        ) {
            $links[] = [
                'url' => $this->router->generate('munewsmodule_message_' . $routeArea . 'view'),
                'text' => $this->__('Messages', 'munewsmodule'),
                'title' => $this->__('Messages list', 'munewsmodule')
            ];
        }
        if ('admin' === $routeArea && $this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
            $links[] = [
                'url' => $this->router->generate('munewsmodule_config_config'),
                'text' => $this->__('Settings', 'munewsmodule'),
                'title' => $this->__('Manage settings for this application', 'munewsmodule'),
                'icon' => 'wrench'
            ];
        }

        return $links;
    }

    /**
     * Returns the name of the providing bundle.
     *
     * @return string The bundle name
     */
    public function getBundleName()
    {
        return 'MUNewsModule';
    }
}