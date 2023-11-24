<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $session = $request->getSession();

        $notification = $session->get('notification');
        $type_notif = $session->get('type_notif');

        $em = $doctrine->getManager();

        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # code...
            $em->persist($user);

            $em->flush();

            $session = $request->getSession();
            $session->set('notification', "User registé avec succès !");
            $session->set('type_notif', "alert-success");

            return $this->redirectToRoute('app_register');
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
            'type_notif' => $type_notif
        ]);
    }
}
