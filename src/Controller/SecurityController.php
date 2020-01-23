<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/check/login/verifiy/token", name="check_token", methods={"POST"})
     * @param Request $request
     * @param JWTEncoderInterface $JWTEncoder
     * @return JsonResponse
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     */
    public function checkToken(Request $request, JWTEncoderInterface $JWTEncoder): JsonResponse
    {
        $token = $request->headers->get('authorization');
        $tokenValid = $JWTEncoder->decode($token);
        if($tokenValid['username']) {
            return $this->json(["token valid" => 1, Response::HTTP_OK]);
        } else {
            return $this->json(["token not valid" => 0, Response::HTTP_BAD_REQUEST]);
        }
    }
}