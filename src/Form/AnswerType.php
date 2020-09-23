<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Contracts\Translation\TranslatorInterface;

class AnswerType extends AbstractType
{
    private TranslatorInterface $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',TextType::class,[
            'label'=>$this->translator->trans('a.name')
        ])->add('isRight',CheckboxType::class,[
                'required'=>false,
                'label'=>$this->translator->trans('a.isRight')
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class
        ]);
    }
}