<?php


namespace App\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,['required' => true])
            ->add('email', TextType::class,['required' => true])
            ->add('password', TextType::class,['required' => true])
            ->add('save', SubmitType::class, ['label' => 'Registrate'])
        ;
    }
}
