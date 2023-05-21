<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Description of AdminFormationController
 *
 * @author Manon Avaullée
 */
class AdminFormationController extends AbstractController{
    
  /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    const CHEMIN_FORMATION = "admin/admin.formations.html.twig";
    
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("/admin", name="admin.formations")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self :: CHEMIN_FORMATION,[
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/suppr/{id}", name="admin.formation.suppr")
     * @param Formation $formation
     * @return Response
     */
    public function suppr(Formation $formation) : Response{
        $this->formationRepository->remove($formation, true);
        return $this->redirectToRoute("admin.formations");
    }
    
   /**
     * @Route("/admin/edit/{id}", name="admin.formation.edit")
     * @param Formation $formation
     * @param Request $request
     * @return Response
     */    
    public function edit(Formation $formation, Request $request) : Response{        
        
        $formFormation = $this->createForm(FormationType::class, $formation);
        $formFormation->handleRequest($request);
        
        if($formFormation->isSubmitted() && $formFormation->isValid())
        {
            $this->formationRepository->add($formation, true);
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/admin.formation.edit.html.twig", [
            'formation' => $formation,
            'formFormation' => $formFormation->createView()
        ]);
    }
    
    /**
     * @Route("/formationsAdmin/tri/{champ}/{ordre}", name="admin.formations.sortDansTableFormation")
     * @param type $champ
     * @param type $ordre     
     * @return Response
     */
    public function sortDansTableFormation($champ, $ordre): Response{
        
        $formations = $this->formationRepository->findAllOrderByChampOrdre($champ, $ordre);
        
        
        $categories = $this->categorieRepository->findAll();
        return $this->render(self ::CHEMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/formationsAdmin/tri/{champ}/{ordre}/{table}", name="admin.formations.sortHorsTableFormation")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sortHorsTableFormation($champ, $ordre, $table): Response{
        
        $formations = $this->formationRepository->findAllOrderByChampOrdreTable($champ, $ordre, $table);
        
        $categories = $this->categorieRepository->findAll();
        return $this->render(self ::CHEMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }     
    
    /**
     * @Route("/formationsAdmin/recherche/{champ}", name="admin.formations.findByContainValueChampFormation")
     * @param type $champ
     * @param Request $request
     
     * @return Response
     */
    public function findByContainValueChampFormation($champ, Request $request): Response{
         if($this->isCsrfTokenValid('filtre_'.$champ, $request->get('_token')))
         {
             $valeur = $request->get("recherche");        
            $formations = $this->formationRepository->findByContainValueChampFormation($champ, $valeur);        
            $categories = $this->categorieRepository->findAll();
            return $this->render(self :: CHEMIN_FORMATION, [
                'formations' => $formations,
                'categories' => $categories,
                'valeur' => $valeur            
            ]);
         }
         else
         {
              return $this->redirectToRoute("admin.formations");
         }       
    } 
    
     /**
     * @Route("/formationsAdmin/recherche/{champ}/{table}", name="admin.formations.findallcontainValueChampHorsTableFormation")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainValueChampHorsTableFormation($champ, Request $request, $table): Response{
        
         if($this->isCsrfTokenValid('filtre_'.$champ, $request->get('_token')))
         {
            $valeur = $request->get("recherche");
            $formations = $this->formationRepository->findByContainValueChampHorsTableFormation($champ, $valeur, $table);        
            $categories = $this->categorieRepository->findAll();
            return $this->render(self :: CHEMIN_FORMATION, [
                'formations' => $formations,
                'categories' => $categories,
                'valeur' => $valeur,
                'table' => $table
            ]);
         }
         else
         {
             return $this->redirectToRoute("admin.formations");
         }
        
    }  
    
     /**
     * @Route("/admin/ajout", name="admin.formation.ajout")
     * @param Request $request
     * @return Response
     */    
    public function ajout(Request $request) : Response{        
        
        $formation = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);
        $formFormation->handleRequest($request);
        
        if($formFormation->isSubmitted() && $formFormation->isValid())
        {
            $this->formationRepository->add($formation, true);
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/admin.formation.ajout.html.twig", [
            'formation' => $formation,
            'formFormation' => $formFormation->createView()
        ]);
    }
}
