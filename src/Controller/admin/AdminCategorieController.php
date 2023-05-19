<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller\admin;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/adminCategorie/categories", name="admin.categories")
     * @return Response
     */
    public function index(): Response{       
        $categories = $this->categorieRepository->findByAllOrderBy('ASC');
        return $this->render(self :: CHEMIN_CATEGORIES, [
            'categories' => $categories            
        ]);
    }
}
