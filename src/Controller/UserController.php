<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;

class UserController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/user', name: 'app_user')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $session = $this->requestStack->getSession();

        if( !$session->get( 'token' ) )
        {
            return $this->redirectToRoute( 'app_login' );
        }

        $user = $doctrine->getRepository(User::class)->findOneBy(['token' => $session->get('token')]);

        if (!$user) {
            return $this->redirectToRoute('app_logout');
        }

        return $this->render('user/index.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/user/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        if( $session->get( 'token' ) )
        {
            return new Response( "You are already logined!" );
        }

        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('token', TextType::class)
            ->add('login', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $session->set( 'token', $user->getToken() );

            return $this->redirectToRoute( 'app_user' );
        }

        return $this->render( 'user/login.html.twig', [
            'form' => $form->createView()
        ] );
    }

    #[Route('/user/logout', name: 'app_logout')]
    public function logout(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        if( $session->get( 'token' ) )
        {
            $session->remove( 'token' );
        }

        return $this->redirectToRoute( 'index' );
    }

}
