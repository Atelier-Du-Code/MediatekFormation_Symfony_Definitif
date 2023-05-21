<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur des formations
 *
 * @author emds
 */
class FormationsController extends AbstractController {

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
    
    const CHEMIN_FORMATION = "pages/formations.html.twig";
    
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("/formations", name="formations")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self :: CHEMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/formations/tri/{champ}/{ordre}", name="formations.sortTableFormation")
     * @param type $champ
     * @param type $ordre    
     * @return Response
     */
    public function sortTableFormation($champ, $ordre): Response{        
        $formations = $this->formationRepository->findAllOrderByChampOrdre($champ, $ordre);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self :: CHEMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }     
    
    /**
     * @Route("/formations/tri/{champ}/{ordre}/{table}", name="formations.sortHorsTableFormation")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sortHorsTableFormation($champ, $ordre, $table): Response{        
        $formations = $this->formationRepository->findAllOrderByChampOrdreTable($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self :: CHEMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }     
    
    
    /**
     * @Route("/formations/rechercheCategorie/{champ}/{table}", name="formations.findAllContainChampCategorie")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainChampCategorie($champ, Request $request, $table): Response{
       
        $valeur = $request->get("rechercheCategorie");
        $formations = $this->formationRepository->findByContainValueChampHorsTableFormation($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self :: CHEMIN_FORMATION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);                
    }  
    
    /**
     * @Route("/formations/recherche/{champ}/{table}", name="formations.findAllContainValuechampPlaylist")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainChampPlaylist($champ, Request $request, $table): Response{
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
            return $this->redirectToRoute("formations");
        }
    }  
    
    /**
     * @Route("/formations/recherche/{champ}", name="formations.findAllContainValueChampTableFormation")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContainValueChampTableFormation($champ, Request $request): Response{
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
            return $this->redirectToRoute("formations");
        }
    }  
    /**
     * @Route("/formations/formation/{id}", name="formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);        
    }   
    
}
