<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of FormationRepositoryTest
 *
 * @author Manon Avaullée
 */
class FormationRepositoryTest extends KernelTestCase {
  
    public function newFormation(): Formation
    {         
        $formation = (new Formation())
                ->setTitle("New York")              
                ->setPublishedAt(new DateTime("now"));    
        
        return $formation;               
    }
    
    public function recupRepository(): FormationRepository
    {
        self::bootKernel();
        $repository = self::getContainer()->get(FormationRepository::class);
        return $repository;
    }  
    
    
    /**
     * Permet de comparer un nombre d'enregistrement théorique et pratique
     * @param Formation $formation
     * @param int $nbErreursAttendues
     * @param string $message
     */
     public function assertErrors(Formation $formation, int $nbErreursAttendues, string $message="")
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error, $message);
    }
    
    /**
     * Vérifie le nombre total de formations dans la base de données
     */
    public function testNbFormations()
    {
        $repository = $this->recupRepository();
        $nbFormations = $repository->count([]);
        $this->assertEquals(6, $nbFormations);
    }
    
    /**
     * Vérifie qu'une formation peut être ajoutée sans erreur
     */
    public function testAddFormation()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $nbFormation = $repository->count([]);
        $repository->add($formation, true);
        $this->assertEquals($nbFormation + 1, $repository->count([]), "erreur lors de l'ajout");        
    }
    /**
     * Vérifie si les formations renvoyées triées sur un champ se trouvant dans la table formation sont réellement ordonnées 
     * par ordre aphabétique croissant et décroissant
     */
    public function testValidfindAllOrderByChampOrdre()
    {
        $repository = $this->recupRepository();  
        
        $formation = $repository->findAllOrderByChampOrdre("title", "ASC");
        $this->assertEquals("Android Studio (complément n°13) : Permissions",$formation[0]->getTitle(), 
                "Le tri par ordre alphaphabétique ASC sur le titre de la formation n'est pas fonctionnel");
        
        $formation = $repository->findAllOrderByChampOrdre("title", "DESC");
        $this->assertEquals("UML : Diagramme de paquetages",$formation[0]->getTitle(), 
                "Le tri par ordre alphaphabétique DESC sur le titre de la formation n'est pas fonctionnel");
    }
    
    /**
     * Vérifie si les formations triées sur un champ se trouvant dans une autre table que la table formation sont réellement 
     * ordonnées par ordre aphabétique croissant et décroissant
     */
    public function testValidfindAllOrderByChampOrdreTable()
    {
        $repository = $this->recupRepository();      
       
        
        $formation = $repository->findAllOrderByChampOrdreTable("name", "ASC", "playlist");
        $this->assertEquals("Android Studio (complément n°13) : Permissions",$formation[0]->getTitle(), 
                "Le tri par ordre alphaphabétique ASC sur le titre de la playlist d'une formation n'est pas fonctionnel");
        
        $formation = $repository->findAllOrderByChampOrdreTable("name", "DESC", "playlist");
        $this->assertEquals("C# : ListBox en couleur",$formation[0]->getTitle(), 
                "Le tri par ordre alphaphabétique DESC sur le titre de la playlist d'une formation n'est pas fonctionnel");
    }
    
    /**
     * Vérifie si une seule formation est trouvée lors d'une recherche de formation sur une valeur contenue 
     * dans un champ de la table formation
     */
    public function testValidFindByContainValueChampFormation()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $formations = $repository->findByContainValueChampFormation("title", "C# : ListBox en couleur");
        $nbFormations = count($formations);
        $this->assertEquals(1, $nbFormations, "Le titre de la formation n'est pas trouvé");  
    }
    
    /**
     * Vérifie qu'un titre de formation n'est pas trouvé si il n'existe pas dans la base de données 
     * lors d'une recherche sur une valeur contenue 
     * dans un champ de la table formation
     */
    public function testNonValidFindByContainValueChampFormation()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $formations = $repository->findByContainValueChampFormation("title", "Titre d'une formation qui n'existe pas");
        $nbFormations = count($formations);
        $this->assertEquals(0, $nbFormations, "Un titre de formation est trouvé");  
    }
    
    /**
     * Vérifie si une seule formation est trouvée lors d'une recherche de formation sur une valeur contenue 
     * dans une autre table que la table formation
     */
    public function testValidFindByContainValueChampHorsTableFormation()
    {
        $repository = $this->recupRepository();
                
        $formation = $this->newFormation();
        $repository->add($formation, true);       
        $formations = $repository->findByContainValueChampHorsTableFormation("name","Eclipse et Java", "playlist");  
        $nbFormations = count($formations);
        $this->assertEquals(2, $nbFormations, "Le titre de la formation n'est pas trouvé");  
    }
    
    
    /**
     * Vérifie qu'un titre de formation n'est pas trouvé si il n'existe pas dans la base de données lors d'une recherche sur une valeur contenue 
     * dans un champ d'une autre table que la table formation
     */
    public function testNonValidFindByContainValueChampHorsTableFormation()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $formations = $repository->findByContainValueChampHorsTableFormation("name","Playlist qui n'existe pas", "playlist");
        $nbFormations = count($formations);
        $this->assertEquals(0, $nbFormations, "Un titre de formation est trouvé");  
    }
    
    /**
     * Vérifie que la fonction renvoie bien un nombre spécifique des dernières formations
     */
    public function testValidFindAllLasted()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $formations = $repository->findAllLasted(3);
        $nbFormations = count($formations);
        $this->assertEquals(3, $nbFormations, "Les 3 dernières formations n'ont pas été trouvées");
    }
    
    /**
     * Vérifie que la fonction ne renvoie pas un nombre de formation supérieur à celui du nombre de formations dans la base de données
     */
    public function testNonValidFindAllLasted()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $formations = $repository->findAllLasted(8);
        $nbFormations = count($formations);
        $this->assertEquals(7, $nbFormations, "Le nombre de formations renvoyées est supérieur au nombre de formations de la base de données");
    }
    
    /**
     * Vérifie que la méthode ne renvoie que les formations associées à une playlist spécifique
     */
    public function testValidFindAllForOnePlaylist()
    {
         $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $formations = $repository->findAllForOnePlaylist("1");
        $nbFormations = count($formations);
        $this->assertEquals(2, $nbFormations, "La méthode renvoie des formations qui ne sont pas associées à cette playlist");
    }
    
    /**
     * Vérifie que la méthode ne renvoie que les formations associées à une playlist spécifique
     */
    public function testNonValidFindAllForOnePlaylist()
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $formations = $repository->findAllForOnePlaylist("30");
        $nbFormations = count($formations);
        $this->assertEquals(0, $nbFormations, "La méthode renvoie les formations d'une playlist qui n'existe pas");
    }
    
    /**
     * Vérifie que le recensement du nombre de formations d'une playlist est opérationnel
     */
    public function testValidFindCountForOnePlaylist()
    {
         $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);
        $formations = $repository->findCountForOnePlaylist("1");        
        $this->assertEquals(2, $formations, "La méthode renvoie pas le bon nombre de formations pour une playlist spécifique");
    }
}
