<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticleType;

class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {

        $em = $doctrine->getManager();

        $articles = $em->getRepository(Article::class)->findAll();


        $session = $request->getSession();
        $notification = $session->get('notification');
        $type_notif = $session->get('type_notif');

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'notification' => $notification,
            'type_notif' => $type_notif
        ]);
    }

    #[Route('/add-article', name: 'add_articles')]
    public function addArticle(ManagerRegistry $doctrine, Request $request): Response
    {

        $em = $doctrine->getManager();

        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData(); // Permet de mettre dans l'objet $article les valeur saisies dans le formulaire

            $article->setBody(nl2br($article->getBody()));

            $em->persist($article); // Doctrine va gérer cet objet

            $em->flush();

            $session = $request->getSession();
            $session->set('notification', "Article ajouté avec succès !");
            $session->set('type_notif', "alert-success");

            return $this->redirectToRoute('app_articles');
        }

        return $this->render('article/addArticle.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/edit-article/{id_article}', name: 'edit_article')]
    public function editArticle(ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        $articles = $em->getRepository(Article::class)->findAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/view-article/{id_article}', name: 'view_article')]
    public function viewArticle(ManagerRegistry $doctrine, $id_article): Response
    {

        $em = $doctrine->getManager();

        $article = $em->getRepository(Article::class)->find($id_article);

        return $this->render('article/viewArticle.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/delete-article/{id_article}', name: 'delete_article')]
    public function deleteArticle(ManagerRegistry $doctrine, $id_article, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $article = $entityManager->getRepository(Article::class)->find($id_article);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article found for id ' . $id_article
            );
        }

        $entityManager->remove($article);
        $entityManager->flush();

        $session = $request->getSession();
        $session->set('notification', "Article supprimé avec succès !");
        $session->set('type_notif', "alert-success");
        return $this->redirectToRoute('app_articles');
    }
}
