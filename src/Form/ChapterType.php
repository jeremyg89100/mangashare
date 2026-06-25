<?php

namespace App\Form;

use App\Entity\Chapter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

/**
 * @extends AbstractType<Chapter>
 */
class ChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('published', ChoiceType::class, [
                'choices' => ['Publié' => true, 'Non publié' => false],
                'expanded' => true,
                'multiple' => false,
                'choice_attr' => static function () {
                    return ['style' => 'display: none;'];
                },
            ])
            ->add('pagesFiles', FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'required' => true,
                'constraints' => [
                    new All([
                        new File(
                            maxSize: '5M',
                            mimeTypes: ['image/png', 'image/jpeg', 'image/webp'],
                            mimeTypesMessage: 'Merci de fournir une image valide (PNG, JPEG ou WebP).',
                        ),
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapter::class,
        ]);
    }
}
