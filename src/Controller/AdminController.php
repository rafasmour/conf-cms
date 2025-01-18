<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{

    #[Route(path:"/admin/" , name:"app_admin_index")]
    public function index(){
        return $this->render("/admin/adminPanel/index.html.twig");
    }
    #[Route('/admin/login', name: 'app_admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        if(!empty($user)){
            return $this->redirectToRoute('app_admin_index');
        }    
        return $this->forward(
            'App\Controller\SecurityController::login',
            ['authenticationUtils' => $authenticationUtils]
        );
    }
    #[Route('/admin/logout', name: 'app_admin_logout')]
    public function logout(): Response
    {
        return $this->forward(
            'App\Controller\SecurityController::logout'
        );
    }
}
