<?php

namespace App\Form;

use App\Entity\Manga;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Manga>
 */
class EditMangaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['required' => true])
            ->add('synopsis', TextareaType::class, ['required' => false])
            ->add('miniatureFile', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('readingDirection', ChoiceType::class, [
                'choices' => ['JP' => 'JP', 'FR' => 'FR'],
                'expanded' => true,
                'choice_attr' => static function () {
                    return ['style' => 'display: none;'];
                },
            ])
            ->add('categories', ChoiceType::class, [
                'choices' => [
                    'Action' => 'Action',
                    'Romance' => 'Romance',
                    'Fantastique' => 'Fantastique',
                    'Tranche de vie' => 'Tranche de vie',
                    'Horreur' => 'Horreur',
                    'Comédie' => 'Comédie',
                    'Sport' => 'Sport',
                ],
                'expanded' => true,
                'multiple' => true,
                'choice_attr' => static function () {
                    return ['style' => 'display: none;'];
                },
            ])
            ->add('status', ChoiceType::class, [
                'choices' => ['En cours' => 'En cours', 'Terminé' => 'Terminé', 'En pause' => 'En pause'],
                'expanded' => true,
                'choice_attr' => static function () {
                    return ['style' => 'display: none;'];
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Manga::class,
        ]);
    }
}
