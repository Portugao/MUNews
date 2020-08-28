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

namespace MU\NewsModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * Entity extension domain class storing message translations.
 *
 * This is the base translation class for message entities.
 */
abstract class AbstractMessageTranslationEntity extends AbstractTranslation
{
    
    /**
     * Use a length of 140 instead of 255 to avoid too long keys for the indexes.
     *
     * @var string
     *
     * @ORM\Column(name="object_class", type="string", length=140)
     */
    protected $objectClass;
    
    /**
     * Use integer instead of string for increased performance.
     *
     * @see https://github.com/Atlantic18/DoctrineExtensions/issues/1512
     *
     * @var int
     *
     * @ORM\Column(name="foreign_key", type="integer")
     */
    protected $foreignKey;
    
    /**
     * Clone interceptor implementation.
     * Performs a quite simple shallow copy.
     *
     * See also:
     * (1) http://docs.doctrine-project.org/en/latest/cookbook/implementing-wakeup-or-clone.html
     * (2) http://www.php.net/manual/en/language.oop5.cloning.php
     * (3) http://stackoverflow.com/questions/185934/how-do-i-create-a-copy-of-an-object-in-php
     */
    public function __clone()
    {
        // if the entity has no identity do nothing, do NOT throw an exception
        if (!$this->id) {
            return;
        }
    
        // unset identifier
        $this->id = 0;
    }
}
