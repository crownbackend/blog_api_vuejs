<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/articles", name="home", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @return JsonResponse
     */
    public function home(ArticleRepository $articleRepository): JsonResponse
    {
        // get all articles
        $articles = $articleRepository->findBy(['Published' => 1], ['id' => "DESC"]);
        return $this->json($articles, Response::HTTP_OK, [], ["groups" => "article"]);
    }
}