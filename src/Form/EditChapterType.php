<?php

namespace App\Form;

use App\Entity\Chapter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @extends AbstractType<Chapter>
 */
class EditChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('pagesFiles', FileType::class, [
            'multiple' => true,
            'mapped' => false,
            'required' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChapterType::class;
    }
}
