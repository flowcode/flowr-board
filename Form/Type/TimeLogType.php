<?php

namespace Flower\BoardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimeLogType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('hours')
            ->add('description')
            ->add('spentOn','collot_datetime', array( 'required' => false,
                                                'pickerOptions' =>
                                                array('format' => 'dd/mm/yyyy  hh:ii',
                                                    'autoclose' => true,
                                                    'todayBtn' => true,
                                                    'todayHighlight' => true,
                                                    'keyboardNavigation' => true,
                                                    'language' => 'en',
                                                    )))
            ->add('task')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Board\TimeLog',
            'translation_domain' => 'TimeLog',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'timelog';
    }
}
