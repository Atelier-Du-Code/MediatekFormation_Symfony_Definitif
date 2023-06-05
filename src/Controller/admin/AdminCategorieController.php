<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Description of AdminCategorieController
 *
 * @author Manon Avaullée
 */
class AdminCategorieController extends AbstractController {
    
     /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository; 
    
    const CHEMIN_CATEGORIES = "admin/admin.categories.html.twig";
    
    function __construct(CategorieRepository $categorieRepository ) {
        $this->categorieRepository = $categorieRepository;
    }
    
     /**
      * Méthode permettant l'accès à la page des catégories de la partie admin
      * et d'y ajouter une catégorie si elle n'est pas déjà recensée dans la base
     * @Route("/adminCategorie/categories", name="admin.categories")
     * @return Response
     */
    public function index(Request $request): Response{       
           
         $categories = $this->categorieRepository->findAll();
        
        $categorie = new Categorie();
        $categorieRef = new Categorie();
       
        
        $formCategorie = $this->createForm(CategorieType::class, $categorie);
        $formCategorie->handleRequest($request);
        
        
        if($formCategorie->isSubmitted() && $formCategorie->isValid()&& 
                $this->categorieRepository->findByDoublonsCategorie($categorie->getName()) == 0)
        {    
            $this->categorieRepository->add($categorie, true);
             return $this->redirectToRoute('admin.categories');
        }
        return $this->render(self:: CHEMIN_CATEGORIES, [
            'categories' => $categories,
            'formCategorie' => $formCategorie->createView()
        ]);
    }
    
    /**
     * Methode permettant de supprimer une catégorie de la base
    * @Route("/adminCategorie/suppr/{id}", name="admin.categorie.suppr")
    * @param Categorie $categorie         
    * @return Response
    */
    public function suppr(Categorie $categorie) : Response{        
        
        $nbFormationPourCategorie = $this->categorieRepository->findByAllFormationsOneCategorie($categorie->getId());
        
         if($nbFormationPourCategorie == 0)
        {
            $this->categorieRepository->remove($categorie, true);
            return $this->redirectToRoute('admin.categories');
        }
        else
        {
            return $this->redirectToRoute('admin.categories');
        }
    }    
}

