<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Question;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class QuizProcessFormType extends AbstractType
{
    private TranslatorInterface $translator;

    /**
     * QuizProcessFormType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**@var Question $question */
        $question = $options['question'];
        $builder->add('answer', EntityType::class, [
            'class' => Answer::class,
            'choices' => $question->getAnswers(),
            'multiple' => false,
            'expanded' => true,
            'label' => $this->translator->trans('l.answer'),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'question' => null
        ]);
    }
}