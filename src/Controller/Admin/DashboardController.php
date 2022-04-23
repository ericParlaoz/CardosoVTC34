<?php

namespace App\Controller\Admin;

use App\Entity\Clients;
use App\Repository\ClientsRepository;
use App\Repository\ContactRepository;
use App\Repository\CourseRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response

    {

            return $this->render('administrator/index.html.twig', [
            ]);
    }

    #[Route('/admin/clients', name: 'admin_clients')]
    public function clientListes(ClientsRepository $clientsRepository): Response
    {
        return $this->render('administrator/clients/index.html.twig', [
            'clients' => $clientsRepository->findAll(),
        ]);
    }



    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Site');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('The Label', 'fas fa-list', Clients::class);
    }
}
