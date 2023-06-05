<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of PlaylistControllerTest
 *
 * @author Manon Avaullée
 */
class PlaylistControllerTest extends WebTestCase {
    
   /**
    * Vérifie que les routes permettant le tris des playlists par ordre 
    * alphabétique croissant et décroissant sont fonctionnelles 
    */
    public function testValidTriSurNomDesPlaylists()
    {
        $client = static::createClient();
        
        $client->request('GET',"/playlists/tri/name/ASC");
        $this->assertSelectorTextContains('h5','Compléments Android : programmation mobile');        
        $client->request('GET',"/playlists/tri/name/DESC");
        $this->assertSelectorTextContains('h5','Visual Studio 2019 et C#');        
    }
    
    /**
    * Vérifie que les routes permettant le tris des playlists sur leur nombre 
     * de formations par ordre croissant et décroissant sont fonctionnelles 
    */
    public function testValidTriSurNbFormationParPlaylist()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET',"/playlists/tri/nbformations/DESC");
       
        $this->assertSelectorTextContains('h5', 'Eclipse et Java');  
        
        $crawler = $client->request('GET',"/playlists/tri/nbformations/ASC");
        $this->assertSelectorTextContains('h5', 'Compléments Android : programmation mobile');
    }
    
    /**
     * Vérifie que la route permettant le filtrage des playlists sur un titre de 
     * playlist est opérationnel 
     */
    public function testValidFiltrePlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');        

        // Simulation de la soumission du formulaire
        $crawler = $client->submitForm('filtrer', [
            'recherche_playlist' => 'Eclipse et Java'
        ]);
        // Vérifie si la formation correspond à la recherche        
        $this->assertCount(0, $crawler->filterXPath('//h5[contains(text(), "UML : Diagramme de paquetages")]'));
    }
    
    /**
     * Verifie si le clique sur le bouton "Voir détail" d'une playlist est fonctionnel
     */
    public function testValidDetailPlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        $client->clickLink('Voir détail');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK,$response->getStatusCode());
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/playlists/playlist/8', $uri);          
        
        $this->assertSelectorTextContains('h4', 'Compléments Android : programmation mobile'); 
    }
}
