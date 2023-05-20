<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function add(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * Retourne la liste des catégories des formations d'une playlist
     * @param type $idPlaylist
     * @return array
     */
    public function findAllForOnePlaylist($idPlaylist): array{
        return $this->createQueryBuilder('c')
                ->join('c.formations', 'f')
                ->join('f.playlist', 'p')
                ->where('p.id=:id')
                ->setParameter('id', $idPlaylist)
                ->orderBy('c.name', 'ASC')   
                ->getQuery()
                ->getResult();        
    }  
    
    /**
     * Retourne toutes les catégories triées par ordre alphabétique croissant ou décroissant 
     * @param type $ordre
     * @return type
     */
    public function findByAllOrderBy($ordre)
    {
        return $this->createQueryBuilder('c')
                ->orderBy('c.name', $ordre)
                ->getQuery()
                ->getResult();
    }
    
    /**
     * Retourne le nombre de formations pour la catégorie passée en paramètre
     * @param type $idCategorie
     * @return int
     */
    public function findByAllFormationsOneCategorie($idCategorie): int
{
    return $this->createQueryBuilder('c')
        ->select('COUNT(f)')
        ->join('c.formations', 'f')
        ->where('c.id = :id')
        ->setParameter('id', $idCategorie)
        ->getQuery()
        ->getSingleScalarResult();
}

    
    /**
     * Renvoie le nombre de catégorie ayant un nom égal au paramètre valeur
     * @param type $valeur
     * @return int
     */
    public function findByDoublonsCategorie($valeur):int
    {
        return $this->createQueryBuilder('c')
                ->select('count(c.name)')
                ->where('c.name LIKE :valeur')
                ->setParameter('valeur', '%'.$valeur.'%')                
                ->getQuery()
                ->getSingleScalarResult(); //Renvoie seulement le nombre d'enregistrements trouvé
    }
}

