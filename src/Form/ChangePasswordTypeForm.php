<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordTypeForm extends AbstractType
{
    private const PASSWORD_MIN_LENGTH = 6;
    private const PASSWORD_MAX_LENGTH = 100;
    private TranslatorInterface $translator;

    /**
     * ResetPasswordFormType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPassword', PasswordType::class, [
            'required' => true,
            'label' => false
        ])
            ->add('plainPassword', RepeatedType::class, [
                'label' => false,
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
                    'label' => false,
                ],
                'second_options' => [
                    'label' => false,
                ],
                'invalid_message' => $this->translator->trans('msg.password.match'),
                'mapped' => false,
            ]);
    }
}