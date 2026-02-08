<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Username',  
                    'class' => 'mb-4 text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a username',
                    ]),
                    new Assert\Length([
                        'min' => 4,
                        'minMessage' => 'Votre username doit contenir minimum {{ limit }} characters',
                        'max' => 50,
                    ])
                ],
                
                ])

            ->add('address' , TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Address',  
                    'class' => 'mb-4 text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter an address',
                    ]),
                    new Assert\Email([
                        'message' => 'Please enter a valid email address',
                    ]),
                ],
                ])

            ->add('password', PasswordType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Password',  
                    'class' => 'mb-4 text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Your password must be at least {{ limit }} characters long',
                        'max' => 4096,
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*\d).+$/',
                        'message' => 'Your password must contain at least one uppercase letter and one number',
                    ]),
                ],
                ])

            ->add('role' , ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Content Creator' => 'ROLE_CREATOR',
                    'Manager' => 'ROLE_MANAGER',
                    'Editor' => 'ROLE_EDITOR',
                ],
                'attr' => [
                    'class' => 'mb-4 text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please select a role',
                    ]),
                ],
            ])

            ->add('creatorSelection', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'username',
                'label' => false,
                'placeholder' => 'Select a Content Creator',
                'mapped' => false,
                'required' => false,
                'query_builder' => function (\App\Repository\UsersRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.role = :role')
                        ->setParameter('role', 'ROLE_CREATOR');
                },
                'attr' => [
                    'class' => 'text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow'
                ],
            ])

            ->add('managerSelection', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'username',
                'label' => false,
                'placeholder' => 'Select a Manager (Optional)',
                'mapped' => false,
                'required' => false,
                'query_builder' => function (\App\Repository\UsersRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.role = :role')
                        ->setParameter('role', 'ROLE_MANAGER');
                },
                'attr' => [
                    'class' => 'text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow'
                ],
            ])

            ->add('numtel' , TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Phone Number',  
                    'class' => 'mb-4 text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow'
                ],
                
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a phone number',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'Please enter a valid 8-digit phone number',
                    ]),
                ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
