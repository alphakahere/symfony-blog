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
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
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
            if($article->getPicture() !== null){
               $file = $form->get('picture')->getData();
               $filename = uniqid().'.'.$file->guessExtension();
               try {
                $file->move($this->getParameter('images_directory'), $filename);
               } catch(FileException $e) {
                return new Response($e->getMessage());
               }
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

    public function show($url)
    {
        return $this->render('/blog/show.html.twig', [
            'controller_name' => 'BlogController',
            'slug' => $url
        ]);
    }

    public function edit($id)
    {
        return  $this->render('/blog/edit.html.twig', [
            'id' => $id
        ]);
    }

    public function remove()
    {
        return new Response('<h1>Article supprimé</h1>');
    }
}
