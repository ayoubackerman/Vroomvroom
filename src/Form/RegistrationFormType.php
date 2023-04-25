<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType ;
use Symfony\Component\Form\CallbackTransformer;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class)
            ->add('nom')
            ->add('prenom')
            ->add('Nomd')
            ->add('num')
            ->add('image')
            ->add('roles', ChoiceType::class,[
                'choices' => ['Chauffeur' => "2" ,
            'Client' => "3" ],
                
                'multiple' => false,
                'required' => true,
            ]
        )
            ->add('statuts', null , [
                'required'=> false ,
                'empty_data' => 'Actif',])
            
            ->add('plainPassword', PasswordType::class, [
                
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\W).+$/",
                        'message' => "le mot de passe doit contenir au moins un lettre majuscule , un lettre minuscule , un chiffre et un caractére spéciale  ."
                    ]),

                    
                ],
            ])

            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'register',
                             
            ])
            

         
        ;

        $builder->get('roles')
    ->addModelTransformer(new CallbackTransformer(
        function ($rolesAsArray) {
             return count($rolesAsArray) ? $rolesAsArray[0]: null;
        },
        function ($rolesAsString) {
             return [$rolesAsString];
        }
));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
