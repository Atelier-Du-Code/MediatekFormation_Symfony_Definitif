<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of FormationControllerTest
 *
 * @author Manon Avaullée
 */
class FormationControllerTest extends WebTestCase {
    
    /**
    * Vérifie que les routes permettant le tris des formations par ordre 
     * alphabétique croissant et décroissant sont fonctionnelles 
    */
    public function testValidTriSurNomDesFormations()
    {
        $client = static::createClient();
        
        $client->request('GET',"/formations/tri/title/ASC");
        $this->assertSelectorTextContains('h5','Android Studio (complément n°13) : Permissions');        
        $client->request('GET',"/formations/tri/title/DESC");
        $this->assertSelectorTextContains('h5','UML : Diagramme de paquetages');        
    }
    
    /**
     * Vérifie que les routes  permettant le tri des formations sur leur playlist par 
     * ordre alphabétique croissant et décroissant sont fonctionnelles 
     */
    public function testValidTriSurNomDesPlaylists()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET',"/formations/tri/name/ASC/playlist");
        $this->assertSelectorTextContains('h5', "Eclipse n°8 : Déploiement");
        
        $crawler = $client->request('GET',"/formations/tri/name/DESC/playlist");
        $this->assertSelectorTextContains('h5', "Python n°16 : Décorateurs");
    }
    
     /**
     * Vérifie que les routes  permettant le tri des formations sur leur date de publication 
      * par ordre croissant et décroissant sont fonctionnelles 
     */
    public function testValidTriSurDatePublication()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET',"/formations/tri/publishedAt/ASC");       
        $this->assertSelectorTextContains('h5', "Sujet E5 SLAM 2019 : cas RESTILOC");
        
        $crawler = $client->request('GET',"/formations/tri/publishedAt/DESC");       
        $this->assertSelectorTextContains('h5', "Eclipse n°8 : Déploiement");
    }
    
    //Vérification des filtres
    
    /**
     * Vérifie que la route permettant le filtrage des formations sur un titre de 
     * formation est opérationnel 
     */
   public function testValidFiltreFormations()
   {
        $client = static::createClient();
        $client->request('GET', '/formations');        

        // Simulation de la soumission du formulaire
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Python n°16 : Décorateurs'
        ]);        
       
        // Vérification du nombre de lignes obtenues
        $this->assertCount(1, $crawler->filter('h5'));
        // Vérifie si la formation correspond à la recherche
        $this->assertSelectorTextContains('h5', 'Python n°16 : Décorateurs');
    }
    
    /**
     * Vérifie que la route permettant le filtrage des formations sur un titre de 
     * playlist est opérationnel 
     */
   public function testValidFiltrePlaylist()
   {
        $client = static::createClient();
        $client->request('GET', '/formations');        

        // Simulation de la soumission du formulaire
        $crawler = $client->submitForm('Filtrer', [
            'recherche_playlist' => 'Eclipse et Java'
        ]);
        // Vérification du nombre de lignes obtenues
        $this->assertCount(3, $crawler->filter('h5'));
        // Vérifie si la formation correspond à la recherche        
        $this->assertCount(1, $crawler->filterXPath('//h5[contains(text(), "C# : ListBox en couleur")]'));
    }
    
    /**
     * Vérifie que la route permettant le filtrage des formations sur un titre 
     * de formation est opérationnel 
     */
    public function testValidDetailFormation() 
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        
        $client->clickLink("C'est la miniature d'une formation");
        
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK,$response->getStatusCode());
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/formations/formation/1', $uri);
        
         $this->assertSelectorTextContains('h4', 'Eclipse n°8 : Déploiement');
    }

}
