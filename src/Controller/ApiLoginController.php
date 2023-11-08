<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiLoginController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository=$userRepository;   
    }

    /**
     * @Route("/api/login", name="app_api_login", methods={"POST"})
     */
    public function loginApi(Request $request, ?User $user, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if(empty($data) || is_null($data)){
            throw new NotFoundHttpException('No se recibieron datos.');
        }
        $email = $data['email'];
        $password = $data['password'];
        $user = $this->userRepository->findOneByEmail($email);
        if (null === $user) {
            $userError = [
                'error' => 'El mail no pertenece a un usuario.',
            ];
            return new JsonResponse($userError, Response::HTTP_UNAUTHORIZED);
        }
        $passwordIsValid = $passwordHasher->isPasswordValid($user, $password);
        if($passwordIsValid){
            $token = 'generar token'; // somehow create an API token for $user
            $user = [
                'userId'  => $user->getId(),
                'userName'  => $user->getName(),
                'userEmail'  => $user->getUserIdentifier(),
                'token' => $token,
            ];
            return new JsonResponse($user, Response::HTTP_OK);
        }else{
            $userError = [
                'error' => 'Usuario o clave incorrectos',
            ];
            return new JsonResponse($userError, Response::HTTP_FORBIDDEN);
        }
    }
}
