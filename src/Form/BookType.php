<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [new NotBlank(), new Length(['min'=>2,'max'=>255])]
            ])
            ->add('author', TextType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Fantasy' => 'Fantasy',
                    'Science Fiction' => 'Science Fiction',
                    'Romance' => 'Romance',
                    'Thriller' => 'Thriller',
                ],
            ])
            ->add('coverImage', FileType::class, [
                'label' => 'Cover Image (JPG or PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg','image/png'],
                        'mimeTypesMessage' => 'Please upload a valid JPG or PNG image',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Book::class]);
    }
}
