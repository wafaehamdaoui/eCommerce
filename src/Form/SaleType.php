<?php

namespace App\Form;

use App\Entity\Sale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('totalPrice')
            ->add('date')
            ->add('quantity')
            ->add('client')
            ->add('shippingAddress')
            ->add('product')
            ->add('status',ChoiceType::class, [
                   'choices' => [
                       'pending' => 'pending',
                       'shipping' => 'shipping',
                       'delivered' => 'delivered',
                   ],
               ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sale::class,
        ]);
    }
}
