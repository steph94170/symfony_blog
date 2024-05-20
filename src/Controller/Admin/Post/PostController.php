<?php

namespace App\Controller\Admin\Post;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/post/list', name: 'admin_post_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/admin/post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/post/create', name: 'admin_post_create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('pages/admin/post/create.html.twig',);
    }


}
