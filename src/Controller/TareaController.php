<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TareaRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * 
 * @Route(path="/api/")
 */
class TareaController extends AbstractController
{
    private $tareaRepository;
    private $userRepository;

    public function __construct(TareaRepository $tareaRepository, UserRepository $userRepository)
    {
        $this->tareaRepository=$tareaRepository;
        $this->userRepository=$userRepository;
    }
        
    /**
      * @Route("tarea", name="add_tarea", methods={"POST"})
      */
    public function createTarea(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $titulo = $data['titulo'];
        $descripcion = $data['descripcion'];
        $userId = 1;
        $user = new User();

        if(empty($titulo) || empty($descripcion)){
            throw new NotFoundHttpException('Hay datos vacios que son de caracter obligatorio.');
        }
        
        $user = $this->userRepository->findOneById($userId);
        $this->tareaRepository->saveTarea($titulo, $descripcion, $user);

        return new JsonResponse(['status' => 'Tarea creada!'], Response::HTTP_CREATED);
    }

    /**
      * @Route("tarea/{id}", name="get_one_tarea", methods={"GET"})
      */
    public function getOneTarea($id): JsonResponse
    {
        $tarea = $this->tareaRepository->findOneById($id);
        $data = [
            'id' => $tarea->getId(),
            'titulo' => $tarea->getTitulo(),
            'descripcion' => $tarea->getDescripcion(),
            'terminada' => $tarea->isTerminada(),
            'fechaCreacion' => date('d-m-Y H:i',$tarea->getCreatedAt()->getTimestamp()),
            'user' => $tarea->getUser()->getNombre(),
        ];

        if(empty($data) || is_null($id)){
            throw new NotFoundHttpException('No existe esa tarea.');
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
      * @Route("tareas", name="get_all_tareas", methods={"GET"})
      */
    public function getAllTareas(Request $request): JsonResponse
    {

        return new JsonResponse();
    }
    
}
