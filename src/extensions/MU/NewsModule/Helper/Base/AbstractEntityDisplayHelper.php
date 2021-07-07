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

use IntlDateFormatter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use MU\NewsModule\Entity\MessageEntity;
use MU\NewsModule\Entity\ImageEntity;
use MU\NewsModule\Helper\ListEntriesHelper;

/**
 * Entity display helper base class.
 */
abstract class AbstractEntityDisplayHelper
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var ListEntriesHelper Helper service for managing list entries
     */
    protected $listEntriesHelper;
    
    /**
     * @var IntlDateFormatter Formatter for dates
     */
    protected $dateFormatter;
    
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        ListEntriesHelper $listEntriesHelper
    ) {
        $this->translator = $translator;
        $this->listEntriesHelper = $listEntriesHelper;
        $locale = null !== $requestStack->getCurrentRequest() ? $requestStack->getCurrentRequest()->getLocale() : 'en';
        $this->dateFormatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE);
    }
    
    /**
     * Returns the formatted title for a given entity.
     */
    public function getFormattedTitle(EntityAccess $entity): string
    {
        if ($entity instanceof MessageEntity) {
            return $this->formatMessage($entity);
        }
        if ($entity instanceof ImageEntity) {
            return $this->formatImage($entity);
        }
    
        return '';
    }
    
    /**
     * Returns an additional description for a given entity.
     */
    public function getDescription(EntityAccess $entity): string
    {
        if ($entity instanceof MessageEntity) {
            return $this->getMessageDescription($entity);
        }
        if ($entity instanceof ImageEntity) {
            return $this->getImageDescription($entity);
        }
    
        return '';
    }
    
    /**
     * Returns the formatted title for a given message.
     */
    protected function formatMessage(MessageEntity $entity): string
    {
        return $this->translator->trans(
            '%title%',
            [
                '%title%' => $entity->getTitle(),
            ],
            'message'
        );
    }
    
    /**
     * Returns an additional description for a given message.
     */
    protected function getMessageDescription(MessageEntity $entity): string
    {
        $descriptionFieldName = $this->getDescriptionFieldName($entity->get_objectType());
    
        return isset($entity[$descriptionFieldName]) && !empty($entity[$descriptionFieldName])
            ? $entity[$descriptionFieldName]
            : ''
        ;
    }
    
    /**
     * Returns the formatted title for a given image.
     */
    protected function formatImage(ImageEntity $entity): string
    {
        return $this->translator->trans(
            'Image %sortNumber% %caption%',
            [
                '%sortNumber%' => $entity->getSortNumber(),
                '%caption%' => $entity->getCaption(),
            ],
            'image'
        );
    }
    
    /**
     * Returns an additional description for a given image.
     */
    protected function getImageDescription(ImageEntity $entity): string
    {
        $descriptionFieldName = $this->getDescriptionFieldName($entity->get_objectType());
    
        return isset($entity[$descriptionFieldName]) && !empty($entity[$descriptionFieldName])
            ? $entity[$descriptionFieldName]
            : ''
        ;
    }
    
    /**
     * Returns name of the field used as title / name for entities of this repository.
     */
    public function getTitleFieldName(string $objectType = ''): string
    {
        if ('message' === $objectType) {
            return 'title';
        }
        if ('image' === $objectType) {
            return 'caption';
        }
    
        return '';
    }
    
    /**
     * Returns name of the field used for describing entities of this repository.
     */
    public function getDescriptionFieldName(string $objectType = ''): string
    {
        if ('message' === $objectType) {
            return 'startText';
        }
        if ('image' === $objectType) {
            return 'caption';
        }
    
        return '';
    }
    
    /**
     * Returns name of first upload field which is capable for handling images.
     */
    public function getPreviewFieldName(string $objectType = ''): string
    {
        if ('message' === $objectType) {
            return 'imageUpload1';
        }
        if ('image' === $objectType) {
            return 'theFile';
        }
    
        return '';
    }
    
    /**
     * Returns name of the date(time) field to be used for representing the start
     * of this object. Used for providing meta data to the tag module.
     */
    public function getStartDateFieldName(string $objectType = ''): string
    {
        if ('message' === $objectType) {
            return 'startDate';
        }
        if ('image' === $objectType) {
            return 'createdDate';
        }
    
        return '';
    }
}
