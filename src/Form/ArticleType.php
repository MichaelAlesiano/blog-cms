<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titolo',
                'attr' => ['placeholder' => 'Inserisci il titolo dell\'articolo'],
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'Estratto',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Breve descrizione dell\'articolo (opzionale)',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenuto',
                'attr' => [
                    'rows' => 12,
                    'placeholder' => 'Scrivi il contenuto dell\'articolo...',
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categoria',
                'required' => false,
                'placeholder' => '-- Seleziona categoria --',
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'label' => 'Tag',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('coverImageFile', FileType::class, [
                'label' => 'Immagine di copertina',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Carica un\'immagine valida (JPEG, PNG o WebP)',
                    ]),
                ],
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'Pubblicato',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
