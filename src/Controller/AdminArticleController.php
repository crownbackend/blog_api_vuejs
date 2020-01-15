<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/admin")
 * Class AdminArticleController
 * @package App\Controller
 */
class AdminArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="admin_articles", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @param JWTEncoderInterface $JWTEncoder
     * @param Request $request
     * @return JsonResponse
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     */
    public function index(ArticleRepository $articleRepository, JWTEncoderInterface $JWTEncoder, Request $request): JsonResponse
    {
        $token = $request->headers->get('authorization');
        $role = $JWTEncoder->decode($token);
        if($role['roles']['0'] == 'ROLE_ADMIN') {
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
            return $this->json($data);
        } else {
            return $this->json(['Not autorisé'], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/articles", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function new(Request $request): JsonResponse
    {
        return $this->json(['toto']);
    }
}