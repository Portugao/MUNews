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

namespace MU\NewsModule\Needle\Base;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\ExtensionsModule\ModuleInterface\MultiHook\NeedleInterface;
use MU\NewsModule\Entity\Factory\EntityFactory;
use MU\NewsModule\Helper\EntityDisplayHelper;
use MU\NewsModule\Helper\PermissionHelper;

/**
 * MessageNeedle base class.
 */
abstract class AbstractMessageNeedle implements NeedleInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var RouterInterface
     */
    protected $router;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * Bundle name.
     *
     * @var string
     */
    protected $bundleName;
    
    /**
     * The name of this needle.
     *
     * @var string
     */
    protected $name;
    
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->permissionHelper = $permissionHelper;
        $this->entityFactory = $entityFactory;
        $this->entityDisplayHelper = $entityDisplayHelper;
    
        $nsParts = explode('\\', static::class);
        $vendor = $nsParts[0];
        $nameAndType = $nsParts[1];
    
        $this->bundleName = $vendor . $nameAndType;
        $this->name = str_replace('Needle', '', array_pop($nsParts));
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getIcon(): string
    {
        return 'circle-o';
    }
    
    public function getTitle(): string
    {
        return $this->translator->trans('Messages', [], 'message');
    }
    
    public function getDescription(): string
    {
        return $this->translator->trans('Links to the list of messages and specific messages.', [], 'message');
    }
    
    public function getUsageInfo(): string
    {
        return 'NEWS{MESSAGES|MESSAGE-messageId}';
    }
    
    public function isActive(): bool
    {
        return true;
    }
    
    public function isCaseSensitive(): bool
    {
        return true;
    }
    
    public function getSubjects(): array
    {
        return ['NEWSMESSAGES', 'NEWSMESSAGE-'];
    }
    
    /**
     * Applies the needle functionality.
     */
    public function apply(string $needleId, string $needleText): string
    {
        // cache the results
        static $cache;
        if (!isset($cache)) {
            $cache = [];
        }
    
        if (isset($cache[$needleId])) {
            // needle is already in cache array
            return $cache[$needleId];
        }
    
        // strip application prefix from needle
        $needleText = str_replace('NEWS', '', $needleText);
    
        if ('MESSAGES' === $needleText) {
            if (!$this->permissionHelper->hasComponentPermission('message', ACCESS_READ)) {
                $cache[$needleId] = '';
            } else {
                $route = $this->router->generate(
                    'munewsmodule_message_view',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                $linkTitle = $this->translator->trans('View messages', [], 'message');
                $linkText = $this->translator->trans('Messages', [], 'message');
                $cache[$needleId] = '<a href="' . $route . '" title="' . $linkTitle . '">' . $linkText . '</a>';
            }
    
            return $cache[$needleId];
        }
    
        $entityId = (int) $needleId;
        if (!$entityId) {
            $cache[$needleId] = '';
    
            return $cache[$needleId];
        }
    
        $repository = $this->entityFactory->getRepository('message');
        $entity = $repository->selectById($entityId, false);
        if (null === $entity) {
            $notFoundMessage = $this->translator->trans(
                'Message with id %id% could not be found',
                ['%id%' => $entityId],
                'message'
            );
            $cache[$needleId] = '<em>' . $notFoundMessage . '</em>';
    
            return $cache[$needleId];
        }
    
        if (!$this->permissionHelper->mayRead($entity)) {
            $cache[$needleId] = '';
    
            return $cache[$needleId];
        }
    
        $title = $this->entityDisplayHelper->getFormattedTitle($entity);
        $route = $this->router->generate(
            'munewsmodule_message_display',
            $entity->createUrlArgs(),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $cache[$needleId] = '<a href="' . $route . '" title="' . str_replace('"', '', $title) . '">' . $title . '</a>';
    
        return $cache[$needleId];
    }
    
    public function getBundleName(): string
    {
        return $this->bundleName;
    }
}
