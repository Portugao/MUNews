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

namespace MU\NewsModule\Entity;

use MU\NewsModule\Entity\Base\AbstractMessageTranslationEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity extension domain class storing message translations.
 *
 * This is the concrete translation class for message entities.
 *
 * @ORM\Entity(repositoryClass="MU\NewsModule\Entity\Repository\MessageTranslationRepository")
 * @ORM\Table(
 *     name="mu_news_message_translation",
 *     options={"row_format":"DYNAMIC"},
 *     indexes={
 *         @ORM\Index(name="translations_lookup_idx", columns={
 *             "locale", "object_class", "foreign_key"
 *         })
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *             "locale", "object_class", "field", "foreign_key"
 *         })
 *     }
 * )
 */
class MessageTranslationEntity extends BaseEntity
{
    // feel free to add your own methods here
}
