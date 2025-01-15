<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFiltersType extends AbstractType
{
    private array $currentState =[];
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', HiddenType::class, [

        ])
            ->add('name')
            ->add('state')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('priority')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }

    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    public function setCurrentState(string $currentState): Post
    {
        $this->currentState = $currentState;
        return $this;
    }

}
