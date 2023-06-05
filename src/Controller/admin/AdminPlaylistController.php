<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AdminPlaylistController
 *
 * @author Manon Avaullée
 */
class AdminPlaylistController extends AbstractController {
    
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
    
    const CHEMIN_PLAYLIST = "admin/admin.playlists.html.twig";
    
    function __construct(PlaylistRepository $playlistRepository, 
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
     /**
      * Méthode permettant d'accéder à la page qui répertorie toutes les playlists
     * dans la partie admin
     * @Route("/adminPlaylist/playlists", name="admin.playlists")
     * @return Response
     */
    public function index(): Response{
        $playlists = $this->playlistRepository->findByOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self :: CHEMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories            
        ]);
    }
    
     /**
      * Méthode permettant le tri croissant ou décroissant des playlists suivant la valeur d'un champs spécifique
     * @Route("/adminPlaylist/tri/{champ}/{ordre}", name="admin.playlist.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre):Response
    {
        switch($champ)
        {                
            case "title":
                $playlists = $this->playlistRepository->findByOrderByName($ordre);
                break;
            case "nbformations":
                $playlists = $this->playlistRepository->findByOrderByNbFormations($ordre);
                break;    
            default:
        }
        
        $categories = $this->categorieRepository->findAll();
        return $this->render(self :: CHEMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
     /**
      * Méthode permettant le filtrage des playlists suivant la valeur d'un champs de la table playlist
     * @Route("/adminPlaylist/recherchePlaylist/{champ}", name="admin.playlists.findByContainValueDansTablePlaylist")
     * @param type $champ
     * @param Request $request    
     * @return Response
     */
    public function findByContainValueDansTablePlaylist($champ, Request $request): Response{
        
        if($this->isCsrfTokenValid('filtre_'.$champ, $request->get('_token')))
        {
            $valeur = $request->get("recherche");
            $playlists = $this->playlistRepository->findByContainValueDansLaTablePlaylist($champ, $valeur);

            $categories = $this->categorieRepository->findAll();
            return $this->render(self :: CHEMIN_PLAYLIST, [
                'playlists' => $playlists,
                'categories' => $categories,            
                'valeurPlaylist' => $valeur,            
            ]);
        }   
        else
        {
            return $this->redirectToRoute("admin.playlists");
        }
    }  
    
    /**
     * Méthode permettant le filtrage des catégories suivant la valeur d'un champs de la table catégorie
     * @Route("/adminPlaylist/rechercheCategorie/{champ}", name="admin.playlists.findByContainValueDansTableCategories")
     * @param type $champ
     * @param Request $request      
     * @return Response
     */
    public function findByContainValueDansTableCategories($champ, Request $request): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValueDansTableCategorie($champ, $valeur);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeurCategorie' => $valeur
        ]);
    }  
    
    /**
     * Méthode pour supprimer une playlist 
     * Côté admin
     * @Route("/adminPlaylist/suppr/{id}", name="admin.playlist.suppr")
     * @param Playlist playlist
     * @return Response
     */
    public function suppr(Playlist $playlist) : Response{
        
        if($playlist->getNbFormationsDeLaPlaylist() == 0)
        {
             $this->playlistRepository->remove($playlist, true);
             return $this->redirectToRoute('admin.playlists');
        }
        else
        {
            return $this->redirectToRoute('admin.playlists');
        }       
    }
    
    /**
     * Méthode permettant d'éditer une playlist
     * Côté admin
     * @Route("/adminPlaylist/edit/{id}", name="admin.playlist.edit")
     * @param Playlist $playlist
     * @param Request $request
     * @return Response
     */    
    public function edit(Playlist $playlist, Request $request) : Response{ 
        
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        $formPlaylist->handleRequest($request);    
        
        if($formPlaylist->isSubmitted() && $formPlaylist->isValid())
        {
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlists');
        }
        $playlists = $this->playlistRepository->findByOrderByName('ASC');
        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'playlists' => $playlists,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * Méthode permettant l'ajout d'une playlist dans la base
     * @Route("/adminPlaylist/ajout", name="admin.playlist.ajout")
     * @param Request $request
     * @return Response
     */    
    public function ajout(Request $request) : Response{        
        
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        $formPlaylist->handleRequest($request);
        
        if($formPlaylist->isSubmitted() && $formPlaylist->isValid())
        {
            $this->playlistRepository->add($playlist, true);
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render("admin/admin.playlist.ajout.html.twig", [
            'playlist' => $playlist,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }
}

