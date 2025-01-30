<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class, ['required' => false])
            ->add('name', TextType::class, ['required' => false])
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    'Haute' => 1,
                    'Moyenne' => 2,
                    'Basse' => 3,
                ],
                'required' => false,
            ])
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'pending',
                    'Complétée' => 'completed',
                ],
                'required' => false,
            ])
            ->add('createdAt', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
