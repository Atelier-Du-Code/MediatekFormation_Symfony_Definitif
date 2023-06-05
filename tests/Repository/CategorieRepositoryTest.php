<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of CategorieRepositoryTest
 *
 * @author Manon Avaullée
 */
class CategorieRepositoryTest extends KernelTestCase {
    
    public function newCategorie () : Categorie
    {
        $categorie = (new Categorie())
                ->setName("Nom d'une catégorie");
        
        return $categorie;
    }
    
    public function recupRepository(): CategorieRepository
    {
        self::bootKernel();
        $repository = self::getContainer()->get(CategorieRepository::class);
        return $repository;
    }  
    
    /**
     * Permet de comparer un nombre d'enregistrement théorique et pratique
     * @param Categorie $categorie
     * @param int $nbErreursAttendues
     * @param string $message
     */
     public function assertErrors(Categorie $categorie, int $nbErreursAttendues, string $message="")
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($categorie);
        $this->assertCount($nbErreursAttendues, $error, $message);
    }
    
    /**
     * Vérifie le nombre total de categorie dans la base de données
     */
    public function testNbCategorie()
    {
        $repository = $this->recupRepository();
        $nbCategorie = $repository->count([]);
        $this->assertEquals(9, $nbCategorie);
    }
    
    /**
     * Vérifie que le tri par ordre alphabétique croissant et décroissant est opérationnel
     */
    public function testValidFindByAllOrderBy()
    {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        
        $categorie = $repository->findByAllOrderBy("ASC");        
        $this->assertEquals("Android", $categorie[0]->getName(),
                "Le tri par ordre alphaphabétique ASC sur le titre des catégories n'est pas fonctionnel");
        
        $categorie = $repository->findByAllOrderBy("DESC");        
        $this->assertEquals("UML", $categorie[0]->getName(),
                "Le tri par ordre alphaphabétique DESC sur le titre des catégories n'est pas fonctionnel");
        
    }
   
    /**
     * Vérifie que le recensement de toutes les catégories d'une playlist est opérationnel
     */
    public function testValidFindAllForOnePlaylist()
    {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $categorie = $repository->findAllForOnePlaylist(1);
        $nbCategorie = count($categorie);
        $this->assertEquals(2, $nbCategorie, "La récupération des catégories des formations d'une playlist n'est pas fonctionnelle");
    }
    
    /**
     * Vérifie que le recensement de toutes les formations d'une catégorie est opérationnel 
     */
    public function testValidFindByAllFormationsOneCategorie()
    {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $categorie = $repository->findByAllFormationsOneCategorie(1);
        $this->assertEquals(1, $categorie, "Le nombre de formation correspondant à cette catégorie est incorrect");
    }
    
    /**
     * Vérifie qu'une catégorie est déja présente dans la base de données
     */
    public function testNonValidFindByDoublonsCategorie()
    {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $categorie = $repository->findByDoublonsCategorie("Java");
        $this->assertEquals(1, $categorie, "Le doublons de la catégorie 'Java' n'a pas été repéré");
    }
    
    /**
     * Vérifie qu'une catégorie n'est pas encore présente dans la base de données
     */
    public function testValidFindByDoublonsCategorie()
    {
        $repository = $this->recupRepository();
        $categorie = $this->newCategorie();
        $repository->add($categorie, true);
        $categorie = $repository->findByDoublonsCategorie("Une autre catégorie non enregistrée dans la BDD");
        $this->assertEquals(0, $categorie, "Un doublon n'existant pas a été détecté");
    }
    
}
