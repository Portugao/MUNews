<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Helper\Base;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use MU\NewsModule\Helper\CategoryHelper;
use MU\NewsModule\Helper\PermissionHelper;

/**
 * Entity collection filter helper base class.
 */
abstract class AbstractCollectionFilterHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;
    
    /**
     * @var bool Fallback value to determine whether only own entries should be selected or not
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * @var bool Whether to apply a locale-based filter or not
     */
    protected $filterDataByLocale = false;
    
    public function __construct(
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        CurrentUserApiInterface $currentUserApi,
        CategoryHelper $categoryHelper,
        VariableApiInterface $variableApi
    ) {
        $this->requestStack = $requestStack;
        $this->permissionHelper = $permissionHelper;
        $this->currentUserApi = $currentUserApi;
        $this->categoryHelper = $categoryHelper;
        $this->showOnlyOwnEntries = (bool)$variableApi->get('MUNewsModule', 'showOnlyOwnEntries');
        $this->filterDataByLocale = (bool)$variableApi->get('MUNewsModule', 'filterDataByLocale');
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $objectType Name of treated entity type
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    public function getViewQuickNavParameters($objectType = '', $context = '', array $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType'], true)) {
            $context = 'controllerAction';
        }
    
        if ('message' === $objectType) {
            return $this->getViewQuickNavParametersForMessage($context, $args);
        }
        if ('image' === $objectType) {
            return $this->getViewQuickNavParametersForImage($context, $args);
        }
    
        return [];
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param string $objectType Name of treated entity type
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCommonViewFilters($objectType, QueryBuilder $qb)
    {
        if ('message' === $objectType) {
            return $this->addCommonViewFiltersForMessage($qb);
        }
        if ('image' === $objectType) {
            return $this->addCommonViewFiltersForImage($qb);
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param string $objectType Name of treated entity type
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param array $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function applyDefaultFilters($objectType, QueryBuilder $qb, array $parameters = [])
    {
        if ('message' === $objectType) {
            return $this->applyDefaultFiltersForMessage($qb, $parameters);
        }
        if ('image' === $objectType) {
            return $this->applyDefaultFiltersForImage($qb, $parameters);
        }
    
        return $qb;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForMessage($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['catId'] = $request->query->get('catId', '');
        $parameters['catIdList'] = $this->categoryHelper->retrieveCategoriesFromRequest('message', 'GET');
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['approver'] = $request->query->getInt('approver', 0);
        $parameters['messageLanguage'] = $request->query->get('messageLanguage', '');
        $parameters['q'] = $request->query->get('q', '');
        $parameters['displayOnIndex'] = $request->query->get('displayOnIndex', '');
        $parameters['allowComments'] = $request->query->get('allowComments', '');
        $parameters['noEndDate'] = $request->query->get('noEndDate', '');
    
        return $parameters;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForImage($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['message'] = $request->query->get('message', 0);
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['q'] = $request->query->get('q', '');
    
        return $parameters;
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForMessage(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route', '');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForMessage();
        foreach ($parameters as $k => $v) {
            if (null === $v) {
                continue;
            }
            if ('catId' === $k) {
                if (0 < (int)$v) {
                    // single category filter
                    $qb->andWhere('tblCategories.category = :category')
                       ->setParameter('category', $v);
                }
                continue;
            }
            if ('catIdList' === $k) {
                // multi category filter
                $qb = $this->categoryHelper->buildFilterClauses($qb, 'message', $v);
                continue;
            }
            if (in_array($k, ['q', 'searchterm'], true)) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('message', $qb, $v);
                }
                continue;
            }
            if (in_array($k, ['displayOnIndex', 'allowComments', 'noEndDate'], true)) {
                // boolean filter
                if ('no' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 0');
                } elseif ('yes' === $v || '1' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 1');
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && '' !== $v) || (is_numeric($v) && 0 < $v)) {
                if ('workflowState' === $k && 0 === strpos($v, '!')) {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1));
                } elseif (0 === strpos($v, '%')) {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    if (in_array($k, ['approver'], true)) {
                        $qb->leftJoin('tbl.' . $k, 'tbl' . ucfirst($k))
                           ->andWhere('tbl' . ucfirst($k) . '.uid = :' . $k)
                           ->setParameter($k, $v);
                    } else {
                        $qb->andWhere('tbl.' . $k . ' = :' . $k)
                           ->setParameter($k, $v);
                    }
                }
            }
        }
    
        return $this->applyDefaultFiltersForMessage($qb, $parameters);
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForImage(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route', '');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForImage();
        foreach ($parameters as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (in_array($k, ['q', 'searchterm'], true)) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('image', $qb, $v);
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && '' !== $v) || (is_numeric($v) && 0 < $v)) {
                if ('workflowState' === $k && 0 === strpos($v, '!')) {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1));
                } elseif (0 === strpos($v, '%')) {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        return $this->applyDefaultFiltersForImage($qb, $parameters);
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param array $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForMessage(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        $routeName = $request->get('_route', '');
        $isAdminArea = false !== strpos($routeName, 'munewsmodule_message_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        if (!array_key_exists('workflowState', $parameters) || empty($parameters['workflowState'])) {
            // per default we show approved messages only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        if (true === (bool)$this->filterDataByLocale) {
            $allowedLocales = ['', $request->getLocale()];
            if (!array_key_exists('messageLanguage', $parameters) || empty($parameters['messageLanguage'])) {
                $qb->andWhere('tbl.messageLanguage IN (:currentMessageLanguage)')
                   ->setParameter('currentMessageLanguage', $allowedLocales);
            }
        }
    
        $qb = $this->applyDateRangeFilterForMessage($qb);
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param array $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForImage(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        $routeName = $request->get('_route', '');
        $isAdminArea = false !== strpos($routeName, 'munewsmodule_image_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        if (!array_key_exists('workflowState', $parameters) || empty($parameters['workflowState'])) {
            // per default we show approved images only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
        if (in_array('tblMessage', $qb->getAllAliases(), true)) {
            $qb = $this->applyDateRangeFilterForMessage($qb, 'tblMessage');
        }
    
        return $qb;
    }
    
    /**
     * Applies start and end date filters for selecting messages.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param string $alias Table alias
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDateRangeFilterForMessage(QueryBuilder $qb, $alias = 'tbl')
    {
        $request = $this->requestStack->getCurrentRequest();
        $startDate = $request->query->get('startDate', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.startDate <= :startDate OR ' . $alias . '.startDate IS NULL)')
           ->setParameter('startDate', $startDate);
    
        $endDate = $request->query->get('endDate', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.endDate >= :endDate OR ' . $alias . '.endDate IS NULL)')
           ->setParameter('endDate', $endDate);
    
        return $qb;
    }
    
    /**
     * Adds a where clause for search query.
     *
     * @param string $objectType Name of treated entity type
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param string $fragment The fragment to search for
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addSearchFilter($objectType, QueryBuilder $qb, $fragment = '')
    {
        if ('' === $fragment) {
            return $qb;
        }
    
        $filters = [];
        $parameters = [];
    
        if ('message' === $objectType) {
            $filters[] = 'tbl.workflowState = :searchWorkflowState';
            $parameters['searchWorkflowState'] = $fragment;
            $filters[] = 'tbl.title LIKE :searchTitle';
            $parameters['searchTitle'] = '%' . $fragment . '%';
            $filters[] = 'tbl.startText LIKE :searchStartText';
            $parameters['searchStartText'] = '%' . $fragment . '%';
            $filters[] = 'tbl.imageUpload1FileName = :searchImageUpload1';
            $parameters['searchImageUpload1'] = $fragment;
            $filters[] = 'tbl.mainText LIKE :searchMainText';
            $parameters['searchMainText'] = '%' . $fragment . '%';
            if (is_numeric($fragment)) {
                $filters[] = 'tbl.amountOfViews = :searchAmountOfViews';
                $parameters['searchAmountOfViews'] = $fragment;
            }
            $filters[] = 'tbl.author LIKE :searchAuthor';
            $parameters['searchAuthor'] = '%' . $fragment . '%';
            $filters[] = 'tbl.notes LIKE :searchNotes';
            $parameters['searchNotes'] = '%' . $fragment . '%';
            $filters[] = 'tbl.messageLanguage LIKE :searchMessageLanguage';
            $parameters['searchMessageLanguage'] = '%' . $fragment . '%';
            $filters[] = 'tbl.imageUpload2FileName = :searchImageUpload2';
            $parameters['searchImageUpload2'] = $fragment;
            $filters[] = 'tbl.imageUpload3FileName = :searchImageUpload3';
            $parameters['searchImageUpload3'] = $fragment;
            $filters[] = 'tbl.imageUpload4FileName = :searchImageUpload4';
            $parameters['searchImageUpload4'] = $fragment;
            $filters[] = 'tbl.startDate = :searchStartDate';
            $parameters['searchStartDate'] = $fragment;
            $filters[] = 'tbl.endDate = :searchEndDate';
            $parameters['searchEndDate'] = $fragment;
            if (is_numeric($fragment)) {
                $filters[] = 'tbl.weight = :searchWeight';
                $parameters['searchWeight'] = $fragment;
            }
        }
        if ('image' === $objectType) {
            $filters[] = 'tbl.theFileFileName = :searchTheFile';
            $parameters['searchTheFile'] = $fragment;
            $filters[] = 'tbl.caption LIKE :searchCaption';
            $parameters['searchCaption'] = '%' . $fragment . '%';
            if (is_numeric($fragment)) {
                $filters[] = 'tbl.sortNumber = :searchSortNumber';
                $parameters['searchSortNumber'] = $fragment;
            }
        }
    
        $qb->andWhere('(' . implode(' OR ', $filters) . ')');
    
        foreach ($parameters as $parameterName => $parameterValue) {
            $qb->setParameter($parameterName, $parameterValue);
        }
    
        return $qb;
    }
    
    /**
     * Adds a filter for the createdBy field.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param int $userId The user identifier used for filtering
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCreatorFilter(QueryBuilder $qb, $userId = null)
    {
        if (null === $userId) {
            $userId = $this->currentUserApi->isLoggedIn() ? (int)$this->currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        }
    
        $qb->andWhere('tbl.createdBy = :userId')
           ->setParameter('userId', $userId);
    
        return $qb;
    }
}
