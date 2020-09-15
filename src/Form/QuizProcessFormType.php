<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Question;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizProcessFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**@var Question $question */
        $question = $options['question'];
        $builder->add('answer', EntityType::class, [
            'class' => Answer::class,
            'choices' => $question->getAnswers(),
            'multiple' => false,
            'expanded' => true,
            'placeholder'=>''
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'question' => null
        ]);
    }
}