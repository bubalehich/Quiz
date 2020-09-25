<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\Question;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class QuizCreateType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => $this->translator->trans('a.name')
        ])->add('isActive', CheckboxType::class, [
            'label' => $this->translator->trans('a.isActive'),
            'required' => false
        ])->add('Questions', CollectionType::class, [
            'entry_type' => EntityType::class,
            'entry_options' => [
                'class' => Question::class,
                'choice_label' => 'name',
                'label' => false
            ],
            'by_reference' => false,
            'label' => $this->translator->trans('a.questions'),
            'allow_add' => true,
            'allow_delete' => true
        ])->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-success'
            ],
            'label' => $this->translator->trans('a.submit')
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