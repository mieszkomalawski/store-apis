<?php

namespace AppBundle\Form;

use AppBundle\Model\UpdateProductCommand;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateProductType extends CreateProductType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdateProductCommand::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $formEvent) {
                $data = $formEvent->getData();
                if (!isset($data['price'])) {
                    $formEvent->getForm()->remove('price');
                }
                if (!isset($data['name'])) {
                    $formEvent->getForm()->remove('name');
                }
            });

        $builder->getForm();
    }
}
