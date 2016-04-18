<?php

namespace Flower\BoardBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Flower\ModelBundle\Entity\Board\TaskType as TaskType2;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class TaskType extends AbstractType
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
            ->add('assignee', 'entity', array(
                'class' => 'FlowerModelBundle:User\User',
            ))
            ->add('estimated', null, array('required' => false))
            ->add('dueDate', 'collot_datetime', array('required' => false,
                'pickerOptions' =>
                    array('format' => 'dd/mm/yyyy  hh:ii',
                        'autoclose' => true,
                        'todayBtn' => true,
                        'todayHighlight' => true,
                        'keyboardNavigation' => true,
                        'language' => 'en',
                    )))
            ->add('type', 'choice', array(
                'choices' => array(
                    TaskType2::TYPE_TASK => "task",
                    TaskType2::TYPE_BUG => "bug",
                )
            ))
            ->add('status', null, array('required' => true))
            ->add('tracker')
            ->add('project')
            ->add('projectIteration')
            ->add('description', 'ckeditor', array(
                'required' => false,
                'config_name' => 'minimal'
            ))
            ->add('save', 'submit', array('label' => 'Save'))
            ->add('saveAndAdd', 'submit', array('label' => 'Save and add'));
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
