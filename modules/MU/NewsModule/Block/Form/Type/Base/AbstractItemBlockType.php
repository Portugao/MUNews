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

namespace MU\NewsModule\Block\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use MU\NewsModule\Entity\Factory\EntityFactory;
use MU\NewsModule\Helper\EntityDisplayHelper;

/**
 * Detail block form type base class.
 */
abstract class AbstractItemBlockType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;

    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->entityDisplayHelper = $entityDisplayHelper;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addObjectTypeField($builder, $options);
        $this->addIdField($builder, $options);
        $this->addTemplateField($builder, $options);
    }

    /**
     * Adds an object type field.
     */
    public function addObjectTypeField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('objectType', HiddenType::class, [
            'label' => $this->__('Object type', 'munewsmodule') . ':',
            'empty_data' => 'message'
        ]);
    }

    /**
     * Adds a item identifier field.
     */
    public function addIdField(FormBuilderInterface $builder, array $options = [])
    {
        $repository = $this->entityFactory->getRepository($options['object_type']);
        // select without joins
        $entities = $repository->selectWhere('', '', false);
    
        $choices = [];
        foreach ($entities as $entity) {
            $choices[$this->entityDisplayHelper->getFormattedTitle($entity)] = $entity->getKey();
        }
        ksort($choices);
    
        $builder->add('id', ChoiceType::class, [
            'multiple' => false,
            'expanded' => false,
            'choices' => $choices,
            'required' => true,
            'label' => $this->__('Entry to display', 'munewsmodule') . ':'
        ]);
    }

    /**
     * Adds template fields.
     */
    public function addTemplateField(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('customTemplate', TextType::class, [
                'label' => $this->__('Custom template', 'munewsmodule') . ':',
                'required' => false,
                'attr' => [
                    'maxlength' => 80,
                    'title' => $this->__('Example', 'munewsmodule') . ': displaySpecial.html.twig'
                ],
                'help' => [
                    $this->__('Example', 'munewsmodule') . ': <em>displaySpecial.html.twig</em>',
                    $this->__('Needs to be located in the "External/YourEntity/" directory.', 'munewsmodule')
                ]
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'munewsmodule_detailblock';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'object_type' => 'message'
            ])
            ->setRequired(['object_type'])
            ->setAllowedTypes('object_type', 'string')
        ;
    }
}
