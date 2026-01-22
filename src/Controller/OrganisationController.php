<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/organisation')]
class OrganisationController extends AbstractController
{
    #[Route('/', name: 'app_organisation_dashboard')]
    public function index(): Response
    {
        // Ensure the user is an organisation
        $user = $this->getUser();
        if ($user->getType() !== 'Organisation') {
            throw $this->createAccessDeniedException('Access denied.');
        }

        return $this->render('organisation/dashboard.html.twig');
    }
}
