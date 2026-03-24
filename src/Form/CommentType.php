<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('authorName', TextType::class, [
                'label' => 'Nome',
                'attr' => ['placeholder' => 'Il tuo nome'],
            ])
            ->add('authorEmail', EmailType::class, [
                'label' => 'Email (opzionale)',
                'required' => false,
                'attr' => ['placeholder' => 'email@esempio.it'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Commento',
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Scrivi il tuo commento...',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
