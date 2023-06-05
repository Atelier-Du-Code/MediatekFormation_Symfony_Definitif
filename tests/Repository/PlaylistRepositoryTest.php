<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of PlaylistRepositoryTest
 *
 * @author Manon Avaullée
 */
class PlaylistRepositoryTest extends KernelTestCase{
   
    public function newPlaylist()
    {
        $playlist = (new Playlist())
                ->setName("Nom de playlist")
                ->setDescription("C'est la description de la playlist");
        
        return $playlist;
    }
    
    public function recupRepository() : PlaylistRepository 
    {
        self::bootKernel();
        $repository = self::getContainer()->get(PlaylistRepository::class);
        return $repository;
    }
    
    /**
     * Permet de comparer un nombre d'enregistrement théorique et pratique
     * @param Formation $formation
     * @param int $nbErreursAttendues
     * @param string $message
     */
     public function assertErrors(Playlist $playlist, int $nbErreursAttendues, string $message="")
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($playlist);
        $this->assertCount($nbErreursAttendues, $error, $message);
    }
    
    /**
     * Vérifie le nombre total de playlists dans la base de données
     */
    public function testNbPlaylists()
    {
        $repository = $this->recupRepository();
        $nbPlaylist = $repository->count([]);
        $this->assertEquals(27, $nbPlaylist);
    }
    
     /**
     * Vérifie qu'une playlist peut être ajoutée sans erreur
     */
    public function testAddPlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = $this->newPlaylist();
        $nbPlaylist = $repository->count([]);
        $repository->add($playlist, true);
        $this->assertEquals($nbPlaylist + 1, $repository->count([]), "erreur lors de l'ajout");        
    }
    
    /**
     * Vérifie que le tri par ordre alphabétique croissant et décroissant est opérationnel
     */
    public function testValidFindByOrderByName()
    {
         $repository = $this->recupRepository();  
        
        $playlist = $repository->findByOrderByName("ASC");
        $this->assertEquals("Bases de la programmation (C#)", $playlist[0]->getName(),
                "Le tri par ordre alphaphabétique ASC sur le titre de la playlist n'est pas fonctionnel");
        
        $playlist = $repository->findByOrderByName("DESC");
        $this->assertEquals("Visual Studio 2019 et C#", $playlist[0]->getName(),
                "Le tri par ordre alphaphabétique DESC sur le titre de la playlist n'est pas fonctionnel");
    }
    
    /**
     * Vérifie que le tri par nombre de formations contenues dans une playlist croissant et décroissant est opérationnel
     */
    public function testValidFindByOrderByNbFormations()
    {
        $repository = $this->recupRepository();  
        
        $playlist = $repository->findByOrderByNbFormations("ASC");
        $this->assertEquals("Programmation sous Python", $playlist[0]->getName(),
                "le tri par ordre croitssant de formation dans une playlist n'est pas fonctionnel");
        
        $playlist = $repository->findByOrderByNbFormations("DESC");
        $this->assertEquals("Eclipse et Java", $playlist[0]->getName(),
                "le tri par ordre décroissant de formation dans une playlist n'est pas fonctionnel");
    }
    
    /**
     * Vérifie que le filtrage des playlists sur leurs catégories est opérationnel
     */
    public function testValidfindByContainValueDansTableCategorie()
    {
        $repository = $this->recupRepository(); 
        $playlist = $this->newPlaylist();
        $repository->add($playlist, true);
        
        $playlist = $repository->findByContainValueDansTableCategorie("name", "Java");
        $nbPlaylist = count($playlist);
        $this->assertEquals(1, $nbPlaylist, "Le titre de cette catégorie n'a pas été trouvé");
    }
    
    /**
     * Vérifie que le filtrage des playlists sur une catégorie qui n'existe pas dans la base échoue
     */
     public function testNonValidfindByContainValueDansTableCategorie()
    {
        $repository = $this->recupRepository(); 
        $playlist = $this->newPlaylist();
        $repository->add($playlist, true);
        
        $playlist = $repository->findByContainValueDansTableCategorie("name", "Catégorie qui n'existe pas");
        $nbPlaylist = count($playlist);
        $this->assertEquals(0, $nbPlaylist, "Le titre de cette catégorie a été trouvé");
    }
    
    /**
     * Vérifie que le filtrage des playlists sur le nom d'une playlist est opérationnel
     */
    public function testValidfindByContainValueDansTablePlaylist()
    {
        $repository = $this->recupRepository(); 
        $playlist = $this->newPlaylist();
        $repository->add($playlist, true);
        
        $playlist = $repository->findByContainValueDansLaTablePlaylist("name", "Eclipse et Java");
        $nbPlaylist = count($playlist);
        $this->assertEquals(1, $nbPlaylist, "Le titre de cette playlist n'a pas été trouvé");
    } 
    
    /**
     * Vérifie que le filtrage des playlists sur le nom d'une playlist qui n'existe pas dans la base échoue
     */
    public function testNonValidfindByContainValueDansTablePlaylist()
    {
        $repository = $this->recupRepository(); 
        $playlist = $this->newPlaylist();
        $repository->add($playlist, true);
        
        $playlist = $repository->findByContainValueDansLaTablePlaylist("name", "Playlist qui n'existe pas");
        $nbPlaylist = count($playlist);
        $this->assertEquals(0, $nbPlaylist, "Le titre de cette playlist a été trouvé");
    } 
    
    
}
