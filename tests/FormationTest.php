<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests;

use App\Entity\Formation;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Description of FormationTest
 *
 * @author Manon Avaullée
 */
class FormationTest extends TestCase{
    
    /*
     * Vérifie qu'une date peut-être modifiée sans erreur
     */
    public function testGetDateCreationString()
    {
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime("2022-04-14"));
        $this->assertEquals("14/04/2022", $formation->getPublishedAtString());
    }
}

