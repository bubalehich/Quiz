<?php


namespace App\Form;


use App\Entity\Quiz;
use App\Entity\Question;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('isActive')
            ->add('Questions',CollectionType::class,[
                'entry_type'=>EntityType::class,
                'entry_options'=>[
                    'class'=>Question::class,
                    'choice_label'=>'name',
                    'label'=>false
                ],
                'by_reference'=>false,
                'allow_add'=>true,
                'allow_delete'=>true
            ])->add('submit',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
            'csrf_protection' => false,
            'validation_groups' => false
        ]);
    }
}