<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Lotto;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class LottoController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/user/lotto', name: 'app_lotto')]
    public function lotto(Request $request, ManagerRegistry $doctrine): Response
    {
        $session = $this->requestStack->getSession();

        if( !$session->get('token') )
        {
            return $this->redirectToRoute( 'app_login' );
        }

        $repository = $doctrine->getRepository(Lotto::class);

        $user = $doctrine->getRepository(User::class)->findOneBy(['token' => $session->get('token')]);

        $lottos = $repository->findAll();

        return $this->render( 'user/lotto/index.html.twig', [
            'lottos' => $lottos,
            'user'  => $user
        ] );
    }

    #[Route('/user/lotto/buy', name: 'app_lottobuy')]
    public function lottobuy(Request $request, ManagerRegistry $doctrine): Response
    {
        $session = $this->requestStack->getSession();
        $entityManager = $doctrine->getManager();

        if( !$session->get('token') )
        {
            return $this->redirectToRoute( 'app_login' );
        }

        $id = $request->query->get('id');

        if( !$id )
        {
            return new Response("You should give id parameter.", 400);
        }

        $lotto = $doctrine->getRepository(Lotto::class)->find($id);

        if( !$lotto )
        {
            return new Response("Lotto type not found.", 400);
        }

        $price = $lotto->getPrice();

        $user = $entityManager->getRepository(User::class)->findOneBy(['token' => $session->get('token')]);

        if ( $user->getCoins() < $price )
        {
            return $this->render( 'user/lotto/buy.html.twig', [
                'error'  => 'You don\'t have enough coins.'
            ] );
        }

        $user->setCoins( $user->getCoins() - $price );

        $reward = $lotto->getRewards()[rand(0, sizeof($lotto->getRewards()) - 1)];

        $user->setCoins( $user->getCoins() + $reward );

        $entityManager->flush();

        return $this->render( 'user/lotto/buy.html.twig', [
            'reward' => $reward,
            'error'  => ''
        ] );
    }

    #[Route('/user/lotto/add_lotto', name: 'app_lottoadd')]
    public function lottoadd(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = $this->requestStack->getSession();

        if( !$session->get('token') )
        {
            return $this->redirectToRoute( 'app_login' );
        }

        $entityManager = $doctrine->getManager();

        $lotto = new Lotto();
        $lotto
            ->setRewards([1,2,5,7,8,9,10])
            ->setPrice(5);

        $entityManager->persist($lotto);

        $entityManager->flush();

        return new Response('success');
    }

}
