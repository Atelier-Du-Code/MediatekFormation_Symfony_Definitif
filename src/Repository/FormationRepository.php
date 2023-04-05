<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function add(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retourne toutes les formations triées sur un champ se trouvant sur une autre table que formation
     * @param type $champ
     * @param type $ordre
     * @param type $table si $champ dans une autre table
     * @return Formation[]
     */
    public function findAllOrderByChampOrdreTable($champ, $ordre, $table): array{           
            return $this->createQueryBuilder('f')
                    ->join('f.'.$table, 't')
                    ->orderBy('t.'.$champ, $ordre)
                    ->getQuery()
                    ->getResult(); 
    }
    
    /**
     * Retourne toutes les formations triées sur un champ se trouvant dans la table formation
     * @param type $champ
     * @param type $ordre
     * @param type $table si $champ dans une autre table
     * @return array
     */
    public function findAllOrderByChampOrdre($champ, $ordre): array{           
            return $this->createQueryBuilder('f')
                    ->orderBy('f.'.$champ, $ordre)
                    ->getQuery()
                    ->getResult();  
    }
    

    const FORMATION_PUBLISHEDAT = 'f.publishedAt';
   
     /**
     * Retourne toutes les formations dont un champ en dehors de la table formation contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table
     * @return Formation[]
     */
    public function findByContainValueHorsChampFormation($champ, $valeur, $table): array{        
        
        if($valeur==""){
            return $this->findAll();
        }
            return $this->createQueryBuilder('f')
                        ->join('f.'.$table, 't')                    
                        ->where('t.'.$champ.' LIKE :valeur')
                        ->orderBy(self::FORMATION_PUBLISHEDAT, 'DESC')
                        ->setParameter('valeur', '%'.$valeur.'%')
                        ->getQuery()
                        ->getResult();     
    }  
    
    /**
    * Retourne toutes les formations dont un champ de la table formation contient une valeur
    * ou tous les enregistrements si la valeur est vide
    * @param type $champ
    * @param type $valeur    
    * @return Formation[]
    */
    public function findByContainValueChampFormation($champ, $valeur): array{ 
        if($valeur==""){
            return $this->findAll();
        }       
            return $this->createQueryBuilder('f')
                    ->where('f.'.$champ.' LIKE :valeur')
                    ->orderBy(self::FORMATION_PUBLISHEDAT, 'DESC')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->getQuery()
                    ->getResult();     
    }      
    
    /**
     * Retourne les n formations les plus récentes
     * @param type $nb
     * @return Formation[]
     */
    public function findAllLasted($nb) : array {
        return $this->createQueryBuilder('f')
                ->orderBy(self::FORMATION_PUBLISHEDAT, 'DESC')
                ->setMaxResults($nb)     
                ->getQuery()
                ->getResult();
    }    
    
    /**
     * Retourne la liste des formations d'une playlist
     * @param type $idPlaylist
     * @return array
     */
    public function findAllForOnePlaylist($idPlaylist): array{
        return $this->createQueryBuilder('f')
                ->join('f.playlist', 'p')
                ->where('p.id=:id')
                ->setParameter('id', $idPlaylist)
                ->orderBy(self::FORMATION_PUBLISHEDAT, 'ASC')   
                ->getQuery()
                ->getResult();        
    }
    /**
     * Retourne le nombre de formations d'une playlist
     * @param type int $idPlaylist
     * @return int
     */
    public function findCountForOnePlaylist($idPlaylist): int{
        return $this->createQueryBuilder('f')
                ->select('COUNT(f.title)')
                ->join('f.playlist', 'p')
                ->where('p.id=:id')
                ->setParameter('id', $idPlaylist)                
                ->getQuery()
                ->getSingleScalarResult();        
    }
}
