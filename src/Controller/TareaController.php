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
 * @Route(path="/")
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

        if(empty($data) || is_null($data)){
            throw new NotFoundHttpException('No se pudo crear la tarea. No se recibieron datos.');
        }

        $titulo = $data['titulo'];
        $descripcion = $data['descripcion'];
        $userId = $data['userId'];
        $user = new User();

        if(empty($titulo) || empty($descripcion) || empty($userId)){
            throw new NotFoundHttpException('No se pudo crear la tarea. Hay datos vacios que son de caracter obligatorio.');
        }
        
        $user = $this->userRepository->findOneById($userId);

        //me devuelve la tarea creada
        $nueva_tarea = $this->tareaRepository->saveTarea($titulo, $descripcion, $user);

        $data = [
            'status' => 'Tarea creada!',
            'nueva_tarea' => [
                'id' => $nueva_tarea->getId(),
                'titulo' => $nueva_tarea->getTitulo(),
                'descripcion' => $nueva_tarea->getDescripcion(),
                'terminada' => $nueva_tarea->isTerminada(),
                'fechaCreacion' => date('d-m-Y H:i', $nueva_tarea->getCreatedAt()->getTimestamp()),
                'user' => $nueva_tarea->getUser()->getName(),
            ]
        ]; 

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
      * @Route("tarea/{id}", name="get_one_tarea", methods={"GET"})
      */
    public function getOneTarea($id): JsonResponse
    {
        $tarea = $this->tareaRepository->findOneById($id);

        if(empty($tarea) || is_null($tarea) || is_null($id)){
            throw new NotFoundHttpException('No existe esa tarea.');
        }

        $data = [
            'id' => $tarea->getId(),
            'titulo' => $tarea->getTitulo(),
            'descripcion' => $tarea->getDescripcion(),
            'terminada' => $tarea->isTerminada(),
            'fechaCreacion' => date('d-m-Y H:i',$tarea->getCreatedAt()->getTimestamp()),
            'user' => $tarea->getUser()->getName(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
      * @Route("tareas/{userId}", name="get_all_tareas", methods={"GET"})
      */
    public function getAllTareas($userId): JsonResponse
    {
        if(empty($userId) || is_null($userId)){
            throw new NotFoundHttpException('Usuario invalido.');
        }

        $tareas = $this->tareaRepository->findByTareasUser($userId);
        $data = [];

        if( empty($tareas) || is_null($tareas)){
            throw new NotFoundHttpException('Sin tareas.');
        }

        foreach ($tareas as $tarea) {
            $data[] = [
                'id' => $tarea->getId(),
                'titulo' => $tarea->getTitulo(),
                'descripcion' => $tarea->getDescripcion(),
                'terminada' => $tarea->isTerminada(),
                'fechaCreacion' => date('d-m-Y H:i', $tarea->getCreatedAt()->getTimestamp()),
                'user' => $tarea->getUser()->getName(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
      * @Route("tarea/{id}", name="update_tarea", methods={"PUT"})
      */
    public function updateTarea($id, Request $request): JsonResponse
    {
        $tarea = $this->tareaRepository->findOneById($id);
        $data = json_decode($request->getContent(), true);
        
        if(empty($tarea) || is_null($tarea)){
            throw new NotFoundHttpException('La tarea no existe o no se encuentra.');
        }

        empty($data['titulo']) ? true : $tarea->setTitulo($data['titulo']);
        empty($data['descripcion']) ? true : $tarea->setDescripcion($data['descripcion']);
        empty($data['terminada']) ? true : $tarea->setTerminada($data['terminada']);

        $this->tareaRepository->updateTarea($tarea);

        return new JsonResponse(['status' => 'Tarea modificada!'], Response::HTTP_OK);
    }

    /**
      * @Route("tarea/{id}", name="delete_tarea", methods={"DELETE"})
      */
    public function deleteTarea($id): JsonResponse
    {
        $tarea = $this->tareaRepository->findOneById($id);

        if(empty($tarea) || is_null($tarea)){
            throw new NotFoundHttpException('La tarea no existe o no se encuentra.');
        }

        $this->tareaRepository->removeTarea($tarea);

        return new JsonResponse(['status' => 'Tarea eliminada!'], Response::HTTP_OK);
    }

}
