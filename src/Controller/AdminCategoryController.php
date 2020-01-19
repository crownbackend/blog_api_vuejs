<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/admin")
 * Class AdminCategoryController
 * @package App\Controller
 */
class AdminCategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="admin_categories")
     * @param Request $request
     * @param JWTEncoderInterface $JWTEncoder
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     */
    public function category(Request $request, JWTEncoderInterface $JWTEncoder, CategoryRepository $categoryRepository): JsonResponse
    {
        $token = $request->headers->get('authorization');
        $role = $JWTEncoder->decode($token);
        if($role['roles']['0'] == 'ROLE_ADMIN') {
            $data = [];
            $categories = $categoryRepository->findAll();
            foreach ($categories as $category) {
                $data[] = [
                    'id' => $category->getId(),
                    'name' => $category->getName()
                ];
            }
            return $this->json($data, Response::HTTP_OK);
        } else {
            return $this->json('accès non autorisé', Response::HTTP_FORBIDDEN);
        }
    }
}