<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostFormType extends AbstractType
{
    private const MSG_MIN_LENGTH = 1;
    private const MSG_MAX_LENGTH = 100;
    private TranslatorInterface $translator;

    /**
     * PostFormType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $options['msg'];
        if ($data) {
            $builder->add('message', TextType::class, [
                'label' => false,
                'attr' => ['value' => $data],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('post.msg'),
                    ]),
                    new Length([
                        'min' => self::MSG_MIN_LENGTH,
                        'minMessage' => sprintf($this->translator->trans('post.msg.min'), self::MSG_MIN_LENGTH),
                        'max' => self::MSG_MAX_LENGTH,
                        'maxMessage' => sprintf($this->translator->trans('post.msg.max'), self::MSG_MAX_LENGTH),
                    ]),
                ],
            ]);
        } else {
            $builder->add('message', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => $this->translator->trans('post.message')]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'msg' => null
        ]);
    }
}