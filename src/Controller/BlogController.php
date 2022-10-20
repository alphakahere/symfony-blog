<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    public function index(): Response
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findBy(
            ['isPublished' => true],
            ['publicationDate' => 'desc']
        );

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    public function add(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setLastUpdateDate(new \DateTime());
            if ($article->getIsPublished()) {
                $article->setPublicationDate(new \DateTime());
            }
            if ($article->getPicture() !== null) {
                $file = $form->get('picture')->getData();
                $filename = uniqid().'.'.$file->guessExtension();
                try {
                    $file->move($this->getParameter('images_directory'), $filename);
                } catch(FileException $e) {
                    return new Response($e->getMessage());
                }
                $article->setPicture($filename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return new Response('L\'article a bien ete enregistrer');
        }
        return    $this->render('/blog/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function show(Article $article)
    {
        return $this->render('/blog/show.html.twig', [
            'controller_name' => 'BlogController',
            'article' => $article
        ]);
    }

    public function edit(Article $article, Request $request)
    {
        $oldPicture = $article->getPicture();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($article->getIsPublished()) {
                $article->setPublicationDate(new \DateTime());
            }
            $article->setLastUpdateDate(new \DateTime());

            if ($article->getPicture() !== null && $article->getPicture() !== $oldPicture) {
                $file = $form->get('picture')->getData();
                $filename = uniqid().'.'.$file->guessExtension();

                try {
                    $file->move($this->getParameter('images_directory'), $filename);
                } catch(FileException $e) {
                    return new Response($e->getMessage());
                }

                $article->setPicture($filename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
        }

        return  $this->render('/blog/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView()
        ]);
    }

    public function remove()
    {
        return new Response('<h1>Article supprimé</h1>');
    }
}
