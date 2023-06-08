<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AdminFormationsControllerTest
 *
 * @author Manon Avaullée
 */
class AdminFormationsControllerTest extends WebTestCase {

    public function testValidTriSurNomDesFormations() {
        $client = static::createClient();

        $crawler = $client->request('GET', "/formationsAdmin/tri/title/ASC");
        $this->assertCount(1, $crawler->filterXPath('//h5[contains(text(), "Android Studio (complément n°13) : Permissions")]'));

        $client->request('GET', "/formationsAdmin/tri/title/DESC");
        $this->assertCount(1, $crawler->filterXPath('//h5[contains(text(), "UML : Diagramme de paquetages")]'));
    }

    public function testValidTriSurNomDesPlaylists() {
        $client = static::createClient();

        $crawler = $client->request('GET', "/formationsAdmin/tri/name/ASC/playlist");
        $this->assertCount(1, $crawler->filterXPath('//h5[contains(text(), "Eclipse n°8 : Déploiement")]'));

        $crawler = $client->request('GET', "/formationsAdmin/tri/name/DESC/playlist");
        $this->assertCount(1, $crawler->filterXPath('//h5[contains(text(), "Python n°16 : Décorateurs")]'));
    }

    public function testValidTriSurDatePublication() {
        $client = static::createClient();

        $crawler = $client->request('GET', "/formationsAdmin/tri/publishedAt/ASC");
        $this->assertCount(1, $crawler->filterXPath('//h5[contains(text(), "Sujet E5 SLAM 2019 : cas RESTILOC ")]'));

        $crawler = $client->request('GET', "/formationsAdmin/tri/publishedAt/DESC");
        $this->assertCount(1, $crawler->filterXPath('//h5[contains(text(), "Eclipse n°8 : Déploiement")]'));
    }
    
   

    public function testValidFiltreFormationsAdmin() { //non fonctionnel
        $client = static::createClient();

        //Route de la fonction de tri sur les formations
        $client->request('GET', '/admin');
        
        // Simulation de la soumission du formulaire
        $crawler = $client->submitForm('filtrerDesFormations', [
            'recherche_formation' => 'Python n°16 : Décorateurs'
        ]);

        // Vérification du nombre de lignes obtenues
        $this->assertCount(1, $crawler->filter('h5'));
        // Vérifie si la formation correspond à la recherche
        $this->assertSelectorTextContains('h5', 'Python n°16 : Décorateurs');
    }   
      
    

    
    /*
    public function testValidEditFormation() //non fonctionnel
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $client->clickLink("Editer");

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK,$response->getStatusCode());
        
        
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/admin/edit/89', $uri);

        $this->assertSelectorTextContains('h4', 'Detail formation :');
    }
     * 
     */    
    
}
