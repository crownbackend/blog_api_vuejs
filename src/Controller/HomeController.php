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
     * @Route("/", name="home", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @return JsonResponse
     */
    public function home(ArticleRepository $articleRepository): JsonResponse
    {
        // get all articles
        $articles = $articleRepository->findBy(['Published' => true]);
        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'description' => $article->getDescription(),
                'image_name' => $article->getImageName(),
                'published' => $article->getPublished(),
                'created_at' => $article->getCreatedAt(),
                'category' => [
                    'id' => $article->getCategory()->getId(),
                    'name' => $article->getCategory()->getName()
                ]
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}