<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('body', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
                'label' => 'Votre commentaire',
                'required' => false,
                'attr' => ['rows' => 3, 'class' => 'form-control', 'placeholder' => 'Écrivez un commentaire...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
