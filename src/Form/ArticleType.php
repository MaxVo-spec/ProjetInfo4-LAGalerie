<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class ArticleType
 * @package App\Form
 */
class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("title", TextType::class, [
            "label" => "Titre :"
        ])
            ->add("img", FileType::class, [
                "label" => "Image :",
                "required" => false,
                "mapped" => false,
                "constraints" => [
                    new Image,
                    new NotNull([
                        "groups" => "create"
                    ])
                ]
            ])
            ->add("text", TextareaType::class, [
                "label" => "Texte :"
            ])
            ->add("note", IntegerType::class, [
                "label" => "Votre note :",
                "attr" => [
                    "min" => 1,
                    "max" => 5
                ]
            ])

            ->add("author", TextType::class, [
                "label" => "Auteur :"
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault("data_class", Article::class);
    }

}
