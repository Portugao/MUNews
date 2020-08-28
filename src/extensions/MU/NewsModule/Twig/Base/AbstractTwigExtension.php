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

namespace MU\NewsModule\Twig\Base;

use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\Bundle\CoreBundle\Translation\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use MU\NewsModule\Helper\EntityDisplayHelper;
use MU\NewsModule\Helper\ListEntriesHelper;
use MU\NewsModule\Helper\WorkflowHelper;

/**
 * Twig extension base class.
 */
abstract class AbstractTwigExtension extends AbstractExtension
{
    use TranslatorTrait;
    
    /**
     * @var ZikulaHttpKernelInterface
     */
    protected $kernel;
    
    /**
     * @var Connection
     */
    protected $databaseConnection;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;
    
    public function __construct(
        ZikulaHttpKernelInterface $kernel,
        TranslatorInterface $translator,
        Connection $connection,
        RequestStack $requestStack,
        VariableApiInterface $variableApi,
        EntityDisplayHelper $entityDisplayHelper,
        WorkflowHelper $workflowHelper,
        ListEntriesHelper $listHelper
    ) {
        $this->kernel = $kernel;
        $this->setTranslator($translator);
        $this->databaseConnection = $connection;
        $this->requestStack = $requestStack;
        $this->variableApi = $variableApi;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->workflowHelper = $workflowHelper;
        $this->listHelper = $listHelper;
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('munewsmodule_moderationObjects', [$this, 'getModerationObjects']),
            new TwigFunction('munewsmodule_increaseCounter', [$this, 'increaseCounter']),
            new TwigFunction('munewsmodule_objectTypeSelector', [$this, 'getObjectTypeSelector']),
            new TwigFunction('munewsmodule_templateSelector', [$this, 'getTemplateSelector']),
        ];
    }
    
    public function getFilters()
    {
        return [
            new TwigFilter('munewsmodule_fileSize', [$this, 'getFileSize'], ['is_safe' => ['html']]),
            new TwigFilter('munewsmodule_relativePath', [$this, 'getRelativePath']),
            new TwigFilter('munewsmodule_listEntry', [$this, 'getListEntry']),
            new TwigFilter('munewsmodule_icalText', [$this, 'formatIcalText']),
            new TwigFilter('munewsmodule_formattedTitle', [$this, 'getFormattedEntityTitle']),
            new TwigFilter('munewsmodule_objectState', [$this, 'getObjectState'], ['is_safe' => ['html']]),
        ];
    }
    
    /**
     * The munewsmodule_objectState filter displays the name of a given object's workflow state.
     * Examples:
     *    {{ item.workflowState|munewsmodule_objectState }}        {# with visual feedback #}
     *    {{ item.workflowState|munewsmodule_objectState(false) }} {# no ui feedback #}.
     */
    public function getObjectState(string $state = 'initial', bool $uiFeedback = true): string
    {
        $stateInfo = $this->workflowHelper->getStateInfo($state);
    
        $result = $stateInfo['text'];
        if (true === $uiFeedback) {
            $result = '<span class="badge badge-' . $stateInfo['ui'] . '">' . $result . '</span>';
        }
    
        return $result;
    }
    
    
    /**
     * The munewsmodule_fileSize filter displays the size of a given file in a readable way.
     * Example:
     *     {{ 12345|munewsmodule_fileSize }}.
     */
    public function getFileSize(int $size = 0, string $filepath = '', bool $nodesc = false, bool $onlydesc = false): string
    {
        if (!$size) {
            if (empty($filepath) || !file_exists($filepath)) {
                return '';
            }
            $size = filesize($filepath);
        }
        if (!$size) {
            return '';
        }
    
        return $this->getReadableFileSize($size, $nodesc, $onlydesc);
    }
    
    /**
     * Display a given file size in a readable format
     */
    private function getReadableFileSize(int $size, bool $nodesc = false, bool $onlydesc = false): string
    {
        $sizeDesc = $this->trans('Bytes');
        if ($size >= 1024) {
            $size /= 1024;
            $sizeDesc = $this->trans('KB');
        }
        if ($size >= 1024) {
            $size /= 1024;
            $sizeDesc = $this->trans('MB');
        }
        if ($size >= 1024) {
            $size /= 1024;
            $sizeDesc = $this->trans('GB');
        }
        $sizeDesc = '&nbsp;' . $sizeDesc;
    
        // format number
        $dec_point = ',';
        $thousands_separator = '.';
        if ($size - (int) $size >= 0.005) {
            $size = number_format($size, 2, $dec_point, $thousands_separator);
        } else {
            $size = number_format($size, 0, '', $thousands_separator);
        }
    
        // append size descriptor if desired
        if (!$nodesc) {
            $size .= $sizeDesc;
        }
    
        // return either only the description or the complete string
        return $onlydesc ? $sizeDesc : $size;
    }
    
    
    /**
     * The munewsmodule_listEntry filter displays the name
     * or names for a given list item.
     * Example:
     *     {{ entity.listField|munewsmodule_listEntry('entityName', 'fieldName') }}.
     */
    public function getListEntry(
        string $value,
        string $objectType = '',
        string $fieldName = '',
        string $delimiter = ', '
    ): string {
        if ((empty($value) && '0' !== $value) || empty($objectType) || empty($fieldName)) {
            return $value;
        }
    
        return $this->listHelper->resolve($value, $objectType, $fieldName, $delimiter);
    }
    
    
    /**
     * The munewsmodule_moderationObjects function determines the amount of unapproved objects.
     * It uses the same logic as the moderation block and the pending content listener.
     */
    public function getModerationObjects(): array
    {
        return $this->workflowHelper->collectAmountOfModerationItems();
    }
    
    
    /**
     * The munewsmodule_increaseCounter function increases a counter field of a specific entity.
     * It uses Doctrine DBAL to avoid creating a new loggable version, sending workflow notification or executing other unwanted actions.
     * Example:
     *     {{ munewsmodule_increaseCounter(message, 'amountOfViews') }}.
     */
    public function increaseCounter(EntityAccess $entity, string $fieldName = ''): void
    {
        $entityId = $entity->getId();
        $objectType = $entity->get_objectType();
    
        // check against session to see if user was already counted
        $request = $this->requestStack->getCurrentRequest();
        $doCount = true;
        if (null !== $request && $request->hasSession() && $session = $request->getSession()) {
            if ($session->has('MUNewsModuleRead' . $objectType . $entityId)) {
                $doCount = false;
            } else {
                $session->set('MUNewsModuleRead' . $objectType . $entityId, 1);
            }
        }
        if (!$doCount) {
            return;
        }
    
        $counterValue = $entity[$fieldName] + 1;
    
        $this->databaseConnection->update(
            'mu_news_' . mb_strtolower($objectType),
            [$fieldName => $counterValue],
            ['id' => $entityId]
        );
    }
    
    
    /**
     * The munewsmodule_icalText filter outputs a given text for the ics output format.
     * Example:
     *     {{ 'someString'|munewsmodule_icalText }}.
     */
    public function formatIcalText(string $string): string
    {
        $result = preg_replace('/<a href="(.*)">.*<\/a>/i', '$1', $string);
        $result = str_replace('€', 'Euro', $result);
        $result = ereg_replace("(\r\n|\n|\r)", '=0D=0A', $result);
    
        return ';LANGUAGE=' . $this->requestStack->getCurrentRequest()->getLocale() . ';ENCODING=QUOTED-PRINTABLE:' . $result . "\r\n";
    }
    
    
    
    
    
    /**
     * The munewsmodule__relativePath filter returns the relative web path to a file.
     * Example:
     *     {{ myPerson.image.getPathname()|munewsmodule_relativePath }}
     */
    public function getRelativePath(string $absolutePath): string
    {
        return str_replace($this->kernel->getProjectDir() . '/public', '', $absolutePath);
    }
    
    /**
     * The munewsmodule_formattedTitle filter outputs a formatted title for a given entity.
     * Example:
     *     {{ myPost|munewsmodule_formattedTitle }}.
     */
    public function getFormattedEntityTitle(EntityAccess $entity): string
    {
        return $this->entityDisplayHelper->getFormattedTitle($entity);
    }
}
