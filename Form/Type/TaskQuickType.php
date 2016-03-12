<?php

namespace Flower\BoardBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Flower\ModelBundle\Entity\Board\TaskType as TaskType2;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class TaskQuickType extends AbstractType
{

    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name')
            ->add('account')
            ->add('assignee', 'entity', array(
                'class' => 'FlowerModelBundle:User\User',
                'required' => false,
            ))
            ->add('estimated', null, array('required' => false))
            ->add('type', 'choice', array(
                'choices' => array(
                    TaskType2::TYPE_TASK => "task",
                    TaskType2::TYPE_BUG => "bug",
                )
            ))
            ->add('status', null, array('required' => true))
            ->add('dueDate', 'collot_datetime', array('required' => false,
                'pickerOptions' =>
                    array('format' => 'dd/mm/yyyy  hh:ii',
                        'autoclose' => true,
                        'todayBtn' => true,
                        'todayHighlight' => true,
                        'keyboardNavigation' => true,
                        'language' => 'en',
                    )))
            ->add('tracker', null, array('required' => false))
            ->add('description', 'ckeditor', array(
                'required' => false,
                'config_name' => 'minimal'
            ))
            ->add('save', 'submit', array('label' => 'Save'));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Board\Task',
            'translation_domain' => 'Task',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'task';
    }

}
