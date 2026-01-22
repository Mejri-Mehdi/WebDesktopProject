<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_USER')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_dashboard')]
    public function index(): Response
    {
        return $this->render('client/dashboard.html.twig');
    }

    #[Route('/profile', name: 'app_client_profile')]
    public function profile(): Response
    {
        return $this->render('client/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/appointments', name: 'app_client_appointments')]
    public function appointments(): Response
    {
        return $this->render('client/appointments.html.twig');
    }

    #[Route('/tickets', name: 'app_client_tickets')]
    public function tickets(): Response
    {
        return $this->render('client/tickets.html.twig');
    }
}
