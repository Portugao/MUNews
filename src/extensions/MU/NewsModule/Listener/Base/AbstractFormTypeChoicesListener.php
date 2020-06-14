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

namespace MU\NewsModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Bundle\FormExtensionBundle\Event\FormTypeChoiceEvent;

/**
 * Event handler base class for injecting custom dynamic form types.
 */
abstract class AbstractFormTypeChoicesListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormTypeChoiceEvent::class => ['formTypeChoices']
        ];
    }
    
    /**
     * Listener for the `FormTypeChoiceEvent` event.
     *
     * Implement using like this:
     *
     * $choices = $event->getChoices();
     *
     * $groupName = $this->translator->trans('Other Fields');
     * if (!isset($choices[$groupName])) {
     *     $choices[$groupName] = [];
     * }
     *
     * $groupChoices = $choices[$groupName];
     * $groupChoices[$this->translator->trans('Special field')] = SpecialFieldType::class;
     * $choices[$groupName] = $groupChoices;
     *
     * $event->setChoices($choices);
     */
    public function formTypeChoices(FormTypeChoiceEvent $event): void
    {
    }
}
