<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Tests\Validations;

use App\Entity\Formation;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of FormationValidationsTest
 *
 * @author Manon Avaullée
 */
class FormationValidationsTest extends KernelTestCase{
    
    public function getFormation(): Formation
    {         
        $formation = (new Formation())
                ->setTitle("New York")              
                ->setPublishedAt(new DateTime("now"));    
        
        return $formation;               
    }
    
    public function assertErrors(Formation $formation, int $nbErreursAttendues, string $message="")
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $error = $validator->validate($formation);
        $this->assertCount($nbErreursAttendues, $error, $message);
    }
    
    public function testValidDeCreationDeDate()
    {
        $aujourdhui = new \DateTime();        
        $this->assertErrors($this->getFormation()->setPublishedAt($aujourdhui), 0, "La date d'aujourd'hui doit être valide");
        
        $JMoinsCinq = (new \Datetime())->sub(new \DateInterval("P5D"));
        $this->assertErrors($this->getFormation()->setPublishedAt($JMoinsCinq), 0, "5 jours plus tôt, la date doit être valide");
    }
    
    public function testNonValidDeCreationDeDate()
    {
         $demain = (new \Datetime())->add(new \DateInterval("P1D"));
         $this->assertErrors($this->getFormation()->setPublishedAt($demain), 1, "La date du lendemain ne doit plus être valide");
         
         $JPlusCinq = (new \Datetime())->add(new \DateInterval("P5D"));
         $this->assertErrors($this->getFormation()->setPublishedAt($JPlusCinq), 1, "5 jours plus tard,la date ne doit plus être valide");
    }
        
}

