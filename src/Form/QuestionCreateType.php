<?php


namespace App\Form;

use App\Entity\Question;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('answers',CollectionType::class, [
                'entry_type'=>AnswerType::class,
                'entry_options'=>[
                    'label'=>false
                ],
                'by_reference'=>false,
                'allow_add'=>true,
                'allow_delete'=>true
            ])
           ->add('submit',SubmitType::class, [
               'attr'=>[
                   'class'=>'btn btn-primary'
               ]
           ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
            'csrf_protection' => false,
            'validation_groups' => false
        ]);
    }
}