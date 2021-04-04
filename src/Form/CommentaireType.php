<?php

namespace App\Article;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CommentaireType
 * @package App\Form
 */
class CommentaireType extends AbstractType

{
  public function configureOptions(OptionsResolver $resolver)
  {
      $resolver->setDefault("data_class", Commentaire::class);
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder->add("user", TextType::class, [
           "label" => "Votre pseudo :"
      ])
              ->add("message", TextareaType::class, [
             "label" => "Votre message :"
      ])
              ->add("note", IntegerType::class, [
              "label" => "Votre note :",
              "attr" => [
                 "min" => 1,
                  "max" => 5
            ]
      ]);
  }
}
