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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Collection\Collectible\PendingContentCollectible;
use Zikula\Common\Collection\Container;
use Zikula\Core\Event\GenericEvent;
use MU\NewsModule\Helper\WorkflowHelper;
use Zikula\ScribiteModule\Event\EditorHelperEvent;

/**
 * Event handler implementation class for special purposes and 3rd party api support.
 */
abstract class AbstractThirdPartyListener implements EventSubscriberInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    public function __construct(Filesystem $filesystem, RequestStack $requestStack, WorkflowHelper $workflowHelper)
    {
        $this->filesystem = $filesystem;
        $this->requestStack = $requestStack;
        $this->workflowHelper = $workflowHelper;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            'get.pending_content'                     => ['pendingContentListener', 5],
            'module.scribite.editorhelpers'           => ['getEditorHelpers', 5],
            'moduleplugin.ckeditor.externalplugins'   => ['getCKEditorPlugins', 5],
            'moduleplugin.quill.externalplugins'      => ['getQuillPlugins', 5],
            'moduleplugin.summernote.externalplugins' => ['getSummernotePlugins', 5],
            'moduleplugin.tinymce.externalplugins'    => ['getTinyMcePlugins', 5]
        ];
    }
    
    /**
     * Listener for the `get.pending_content` event which collects information from modules
     * about pending content items waiting for approval.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function pendingContentListener(GenericEvent $event)
    {
        $collection = new Container('MUNewsModule');
        $amounts = $this->workflowHelper->collectAmountOfModerationItems();
        if (0 < count($amounts)) {
            foreach ($amounts as $amountInfo) {
                $aggregateType = $amountInfo['aggregateType'];
                $description = $amountInfo['description'];
                $amount = $amountInfo['amount'];
                $route = 'munewsmodule_' . strtolower($amountInfo['objectType']) . '_adminview';
                $routeArgs = [
                    'workflowState' => $amountInfo['state']
                ];
                $item = new PendingContentCollectible($aggregateType, $description, $amount, $route, $routeArgs);
                $collection->add($item);
            }
        
            // add collected items for pending content
            if (0 < $collection->count()) {
                $event->getSubject()->add($collection);
            }
        }
    }
    
    /**
     * Listener for the `module.scribite.editorhelpers` event.
     *
     * This occurs when Scribite adds pagevars to the editor page.
     * MUNewsModule will use this to add a javascript helper to add custom items.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function getEditorHelpers(EditorHelperEvent $event)
    {
        // install assets for Scribite plugins
        $targetDir = 'web/modules/munews';
        if (!$this->filesystem->exists($targetDir)) {
            $moduleDirectory = str_replace('Listener/Base', '', __DIR__);
            if (is_dir($originDir = $moduleDirectory . 'Resources/public')) {
                $this->filesystem->symlink($originDir, $targetDir, true);
            }
        }
    
        $event->getHelperCollection()->add(
            [
                'module' => 'MUNewsModule',
                'type' => 'javascript',
                'path' => $this->getPathToModuleWebAssets() . 'js/MUNewsModule.Finder.js'
            ]
        );
    }
    
    /**
     * Listener for the `moduleplugin.ckeditor.externalplugins` event.
     *
     * Adds external plugin to CKEditor.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function getCKEditorPlugins(GenericEvent $event)
    {
        $event->getSubject()->add([
            'name' => 'munewsmodule',
            'path' => $this->getPathToModuleWebAssets() . 'scribite/CKEditor/munewsmodule/',
            'file' => 'plugin.js',
            'img' => 'ed_munewsmodule.gif'
        ]);
    }
    
    /**
     * Listener for the `moduleplugin.quill.externalplugins` event.
     *
     * Adds external plugin to Quill.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function getQuillPlugins(GenericEvent $event)
    {
        $event->getSubject()->add([
            'name' => 'munewsmodule',
            'path' => $this->getPathToModuleWebAssets() . 'scribite/Quill/munewsmodule/plugin.js'
        ]);
    }
    
    /**
     * Listener for the `moduleplugin.summernote.externalplugins` event.
     *
     * Adds external plugin to Summernote.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function getSummernotePlugins(GenericEvent $event)
    {
        $event->getSubject()->add([
            'name' => 'munewsmodule',
            'path' => $this->getPathToModuleWebAssets() . 'scribite/Summernote/munewsmodule/plugin.js'
        ]);
    }
    
    /**
     * Listener for the `moduleplugin.tinymce.externalplugins` event.
     *
     * Adds external plugin to TinyMce.
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function getTinyMcePlugins(GenericEvent $event)
    {
        $event->getSubject()->add([
            'name' => 'munewsmodule',
            'path' => $this->getPathToModuleWebAssets() . 'scribite/TinyMce/munewsmodule/plugin.js'
        ]);
    }
    
    /**
     * Returns base path where module assets are located.
     *
     * @return string
     */
    protected function getPathToModuleWebAssets()
    {
        return $this->requestStack->getCurrentRequest()->getBasePath() . '/web/modules/munews/';
    }
}