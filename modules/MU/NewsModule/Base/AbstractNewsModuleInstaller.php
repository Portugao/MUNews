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

namespace MU\NewsModule\Base;

use Exception;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula\CategoriesModule\Entity\CategoryRegistryEntity;
use MU\NewsModule\Entity\MessageEntity;
use MU\NewsModule\Entity\MessageTranslationEntity;
use MU\NewsModule\Entity\MessageAttributeEntity;
use MU\NewsModule\Entity\MessageCategoryEntity;
use MU\NewsModule\Entity\ImageEntity;

/**
 * Installer base class.
 */
abstract class AbstractNewsModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * @var string[]
     */
    protected $entities = [
        MessageEntity::class,
        MessageTranslationEntity::class,
        MessageAttributeEntity::class,
        MessageCategoryEntity::class,
        ImageEntity::class,
    ];

    public function install()
    {
        $logger = $this->container->get('logger');
        $userName = $this->container->get('zikula_users_module.current_user')->get('uname');
    
        // Check if upload directories exist and if needed create them
        try {
            $container = $this->container;
            $uploadHelper = new \MU\NewsModule\Helper\UploadHelper(
                $container->get('translator.default'),
                $container->get('filesystem'),
                $container->get('request_stack'),
                $container->get('logger'),
                $container->get('zikula_users_module.current_user'),
                $container->get('zikula_extensions_module.api.variable'),
                $container->getParameter('datadir')
            );
            $uploadHelper->checkAndCreateAllUploadFolders();
        } catch (Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            $logger->error('{app}: User {user} could not create upload folders during installation. Error details: {errorMessage}.', ['app' => 'MUNewsModule', 'user' => $userName, 'errorMessage' => $exception->getMessage()]);
        
            return false;
        }
        // create all tables from according entity definitions
        try {
            $this->schemaTool->create($this->entities);
        } catch (Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'MUNewsModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // set up all our vars with initial values
        $this->setVar('enableAttribution', false);
        $this->setVar('enableMultiLanguage', false);
        $this->setVar('showAuthor', false);
        $this->setVar('showDate', false);
        $this->setVar('enableCategorization', false);
        $this->setVar('defaultMessageSorting', 'articledatetime');
        $this->setVar('defaultMessageSortingBackend', 'articledatetime');
        $this->setVar('sortingDirection', 'descending');
        $this->setVar('enableMoreMessagesInCategory', false);
        $this->setVar('amountOfMoreArticlesInCategory', 0);
        $this->setVar('displayPdfLink', false);
        $this->setVar('enablePictureUpload', false);
        $this->setVar('imageFloatOnViewPage', 'left');
        $this->setVar('imageFloatOnDisplayPage', 'left');
        $this->setVar('maxSize', '200k');
        $this->setVar('messageEntriesPerPage', 10);
        $this->setVar('linkOwnMessagesOnAccountPage', true);
        $this->setVar('showOnlyOwnEntries', false);
        $this->setVar('filterDataByLocale', false);
        $this->setVar('enableShrinkingForMessageImageUpload1', false);
        $this->setVar('shrinkWidthMessageImageUpload1', 800);
        $this->setVar('shrinkHeightMessageImageUpload1', 600);
        $this->setVar('thumbnailModeMessageImageUpload1', 'inset');
        $this->setVar('thumbnailWidthMessageImageUpload1View', 32);
        $this->setVar('thumbnailHeightMessageImageUpload1View', 24);
        $this->setVar('thumbnailWidthMessageImageUpload1Display', 240);
        $this->setVar('thumbnailHeightMessageImageUpload1Display', 180);
        $this->setVar('thumbnailWidthMessageImageUpload1Edit', 240);
        $this->setVar('thumbnailHeightMessageImageUpload1Edit', 180);
        $this->setVar('enableShrinkingForMessageImageUpload2', false);
        $this->setVar('shrinkWidthMessageImageUpload2', 800);
        $this->setVar('shrinkHeightMessageImageUpload2', 600);
        $this->setVar('thumbnailModeMessageImageUpload2', 'inset');
        $this->setVar('thumbnailWidthMessageImageUpload2View', 32);
        $this->setVar('thumbnailHeightMessageImageUpload2View', 24);
        $this->setVar('thumbnailWidthMessageImageUpload2Display', 240);
        $this->setVar('thumbnailHeightMessageImageUpload2Display', 180);
        $this->setVar('thumbnailWidthMessageImageUpload2Edit', 240);
        $this->setVar('thumbnailHeightMessageImageUpload2Edit', 180);
        $this->setVar('enableShrinkingForMessageImageUpload3', false);
        $this->setVar('shrinkWidthMessageImageUpload3', 800);
        $this->setVar('shrinkHeightMessageImageUpload3', 600);
        $this->setVar('thumbnailModeMessageImageUpload3', 'inset');
        $this->setVar('thumbnailWidthMessageImageUpload3View', 32);
        $this->setVar('thumbnailHeightMessageImageUpload3View', 24);
        $this->setVar('thumbnailWidthMessageImageUpload3Display', 240);
        $this->setVar('thumbnailHeightMessageImageUpload3Display', 180);
        $this->setVar('thumbnailWidthMessageImageUpload3Edit', 240);
        $this->setVar('thumbnailHeightMessageImageUpload3Edit', 180);
        $this->setVar('enableShrinkingForMessageImageUpload4', false);
        $this->setVar('shrinkWidthMessageImageUpload4', 800);
        $this->setVar('shrinkHeightMessageImageUpload4', 600);
        $this->setVar('thumbnailModeMessageImageUpload4', 'inset');
        $this->setVar('thumbnailWidthMessageImageUpload4View', 32);
        $this->setVar('thumbnailHeightMessageImageUpload4View', 24);
        $this->setVar('thumbnailWidthMessageImageUpload4Display', 240);
        $this->setVar('thumbnailHeightMessageImageUpload4Display', 180);
        $this->setVar('thumbnailWidthMessageImageUpload4Edit', 240);
        $this->setVar('thumbnailHeightMessageImageUpload4Edit', 180);
        $this->setVar('enableShrinkingForImageTheFile', false);
        $this->setVar('shrinkWidthImageTheFile', 800);
        $this->setVar('shrinkHeightImageTheFile', 600);
        $this->setVar('thumbnailModeImageTheFile', 'inset');
        $this->setVar('moderationGroupForMessages', 2);
        $this->setVar('allowModerationSpecificCreatorForMessage', false);
        $this->setVar('allowModerationSpecificCreationDateForMessage', false);
        $this->setVar('enabledFinderTypes', 'message###image');
    
        // add default entry for category registry (property named Main)
        $categoryHelper = new \MU\NewsModule\Helper\CategoryHelper(
            $this->container->get('translator.default'),
            $this->container->get('request_stack'),
            $logger,
            $this->container->get('zikula_users_module.current_user'),
            $this->container->get('zikula_categories_module.category_registry_repository'),
            $this->container->get('zikula_categories_module.api.category_permission')
        );
        $categoryGlobal = $this->container->get('zikula_categories_module.category_repository')->findOneBy(['name' => 'Global']);
        if ($categoryGlobal) {
            $categoryRegistryIdsPerEntity = [];
    
            $registry = new CategoryRegistryEntity();
            $registry->setModname('MUNewsModule');
            $registry->setEntityname('MessageEntity');
            $registry->setProperty($categoryHelper->getPrimaryProperty('Message'));
            $registry->setCategory($categoryGlobal);
    
            try {
                $this->entityManager->persist($registry);
                $this->entityManager->flush();
            } catch (Exception $exception) {
                $this->addFlash('warning', $this->__f('Error! Could not create a category registry for the %entity% entity. If you want to use categorisation, register at least one registry in the Categories administration.', ['%entity%' => 'message']));
                $logger->error('{app}: User {user} could not create a category registry for {entities} during installation. Error details: {errorMessage}.', ['app' => 'MUNewsModule', 'user' => $userName, 'entities' => 'messages', 'errorMessage' => $exception->getMessage()]);
            }
            $categoryRegistryIdsPerEntity['message'] = $registry->getId();
        }
    
        // initialisation successful
        return true;
    }
    
    public function upgrade($oldVersion)
    {
    /*
        $logger = $this->container->get('logger');
    
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->schemaTool->update($this->entities);
                } catch (Exception $exception) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'MUNewsModule', 'errorMessage' => $exception->getMessage()]);
    
                    return false;
                }
        }
    */
    
        // update successful
        return true;
    }
    
    public function uninstall()
    {
        $logger = $this->container->get('logger');
    
        try {
            $this->schemaTool->drop($this->entities);
        } catch (Exception $exception) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $exception->getMessage());
            $logger->error('{app}: Could not remove the database tables during uninstallation. Error details: {errorMessage}.', ['app' => 'MUNewsModule', 'errorMessage' => $exception->getMessage()]);
    
            return false;
        }
    
        // remove all module vars
        $this->delVars();
    
        // remove category registry entries
        $registries = $this->container->get('zikula_categories_module.category_registry_repository')->findBy(['modname' => 'MUNewsModule']);
        foreach ($registries as $registry) {
            $this->entityManager->remove($registry);
        }
        $this->entityManager->flush();
    
        // remind user about upload folders not being deleted
        $uploadPath = $this->container->getParameter('datadir') . '/MUNewsModule/';
        $this->addFlash('status', $this->__f('The upload directories at "%path%" can be removed manually.', ['%path%' => $uploadPath]));
    
        // uninstallation successful
        return true;
    }
}
