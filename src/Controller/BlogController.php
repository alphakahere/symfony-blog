<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    public function add()
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
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
