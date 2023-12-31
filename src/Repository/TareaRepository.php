<?php

namespace App\Repository;

use App\Entity\Tarea;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tarea>
 *
 * @method Tarea|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tarea|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tarea[]    findAll()
 * @method Tarea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TareaRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Tarea::class);
        $this->manager = $manager;
    }

    public function saveTarea($titulo, $descripcion, $user): ?Tarea
    {
        $tarea = new Tarea();

        $tarea->setTitulo($titulo);
        $tarea->setDescripcion($descripcion);
        $tarea->setTerminada(false);
        $tarea->setUser($user);

        $this->manager->persist($tarea);
        $this->manager->flush();

        return $tarea;
    }

    public function updateTarea(Tarea $tarea): ?Tarea
    {
        $this->manager->persist($tarea);
        $this->manager->flush();

        return $tarea;
    }

    public function removeTarea(Tarea $tarea)
    {
        $this->manager->remove($tarea);
        $this->manager->flush();
    }

    public function findOneById($id): ?Tarea
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

   /**
    * @return Tarea[] Returns an array of Tarea objects
    */
   public function findByTareasUser($userId): array
   {
        return $this->createQueryBuilder('t')
            ->join('t.user', 'u')
            ->where('u.id = :val')
            ->setParameter('val', $userId)
            ->orderBy('t.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
   }
}
