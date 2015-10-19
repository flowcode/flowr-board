<?php

namespace Flower\BoardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BoardType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('name')
            ->add('description', null, array('required' => false))
            ->add('status', 'choice', array(
                    'choices' => array(
                        0 => "Closed",
                        1 => "Active",
                    )
                ))
            ->add('startDate','collot_datetime', array( 'required' => false,
                                                'pickerOptions' =>
                                                array('format' => 'dd/mm/yyyy  hh:ii',
                                                    'autoclose' => true,
                                                    'todayBtn' => true,
                                                    'todayHighlight' => true,
                                                    'keyboardNavigation' => true,
                                                    'language' => 'en',
                                                    )))
            ->add('dueDate','collot_datetime', array( 'required' => false,
                                                'pickerOptions' =>
                                                array('format' => 'dd/mm/yyyy  hh:ii',
                                                    'autoclose' => true,
                                                    'todayBtn' => true,
                                                    'todayHighlight' => true,
                                                    'keyboardNavigation' => true,
                                                    'language' => 'en',
                                                    )))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Board\Board',
            'translation_domain' => 'Board',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'board';
    }
}
