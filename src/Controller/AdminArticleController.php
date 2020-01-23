<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    public function index(ArticleRepository $articleRepository,
                          JWTEncoderInterface $JWTEncoder, Request $request): JsonResponse
    {
        $token = $request->headers->get('authorization');
        $role = $JWTEncoder->decode($token);
        if($role['roles']['0'] == 'ROLE_ADMIN') {
            // get all articles
            $articles = $articleRepository->findBy([], ['createdAt' => "DESC"]);
            return $this->json($articles, Response::HTTP_OK, [], ["groups" => "article"]);
        } else {
            return $this->json('accès non autorisé', Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/article/{id}", name="admin_article_show", methods={"GET"})
     * @param Request $request
     * @param JWTEncoderInterface $JWTEncoder
     * @param int $id
     * @param ArticleRepository $articleRepository
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function show(Request $request, JWTEncoderInterface $JWTEncoder,
                         int $id, ArticleRepository $articleRepository): JsonResponse
    {
        $token = $request->headers->get('authorization');
        $role = $JWTEncoder->decode($token);
        if($role['roles']['0'] == 'ROLE_ADMIN') {
            $article = $articleRepository->findOneBy(["id" => (int)$id]);
            return $this->json($article, Response::HTTP_OK, [], ["groups" => "article"]);
        } else {
            return $this->json('accès non autorisé', Response::HTTP_FORBIDDEN);
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
    public function new(Request $request, JWTEncoderInterface $JWTEncoder,
                        CategoryRepository $categoryRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $role = $JWTEncoder->decode($request->request->get('authorization'));
        if($role['roles']['0'] == 'ROLE_ADMIN') {
            $article = new Article();
            $this->upload($request, $article);

            $category = $categoryRepository->findOneBy(['id' => $request->request->get('category')]);
            $article->setTitle($request->request->get('title'));
            $article->setDescription($request->request->get('description'));
            $article->setPublished((int)$request->request->get('published'));
            $article->setCategory($category);
            $em->persist($article);
            $em->flush();
            return $this->json(['created' => 1], Response::HTTP_CREATED);
        } else {
            return $this->json('accès non autorisé', Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/article/{id}", name="admin_edit_article", methods={"PUT"})
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param JWTEncoderInterface $JWTEncoder
     * @param int $id
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function edit(Request $request, ArticleRepository $articleRepository,
                         JWTEncoderInterface $JWTEncoder, int $id, CategoryRepository $categoryRepository): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $token = $request->headers->get('authorization');
        $role = $JWTEncoder->decode($token);
        if($role['roles']['0'] == 'ROLE_ADMIN') {
            $article = $articleRepository->findOneBy(["id" => (int)$id]);
            $this->upload($request, $article);

            $category = $categoryRepository->findOneBy(['id' => $request->request->get('category')]);
            $article->setTitle($request->request->get('title'));
            $article->setDescription($request->request->get('description'));
            $article->setPublished((int)$request->request->get('published'));
            $article->setCategory($category);
            $em->persist($article);
            $em->flush();
            return $this->json(['updated' => 1], Response::HTTP_OK);
        } else {
            return $this->json('accès non autorisé', Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/article/{id}", name="admin_edit_delete", methods={"DELETE"})
     * @param Request $request
     * @param int $id
     * @param ArticleRepository $articleRepository
     * @param JWTEncoderInterface $JWTEncoder
     * @return JsonResponse
     * @throws JWTDecodeFailureException
     */
    public function delete(Request $request, int $id,
                           ArticleRepository $articleRepository, JWTEncoderInterface $JWTEncoder): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $token = $request->headers->get('authorization');
        $role = $JWTEncoder->decode($token);
        if($role['roles']['0'] == 'ROLE_ADMIN') {
            $article = $articleRepository->findOneBy(["id" => (int)$id]);
            $em->remove($article);
            $em->flush();
            return $this->json(['delete' => 1], Response::HTTP_NO_CONTENT);
        } else {
            return $this->json('accès non autorisé', Response::HTTP_FORBIDDEN);
        }
    }

    private function upload(Request $request, Article $article)
    {
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
    }
}