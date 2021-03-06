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

namespace MU\NewsModule\Helper\Base;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\SettingsModule\Api\ApiInterface\LocaleApiInterface;
use MU\NewsModule\Entity\Factory\EntityFactory;

/**
 * Helper base class for translatable methods.
 */
abstract class AbstractTranslatableHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var LocaleApiInterface
     */
    protected $localeApi;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        VariableApiInterface $variableApi,
        LocaleApiInterface $localeApi,
        EntityFactory $entityFactory
    ) {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->variableApi = $variableApi;
        $this->localeApi = $localeApi;
        $this->entityFactory = $entityFactory;
    }
    
    /**
     * Return list of translatable fields per entity.
     * These are required to be determined to recognise
     * that they have to be selected from according translation tables.
     */
    public function getTranslatableFields(string $objectType): array
    {
        $fields = [];
        switch ($objectType) {
            case 'message':
                $fields = ['title', 'startText', 'mainText', 'slug'];
                break;
        }
    
        return $fields;
    }
    
    /**
     * Return the current language code.
     */
    public function getCurrentLanguage(): string
    {
        $request = $this->requestStack->getCurrentRequest();
    
        return null !== $request ? $request->getLocale() : 'en';
    }
    
    /**
     * Return list of supported languages on the current system.
     */
    public function getSupportedLanguages(string $objectType): array
    {
        if ($this->variableApi->getSystemVar('multilingual')) {
            return $this->localeApi->getSupportedLocales();
        }
    
        // if multi language is disabled use only the current language
        return [$this->getCurrentLanguage()];
    }
    
    /**
     * Returns a list of mandatory fields for each supported language.
     */
    public function getMandatoryFields(string $objectType): array
    {
        $mandatoryFields = [];
        foreach ($this->getSupportedLanguages($objectType) as $language) {
            $mandatoryFields[$language] = [];
        }
    
        return $mandatoryFields;
    }
    
    /**
     * Collects translated fields for editing.
     *
     * @return array Collected translations for each language code
     */
    public function prepareEntityForEditing(EntityAccess $entity): array
    {
        $translations = [];
        $objectType = $entity->get_objectType();
    
        if (!$this->variableApi->getSystemVar('multilingual')) {
            return $translations;
        }
    
        // check if there are any translated fields registered for the given object type
        $fields = $this->getTranslatableFields($objectType);
        if (!count($fields)) {
            return $translations;
        }
    
        // get translations
        $entityManager = $this->entityFactory->getEntityManager();
        $repository = $entityManager->getRepository(
            'MU\NewsModule\Entity\\' . ucfirst($objectType) . 'TranslationEntity'
        );
        $entityTranslations = $repository->findTranslations($entity);
    
        $supportedLanguages = $this->getSupportedLanguages($objectType);
        $currentLanguage = $this->getCurrentLanguage();
        foreach ($supportedLanguages as $language) {
            if ($language === $currentLanguage) {
                foreach ($fields as $fieldName) {
                    if (null === $entity[$fieldName]) {
                        $entity[$fieldName] = '';
                    }
                }
                // skip current language as this is not treated as translation on controller level
                continue;
            }
            $translationData = [];
            foreach ($fields as $fieldName) {
                $translationData[$fieldName] = $entityTranslations[$language][$fieldName] ?? '';
            }
            // add data to collected translations
            $translations[$language] = $translationData;
        }
    
        return $translations;
    }
    
    /**
     * Post-editing method persisting translated fields.
     */
    public function processEntityAfterEditing(EntityAccess $entity, FormInterface $form): void
    {
        $objectType = $entity->get_objectType();
        $entityManager = $this->entityFactory->getEntityManager();
        $supportedLanguages = $this->getSupportedLanguages($objectType);
        foreach ($supportedLanguages as $language) {
            $translationInput = $this->readTranslationInput($form, $language);
            if (!count($translationInput)) {
                continue;
            }
    
            foreach ($translationInput as $fieldName => $fieldData) {
                $setter = 'set' . ucfirst($fieldName);
                $entity->$setter($fieldData);
            }
    
            $entity->setLocale($language);
            $entityManager->flush();
        }
    }
    
    /**
     * Collects translated fields from given form for a specific language.
     */
    public function readTranslationInput(FormInterface $form, string $language = 'en'): array
    {
        $data = [];
        $translationKey = 'translations' . $language;
        if (!isset($form[$translationKey])) {
            return $data;
        }
        $translatedFields = $form[$translationKey];
        foreach ($translatedFields as $fieldName => $formField) {
            $fieldData = $formField->getData();
            if (!$fieldData && isset($form[$fieldName])) {
                $fieldData = $form[$fieldName]->getData();
            }
            $data[$fieldName] = $fieldData;
        }
    
        return $data;
    }
}
