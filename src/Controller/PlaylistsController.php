<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController {
    
    /**
     * 
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
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
    
    const CHEMIN_PLAYLISTS = "pages/playlists.html.twig";
    
    function __construct(PlaylistRepository $playlistRepository, 
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    public function index(): Response{
        $playlists = $this->playlistRepository->findByOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories            
        ]);
    }

    /**
     * Méthode permettant le tri des playlists suivant la valeur d'un champs de la table Playlist ou Categorie
     * @Route("/playlists/tri/{champ}/{ordre}", name="playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre):Response
    {
        switch($champ)
        {                
            case "name":
                $playlists = $this->playlistRepository->findByOrderByName($ordre);
                break;
            case "nbformations":
                $playlists = $this->playlistRepository->findByOrderByNbFormations($ordre);
                break;    
            default:
        }
        
        $categories = $this->categorieRepository->findAll();
        return $this->render(self :: CHEMIN_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * Méthode permettant le tri des playlists suivant la valeur d'un champs de la table Categorie
     * @Route("/playlists/recherche/{champ}", name="playlists.findAllContainValueTbCategorie")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContainValueDansLaTableCategories($champ, Request $request): Response{
        
        $valeur = $request->get("recherche");
            $playlists = $this->playlistRepository->findByContainValueDansTableCategorie($champ, $valeur);
            $categories = $this->categorieRepository->findAll();
            return $this->render(self::CHEMIN_PLAYLISTS, [
                'playlists' => $playlists,
                'categories' => $categories,            
                'valeur' => $valeur
            ]);
        
    }  
    
     /**
      * Méthode permettant le tri des playlists suivant la valeur d'un champs de la table Playlist
     * @Route("/playlists/recherche_playlist/{champ}", name="playlists.findAllContainValueTbPlaylist")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContainValueDansLaTablePlaylist($champ, Request $request): Response{      
        
        if($this->isCsrfTokenValid('filtre_'.$champ, $request->get('_token')))
        {
            $valeur = $request->get("recherche_playlist");
        $playlists = $this->playlistRepository->findByContainValueDansLaTablePlaylist($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeurPlaylist' => $valeur
        ]);
        }
        else 
        {
             return $this->redirectToRoute("playlists");
        }        
    }  
    
    /**
     * Méthode permettant l'appel une page contenant les détails de la playlist courante
     * @Route("/playlists/playlist/{id}", name="playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $nbFormationDeLaPlaylist = $this->formationRepository->findCountForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'nbFormationDeLaPlaylist' => $nbFormationDeLaPlaylist,
            'playlistformations' => $playlistFormations
        ]);        
    }       
    
}
