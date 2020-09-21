<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditUsernameFormType extends AbstractType
{
    private const NAME_MIN_LENGTH = 3;
    private const NAME_MAX_LENGTH = 40;
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
        $builder->add('name', TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => $this->translator->trans('u.name.msg'),
                ]),
                new Length([
                    'min' => self::NAME_MIN_LENGTH,
                    'minMessage' => sprintf($this->translator->trans('u.name.msg.min'), self::NAME_MIN_LENGTH),
                    'max' => self::NAME_MAX_LENGTH,
                    'maxMessage' => sprintf($this->translator->trans('u.name.msg.max'), self::NAME_MAX_LENGTH),
                ]),

            ],
            'label' => false,
        ]);
    }
}