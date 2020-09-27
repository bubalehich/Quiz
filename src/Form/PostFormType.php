<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostFormType extends AbstractType
{
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
                'attr' => ['value' => $data]
            ]);
        } else {
            $builder->add('message', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'message']
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