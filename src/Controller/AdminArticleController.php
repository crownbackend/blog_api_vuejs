<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * Class AdminArticleController
 * @package App\Controller
 */
class AdminArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="admin_article_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param ArticleRepository $articleRepository
     * @return JsonResponse
     */
    public function index(ArticleRepository $articleRepository): JsonResponse
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
                'category' => [
                    'id' => $article->getCategory()->getId(),
                    'name' => $article->getCategory()->getName()
                ]
            ];
        }
        return $this->json($data);
    }
}