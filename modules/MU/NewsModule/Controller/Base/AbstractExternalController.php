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

namespace MU\NewsModule\Controller\Base;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Core\Controller\AbstractController;

/**
 * Controller for external calls base class.
 */
abstract class AbstractExternalController extends AbstractController
{
    /**
     * Displays one item of a certain object type using a separate template for external usages.
     *
     * @param Request $request
     * @param string $objectType The currently treated object type
     * @param int $id Identifier of the entity to be shown
     * @param string $source Source of this call (block, contentType, scribite)
     * @param string $displayMode Display mode (link or embed)
     *
     * @return Response
     */
    public function displayAction(
        Request $request,
        $objectType,
        $id,
        $source,
        $displayMode
    ) {
        $controllerHelper = $this->get('mu_news_module.controller_helper');
        $contextArgs = ['controller' => 'external', 'action' => 'display'];
        if (!in_array($objectType, $controllerHelper->getObjectTypes('controllerAction', $contextArgs), true)) {
            $objectType = $controllerHelper->getDefaultObjectType('controllerAction', $contextArgs);
        }
        
        $entityFactory = $this->get('mu_news_module.entity_factory');
        $repository = $entityFactory->getRepository($objectType);
        
        // assign object data fetched from the database
        $entity = $repository->selectById($id);
        if (null === $entity) {
            return new Response($this->__('No such item.'));
        }
        
        if (!$this->get('mu_news_module.permission_helper')->mayRead($entity)) {
            return new Response('');
        }
        
        $template = $request->query->get('template');
        if (null === $template || '' === $template) {
            $template = 'display.html.twig';
        }
        
        $templateParameters = [
            'objectType' => $objectType,
            'source' => $source,
            $objectType => $entity,
            'displayMode' => $displayMode
        ];
        
        $contextArgs = ['controller' => 'external', 'action' => 'display'];
        $templateParameters = $this->get('mu_news_module.controller_helper')->addTemplateParameters(
            $objectType,
            $templateParameters,
            'controllerAction',
            $contextArgs
        );
        
        $viewHelper = $this->get('mu_news_module.view_helper');
        $request->query->set('raw', true);
        
        return $viewHelper->processTemplate(
            'external',
            ucfirst($objectType) . '/' . str_replace('.html.twig', '', $template),
            $templateParameters
        );
    }
    
    /**
     * Popup selector for Scribite plugins.
     * Finds items of a certain object type.
     *
     * @param Request $request
     * @param string $objectType The object type
     * @param string $editor Name of used Scribite editor
     * @param string $sort Sorting field
     * @param string $sortdir Sorting direction
     * @param int $pos Current pager position
     * @param int $num Amount of entries to display
     *
     * @return Response
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function finderAction(
        Request $request,
        $objectType,
        $editor,
        $sort,
        $sortdir,
        $pos = 1,
        $num = 0
    ) {
        $listEntriesHelper = $this->get('mu_news_module.listentries_helper');
        $activatedObjectTypes = $listEntriesHelper->extractMultiList($this->getVar('enabledFinderTypes', ''));
        if (!in_array($objectType, $activatedObjectTypes, true)) {
            if (!count($activatedObjectTypes)) {
                throw new AccessDeniedException();
            }
        
            // redirect to first valid object type
            $redirectUrl = $this->get('router')->generate(
                'munewsmodule_external_finder',
                ['objectType' => array_shift($activatedObjectTypes), 'editor' => $editor]
            );
        
            return new RedirectResponse($redirectUrl);
        }
        
        $formData = $request->query->get('munewsmodule_' . strtolower($objectType) . 'finder', []);
        if (isset($formData['language'])) {
            $this->get('stof_doctrine_extensions.listener.translatable')->setTranslatableLocale($formData['language']);
        }
        
        if (!$this->get('mu_news_module.permission_helper')->hasComponentPermission($objectType, ACCESS_COMMENT)) {
            throw new AccessDeniedException();
        }
        
        if (empty($editor) || !in_array($editor, ['ckeditor', 'quill', 'summernote', 'tinymce'], true)) {
            return new Response($this->__('Error: Invalid editor context given for external controller action.'));
        }
        
        $assetHelper = $this->get('zikula_core.common.theme.asset_helper');
        $cssAssetBag = $this->get('zikula_core.common.theme.assets_css');
        $cssAssetBag->add($assetHelper->resolve('@MUNewsModule:css/style.css'));
        $cssAssetBag->add([$assetHelper->resolve('@MUNewsModule:css/custom.css') => 120]);
        
        $repository = $this->get('mu_news_module.entity_factory')->getRepository($objectType);
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields(), true)) {
            $sort = $repository->getDefaultSortingField();
        }
        
        $sdir = strtolower($sortdir);
        if ('asc' !== $sdir && 'desc' !== $sdir) {
            $sdir = 'asc';
        }
        
        // the current offset which is used to calculate the pagination
        $currentPage = (int)$pos;
        
        // the number of items displayed on a page for pagination
        $resultsPerPage = (int)$num;
        if (0 === $resultsPerPage) {
            $resultsPerPage = $this->getVar($objectType . 'EntriesPerPage', 20);
        }
        
        $templateParameters = [
            'editorName' => $editor,
            'objectType' => $objectType,
            'sort' => $sort,
            'sortdir' => $sdir,
            'currentPage' => $currentPage,
            'language' => isset($formData['language']) ? $formData['language'] : $request->getLocale(),
            'onlyImages' => false,
            'imageField' => ''
        ];
        $searchTerm = '';
        
        $formOptions = [
            'object_type' => $objectType,
            'editor_name' => $editor
        ];
        $form = $this->createForm(
            'MU\NewsModule\Form\Type\Finder\\' . ucfirst($objectType) . 'FinderType',
            $templateParameters,
            $formOptions
        );
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $templateParameters = array_merge($templateParameters, $formData);
            $currentPage = $formData['currentPage'];
            $resultsPerPage = $formData['num'];
            $sort = $formData['sort'];
            $sdir = $formData['sortdir'];
            $searchTerm = $formData['q'];
            $templateParameters['onlyImages'] = isset($formData['onlyImages']) ? (bool)$formData['onlyImages'] : false;
            $templateParameters['imageField'] = isset($formData['imageField']) ? $formData['imageField'] : '';
        }
        
        $where = '';
        $orderBy = $sort . ' ' . $sdir;
        
        $qb = $repository->getListQueryBuilder($where, $orderBy);
        
        if (true === $templateParameters['onlyImages'] && '' !== $templateParameters['imageField']) {
            $imageField = $templateParameters['imageField'];
            $orX = $qb->expr()->orX();
            foreach (['gif', 'jpg', 'jpeg', 'jpe', 'png', 'bmp'] as $imageExtension) {
                $orX->add($qb->expr()->like('tbl.' . $imageField . 'FileName', $qb->expr()->literal('%.' . $imageExtension)));
            }
        
            $qb->andWhere($orX);
        }
        
        if ('' !== $searchTerm) {
            $qb = $this->get('mu_news_module.collection_filter_helper')->addSearchFilter($objectType, $qb, $searchTerm);
        }
        
        $query = $repository->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
        list($entities, $objectCount) = $repository->retrieveCollectionResult($query, true);
        
        // filter by permissions
        $entities = $this->get('mu_news_module.permission_helper')->filterCollection($objectType, $entities, ACCESS_READ);
        
        $templateParameters['items'] = $entities;
        $templateParameters['finderForm'] = $form->createView();
        
        $contextArgs = ['controller' => 'external', 'action' => 'display'];
        $templateParameters = $this->get('mu_news_module.controller_helper')->addTemplateParameters(
            $objectType,
            $templateParameters,
            'controllerAction',
            $contextArgs
        );
        
        $templateParameters['activatedObjectTypes'] = $activatedObjectTypes;
        
        $templateParameters['pager'] = [
            'numitems' => $objectCount,
            'itemsperpage' => $resultsPerPage
        ];
        
        $viewHelper = $this->get('mu_news_module.view_helper');
        $request->query->set('raw', true);
        
        return $viewHelper->processTemplate('external', ucfirst($objectType) . '/find', $templateParameters);
    }
}
