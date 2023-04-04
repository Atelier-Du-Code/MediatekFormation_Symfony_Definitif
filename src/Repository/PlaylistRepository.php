<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function add(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    const ID = 'p.id';
    const PLAYLIST_NAME = 'p.name';
    const CATEGORIE_NAME = 'c.name';
    const CHAMP_RESULTAT_CATEGORIENAME = 'categoriename';    
    const PLAYLIST_FORMATION = 'p.formations';
    const FORMATION_CATEGORIE = 'f.categories';
    
   
    
    /**
     * Retourne toutes les playlists triées sur un champ
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderBy($champ, $ordre): array{
        return $this->createQueryBuilder('p')
                ->select(self::ID)
                ->addSelect(self::PLAYLIST_NAME)
                ->addSelect( self::CATEGORIE_NAME.' '.self::CHAMP_RESULTAT_CATEGORIENAME)
                ->leftjoin(self::PLAYLIST_FORMATION, 'f')
                ->leftjoin(self::FORMATION_CATEGORIE, 'c')
                ->groupBy(self::ID)
                ->addGroupBy( self::CATEGORIE_NAME)
                ->orderBy('p.'.$champ, $ordre)
                ->addOrderBy( self::CATEGORIE_NAME)
                ->getQuery()
                ->getResult();       
    }

    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table si $champ dans une autre table
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur, $table=""): array{
        if($valeur==""){
            return $this->findAllOrderBy('name', 'ASC');
        }    
        if($table==""){      
            return $this->createQueryBuilder('p')
                    ->select(self::ID)
                    ->addSelect(self::PLAYLIST_NAME)
                    ->addSelect( self::CATEGORIE_NAME.' '.self::CHAMP_RESULTAT_CATEGORIENAME)
                    ->leftjoin(self::PLAYLIST_FORMATION, 'f')
                    ->leftjoin(self::FORMATION_CATEGORIE, 'c')
                    ->where('p.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy(self::ID)
                    ->addGroupBy( self::CATEGORIE_NAME)
                    ->orderBy(self::PLAYLIST_NAME, 'ASC')
                    ->addOrderBy( self::CATEGORIE_NAME)
                    ->getQuery()
                    ->getResult();              
        }else{   
            return $this->createQueryBuilder('p')
                    ->select(self::ID)
                    ->addSelect(self::PLAYLIST_NAME)
                    ->addSelect( self::CATEGORIE_NAME.' '.self::CHAMP_RESULTAT_CATEGORIENAME)
                    ->leftjoin(self::PLAYLIST_FORMATION, 'f')
                    ->leftjoin(self::FORMATION_CATEGORIE, 'c')
                    ->where('c.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy(self::ID)
                    ->addGroupBy( self::CATEGORIE_NAME)
                    ->orderBy(self::PLAYLIST_NAME, 'ASC')
                    ->addOrderBy( self::CATEGORIE_NAME)
                    ->getQuery()
                    ->getResult();              
            
        }           
    }    


    
}
