<?php

namespace App\Form;

use App\Entity\Manga;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

/**
 * @extends AbstractType<Manga>
 */
class EditMangaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->remove('firstChapter');

        $builder->add('miniatureFile', FileType::class, [
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File(
                    maxSize: '2M',
                    mimeTypes: ['image/png', 'image/jpeg', 'image/webp'],
                    mimeTypesMessage: 'Merci de fournir une image valide (PNG, JPEG ou WebP).',
                ),
            ],
        ])
        ;
    }

    public function getParent(): string
    {
        return MangaType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Manga::class,
        ]);
    }
}
