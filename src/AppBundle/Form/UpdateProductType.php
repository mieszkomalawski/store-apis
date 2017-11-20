<?php

namespace AppBundle\Form;

use AppBundle\Command\UpdateProductCommand;
use AppBundle\CustomPropertyAccessor;
use Money\Money;
use Store\Catalog\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateProductType extends AbstractType
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

        $builder
            // we are not mapping fields since properties are already set via constuctor
            ->add('name', TextType::class)
            ->add('price', NumberType::class);

        $builder->getForm();
    }
}
