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

namespace MU\NewsModule\Block\Base;

use Exception;
use Zikula\BlocksModule\AbstractBlockHandler;
use MU\NewsModule\Block\Form\Type\ItemListBlockType;
use MU\NewsModule\Helper\FeatureActivationHelper;

/**
 * Generic item list block base class.
 */
abstract class AbstractItemListBlock extends AbstractBlockHandler
{
    /**
     * List of object types allowing categorisation.
     *
     * @var array
     */
    protected $categorisableObjectTypes;
    
    public function getType()
    {
        return $this->__('News list', 'munewsmodule');
    }
    
    public function display(array $properties = [])
    {
        // only show block content if the user has the required permissions
        if (!$this->hasPermission('MUNewsModule:ItemListBlock:', $properties['title'] . '::', ACCESS_OVERVIEW)) {
            return '';
        }
        
        $this->categorisableObjectTypes = ['message'];
        
        // set default values for all params which are not properly set
        $defaults = $this->getDefaults();
        $properties = array_merge($defaults, $properties);
        
        $controllerHelper = $this->get('mu_news_module.controller_helper');
        $contextArgs = ['name' => 'list'];
        if (
            !isset($properties['objectType'])
            || !in_array($properties['objectType'], $controllerHelper->getObjectTypes('block', $contextArgs), true)
        ) {
            $properties['objectType'] = $controllerHelper->getDefaultObjectType('block', $contextArgs);
        }
        
        $objectType = $properties['objectType'];
        
        $featureActivationHelper = $this->get('mu_news_module.feature_activation_helper');
        $hasCategories = in_array($objectType, $this->categorisableObjectTypes, true)
            && $featureActivationHelper->isEnabled(
                FeatureActivationHelper::CATEGORIES,
                $properties['objectType']
            )
        ;
        if ($hasCategories) {
            $categoryProperties = $this->resolveCategoryIds($properties);
        }
        
        $repository = $this->get('mu_news_module.entity_factory')->getRepository($objectType);
        
        // create query
        $orderBy = $this->get('mu_news_module.model_helper')->resolveSortParameter($objectType, $properties['sorting']);
        $qb = $repository->getListQueryBuilder($properties['filter'], $orderBy);
        
        if ($hasCategories) {
            $categoryHelper = $this->get('mu_news_module.category_helper');
            // apply category filters
            if (is_array($properties['categories']) && count($properties['categories']) > 0) {
                $qb = $categoryHelper->buildFilterClauses($qb, $objectType, $properties['categories']);
            }
        }
        
        // get objects from database
        $currentPage = 1;
        $resultsPerPage = $properties['amount'];
        $query = $repository->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
        try {
            list($entities, $objectCount) = $repository->retrieveCollectionResult($query, true);
        } catch (Exception $exception) {
            $entities = [];
            $objectCount = 0;
        }
        
        // filter by permissions
        $entities = $this->get('mu_news_module.permission_helper')->filterCollection($objectType, $entities, ACCESS_READ);
        
        // set a block title
        if (empty($properties['title'])) {
            $properties['title'] = $this->__('News list', 'munewsmodule');
        }
        
        $template = $this->getDisplayTemplate($properties);
        
        $templateParameters = [
            'vars' => $properties,
            'objectType' => $objectType,
            'items' => $entities
        ];
        if ($hasCategories) {
            $templateParameters['properties'] = $categoryProperties;
        }
        
        $templateParameters = $this->get('mu_news_module.controller_helper')->addTemplateParameters(
            $properties['objectType'],
            $templateParameters,
            'block'
        );
        
        return $this->renderView($template, $templateParameters);
    }
    
    /**
     * Returns the template used for output.
     *
     * @param array $properties The block properties
     *
     * @return string the template path
     */
    protected function getDisplayTemplate(array $properties = [])
    {
        $templateFile = $properties['template'];
        if (
            'custom' === $templateFile
            && null !== $properties['customTemplate']
            && '' !== $properties['customTemplate']
        ) {
            $templateFile = $properties['customTemplate'];
        }
    
        $templateForObjectType = str_replace('itemlist_', 'itemlist_' . $properties['objectType'] . '_', $templateFile);
        $templating = $this->get('templating');
    
        $templateOptions = [
            'Block/' . $templateForObjectType,
            'Block/' . $templateFile,
            'Block/itemlist.html.twig'
        ];
    
        $template = '';
        foreach ($templateOptions as $templatePath) {
            if ($templating->exists('@MUNewsModule/' . $templatePath)) {
                $template = '@MUNewsModule/' . $templatePath;
                break;
            }
        }
    
        return $template;
    }
    
    public function getFormClassName()
    {
        return ItemListBlockType::class;
    }
    
    public function getFormOptions()
    {
        $objectType = 'message';
        $this->categorisableObjectTypes = ['message'];
    
        $request = $this->get('request_stack')->getCurrentRequest();
        if (null !== $request && $request->attributes->has('blockEntity')) {
            $blockEntity = $request->attributes->get('blockEntity');
            if (is_object($blockEntity) && method_exists($blockEntity, 'getProperties')) {
                $blockProperties = $blockEntity->getProperties();
                if (isset($blockProperties['objectType'])) {
                    $objectType = $blockProperties['objectType'];
                } else {
                    // set default options for new block creation
                    $blockEntity->setProperties($this->getDefaults());
                }
            }
        }
    
        return [
            'object_type' => $objectType,
            'is_categorisable' => in_array($objectType, $this->categorisableObjectTypes, true),
            'category_helper' => $this->get('mu_news_module.category_helper'),
            'feature_activation_helper' => $this->get('mu_news_module.feature_activation_helper')
        ];
    }
    
    public function getFormTemplate()
    {
        return '@MUNewsModule/Block/itemlist_modify.html.twig';
    }
    
    /**
     * Returns default settings for this block.
     *
     * @return array The default settings
     */
    protected function getDefaults()
    {
        return [
            'objectType' => 'message',
            'sorting' => 'default',
            'amount' => 5,
            'template' => 'itemlist_display.html.twig',
            'customTemplate' => null,
            'filter' => ''
        ];
    }
    
    /**
     * Resolves category filter ids.
     *
     * @param array $properties The block properties
     *
     * @return array The updated block properties
     */
    protected function resolveCategoryIds(array $properties = [])
    {
        $categoryHelper = $this->get('mu_news_module.category_helper');
        $primaryRegistry = $categoryHelper->getPrimaryProperty($properties['objectType']);
        if (!isset($properties['categories'])) {
            $properties['categories'] = [$primaryRegistry => []];
        } else {
            if (!is_array($properties['categories'])) {
                $properties['categories'] = explode(',', $properties['categories']);
            }
            if (count($properties['categories']) > 0) {
                $firstCategories = reset($properties['categories']);
                if (!is_array($firstCategories)) {
                    $firstCategories = [$firstCategories];
                }
                $properties['categories'] = [$primaryRegistry => $firstCategories];
            }
        }
    
        return $properties;
    }
}
