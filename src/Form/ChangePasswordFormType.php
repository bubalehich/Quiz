<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordFormType extends AbstractType
{
    private const PASSWORD_MIN_LENGTH = 6;
    private const PASSWORD_MAX_LENGTH = 100;
    private TranslatorInterface $translator;

    /**
     * ChangePasswordFormType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('msg.password'),
                    ]),
                    new Length([
                        'min' => self::PASSWORD_MIN_LENGTH,
                        'minMessage' => sprintf($this->translator->trans('msg.password.min'), self::PASSWORD_MIN_LENGTH),
                        'max' => self::PASSWORD_MAX_LENGTH,
                        'maxMessage' => sprintf($this->translator->trans('msg.password.max'), self::PASSWORD_MAX_LENGTH),
                    ]),
                ],
                'label' => $this->translator->trans('l.password.new'),
            ],
            'second_options' => [
                'label' => $this->translator->trans('l.password.repeat'),
            ],
            'invalid_message' => $this->translator->trans('msg.password.match'),
            'mapped' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}