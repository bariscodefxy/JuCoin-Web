<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class HomeController extends AbstractController
{

	private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

	public function home(RequestStack $requestStack): Response
	{
		$session = $this->requestStack->getSession();

		if( $session->get('token') )
		{
			return $this->redirectToRoute( 'app_user' );
		}

		return $this->render( 'home.html.twig', [] );
	}

}