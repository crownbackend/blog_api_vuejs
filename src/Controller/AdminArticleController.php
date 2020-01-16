<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
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
     * @throws JWTDecodeFailureException
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
            return $this->json($data, Response::HTTP_OK);
        } else {
            return $this->json('interdit', Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/articles", methods={"POST"})
     * @param Request $request
     * @param JWTEncoderInterface $JWTEncoder
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function new(Request $request, JWTEncoderInterface $JWTEncoder, CategoryRepository $categoryRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $role = $JWTEncoder->decode($request->request->get('authorization'));
        if($role['roles']['0'] == 'ROLE_ADMIN') {
            $article = new Article();
            $imageFile = $request->files->get('image');
            if($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $this->json($e->getMessage());
                }
                $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                $article->setImageName($baseurl.'/uploads/images/'.$newFilename);
            }

            $category = $categoryRepository->findOneBy(['id' => $request->request->get('category')]);
            $article->setTitle($request->request->get('title'));
            $article->setDescription($request->request->get('description'));
            $article->setPublished((int)$request->request->get('published'));
            $article->setCategory($category);
            $em->persist($article);
            $em->flush();
            return $this->json(['created' => 1], Response::HTTP_CREATED);
        } else {
            return $this->json('interdit', Response::HTTP_FORBIDDEN);
        }
    }
}