<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    private ManagerRegistry $registry;
    public function __construct(ManagerRegistry $registry) {
        $this->registry = $registry;
    }
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createFormBuilder()
            ->add('username')
            ->add('password', type: RepeatedType::class, options: [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm password']
            ])
            ->add('email', type: EmailType::class)
            ->add('submit', type: SubmitType::class, options: [
                'attr' => [
                    'class' => 'btn btn-success float-right',
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()){
            $data = $form->getData();
            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $data['password'])
            );
            $user->setEmail($data['email']);

            $em = $this->registry->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('login'));
        }
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
