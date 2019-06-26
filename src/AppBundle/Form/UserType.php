<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Form\Events\UploadFileSubscriber;
use AppBundle\Form\Events\UserSubscriber;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var UserManagerInterface
     */
    private $userManager;


    public function __construct(TokenStorage $tokenStorage, UserManagerInterface $userManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();

        $data = $builder->getData();

        $rol = 'ROLE_USER';

        if ($data instanceof User) {
            if ($data->hasRole('ROLE_ADMIN')) {
                $rol = 'ROLE_ADMIN';
            }
        }

        if ('POST' === $builder->getMethod()) {
            $builder
                ->add('name')
                ->add('lastName')
                ->add(
                    'username',
                    null,
                    array('constraints' => array(new NotBlank(), new Length(array('min' => 4, 'max' => 20))))
                )
                ->add(
                    'email',
                    null,
                    array(
                        'constraints' => array(
                            new NotBlank(),
                            new Length(array('min' => 10, 'max' => 50)),
                            new Email(),
                        ),
                    )
                )
                ->add(
                    'enabled',
                    ChoiceType::class,
                    array('label' => 'Status', 'choices' => array('Enabled' => true, 'Disabled' => false))
                )
                ->add(
                    'rol',
                    ChoiceType::class,
                    array(
                        'mapped' => false,
                        'data' => $rol,
                        'choices' => array('User' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'),
                    )
                )
                ->add(
                    'plain_password',
                    RepeatedType::class,
                    array(
                        'type' => PasswordType::class,
                        'invalid_message' => 'Password invalid message',
                        'options' => array('attr' => array('class' => 'password-field')),
                        'first_options' => array('label' => 'New password'),
                        'second_options' => array('label' => 'Repeat password'),
                        'constraints' => array(
                            new Length(array('min' => 8, 'max' => 20)),
                            new Regex(
                                array(
                                    'pattern' => '/^(?=.*[a-z])/',
                                    'message' => 'Este valor debería tener 1 letra minuscula o más.',
                                )
                            ),
                            new Regex(
                                array(
                                    'pattern' => '/^(?=.*[A-Z])/',
                                    'message' => 'Este valor debería tener 1 letra mayuscula o más.',
                                )
                            ),
                            new Regex(
                                array(
                                    'pattern' => '/^(?=.*[0-9])/',
                                    'message' => 'Este valor debería tener 1 número o más.',
                                )
                            ),
                        ),
                    )
                );
        } elseif ($currentUser === $data) {
            $builder
                ->add('name')
                ->add('lastName')
                ->add(
                    'username',
                    null,
                    array('constraints' => array(new NotBlank(), new Length(array('min' => 4, 'max' => 20))))
                )
                ->add(
                    'email',
                    null,
                    array(
                        'constraints' => array(
                            new NotBlank(),
                            new Length(array('min' => 10, 'max' => 50)),
                            new Email(),
                        ),
                    )
                )
                ->add(
                    'enabled',
                    ChoiceType::class,
                    array(
                        'disabled' => true,
                        'label' => 'Status',
                        'choices' => array('Enabled' => true, 'Disabled' => false),
                    )
                )
                ->add(
                    'rol',
                    ChoiceType::class,
                    array(
                        'disabled' => true,
                        'mapped' => false,
                        'data' => $rol,
                        'choices' => array('User' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'),
                    )
                );

            if ('PUT' === $builder->getMethod()) {
                $builder
                    ->add(
                        'plain_password',
                        RepeatedType::class,
                        array(
                            'type' => PasswordType::class,
                            'invalid_message' => 'Password invalid message',
                            'options' => array('attr' => array('class' => 'password-field')),
                            'first_options' => array('label' => 'New password'),
                            'second_options' => array('label' => 'Repeat password'),
                            'constraints' => array(
                                new Length(array('min' => 8, 'max' => 20)),
                                new Regex(
                                    array(
                                        'pattern' => '/^(?=.*[a-z])/',
                                        'message' => 'Este valor debería tener 1 letra minuscula o más.',
                                    )
                                ),
                                new Regex(
                                    array(
                                        'pattern' => '/^(?=.*[A-Z])/',
                                        'message' => 'Este valor debería tener 1 letra mayuscula o más.',
                                    )
                                ),
                                new Regex(
                                    array(
                                        'pattern' => '/^(?=.*[0-9])/',
                                        'message' => 'Este valor debería tener 1 número o más.',
                                    )
                                ),
                            ),
                        )
                    )
                    ->add(
                        'currentPassword',
                        PasswordType::class,
                        array('mapped' => false, 'constraints' => array(new NotBlank(), new UserPassword()))
                    );

                $builder->addEventSubscriber(new UploadFileSubscriber());
            }
        } else {
            $builder
                ->add('name', null, array('disabled' => true))
                ->add('lastName', null, array('disabled' => true))
                ->add('username', null, array('disabled' => true))
                ->add('email', null, array('disabled' => true))
                ->add(
                    'enabled',
                    ChoiceType::class,
                    array('label' => 'Status', 'choices' => array('Enabled' => true, 'Disabled' => false))
                )
                ->add(
                    'rol',
                    ChoiceType::class,
                    array(
                        'mapped' => false,
                        'data' => $rol,
                        'choices' => array('User' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'),
                    )
                );
        }

        $builder->addEventSubscriber(new UserSubscriber($this->userManager));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\User',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }
}
