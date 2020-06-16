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

namespace MU\NewsModule\Form\Type\QuickNavigation;

use MU\NewsModule\Form\Type\QuickNavigation\Base\AbstractMessageQuickNavType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Message quick navigation form type implementation class.
 */
class MessageQuickNavType extends AbstractMessageQuickNavType
{
    public function addOutgoingRelationshipFields(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('images', HiddenType::class);
    }
}