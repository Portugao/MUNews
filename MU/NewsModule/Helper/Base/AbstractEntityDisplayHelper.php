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

use IntlDateFormatter;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
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
    
    /**
     * EntityDisplayHelper constructor.
     *
     * @param TranslatorInterface $translator
     * @param RequestStack $requestStack
     * @param ListEntriesHelper $listEntriesHelper
     */
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        ListEntriesHelper $listEntriesHelper
    ) {
        $this->translator = $translator;
        $this->listEntriesHelper = $listEntriesHelper;
        $locale = null !== $requestStack->getCurrentRequest() ? $requestStack->getCurrentRequest()->getLocale() : null;
        $this->dateFormatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE);
    }
    
    /**
     * Returns the formatted title for a given entity.
     *
     * @param object $entity The given entity instance
     *
     * @return string The formatted title
     */
    public function getFormattedTitle($entity)
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
     * Returns the formatted title for a given entity.
     *
     * @param MessageEntity $entity The given entity instance
     *
     * @return string The formatted title
     */
    protected function formatMessage(MessageEntity $entity)
    {
        return $this->translator->__f('%title%', [
            '%title%' => $entity->getTitle()
        ]);
    }
    
    /**
     * Returns the formatted title for a given entity.
     *
     * @param ImageEntity $entity The given entity instance
     *
     * @return string The formatted title
     */
    protected function formatImage(ImageEntity $entity)
    {
        return $this->translator->__f('Image %sortNumber% %caption%', [
            '%sortNumber%' => $entity->getSortNumber(),
            '%caption%' => $entity->getCaption()
        ]);
    }
    
    /**
     * Returns name of the field used as title / name for entities of this repository.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used as title
     */
    public function getTitleFieldName($objectType)
    {
        if ($objectType == 'message') {
            return 'title';
        }
        if ($objectType == 'image') {
            return 'caption';
        }
    
        return '';
    }
    
    /**
     * Returns name of the field used for describing entities of this repository.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used as description
     */
    public function getDescriptionFieldName($objectType)
    {
        if ($objectType == 'message') {
            return 'startText';
        }
        if ($objectType == 'image') {
            return 'caption';
        }
    
        return '';
    }
    
    /**
     * Returns name of first upload field which is capable for handling images.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used for preview images
     */
    public function getPreviewFieldName($objectType)
    {
        if ($objectType == 'message') {
            return 'imageUpload1';
        }
        if ($objectType == 'image') {
            return 'theFile';
        }
    
        return '';
    }
    
    /**
     * Returns name of the date(time) field to be used for representing the start
     * of this object. Used for providing meta data to the tag module.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return string Name of field to be used as date
     */
    public function getStartDateFieldName($objectType)
    {
        if ($objectType == 'message') {
            return 'startDate';
        }
        if ($objectType == 'image') {
            return 'createdDate';
        }
    
        return '';
    }
}
