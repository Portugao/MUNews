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

declare(strict_types=1);

namespace MU\NewsModule\Event\Base;

use MU\NewsModule\Entity\MessageEntity;

/**
 * Event base class for filtering message processing.
 */
abstract class AbstractMessagePreRemoveEvent
{
    /**
     * @var MessageEntity Reference to treated entity instance.
     */
    protected $message;

    public function __construct(MessageEntity $message)
    {
        $this->message = $message;
    }

    /**
     * @return MessageEntity
     */
    public function getMessage(): MessageEntity
    {
        return $this->message;
    }
}
