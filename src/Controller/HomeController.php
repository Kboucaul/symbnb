<?php 

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*Dans src on est dans le NameSpace : App\Controller 
**notre classe s’appelle en vrai App\Controller\HomeController 
*/ 

class HomeController extends AbstractController{ 

/* 
**Pour créer une classe : 3 pilliers 
*/ 

/* 
**1-Expliquer a quelle addresse l’utiliser (la route) 
**On utilise des annotations sous la forme :  
“/** 
*@Route(“/”, name=”homepage”) 
*/
/*
Pour que cette annotation marche il faut utiliser le bon namespace 
=> on doit importer cette classe 
*/
 
/* 
**2-Fonction publique qui correspond  al’affichage que l’on souhaite 
*/ 
/** 
*@Route("/", name="homepage") 
*/
    public function home(AdRepository $adRepo, UserRepository $userRepo) { 

    /* 
    **Retourner un objet reponse 
    */ 
    /*
    ** avec twig render prends 2 arguments :
    ** le liens du fichier twig et un tableau associatifs 
    **  d'arguments a passer au template twig
    */
    return $this->render("home.html.twig", [
        'users' => $userRepo->findBestUsers(2),
        'ads' => $adRepo->findBestAds(3)
    ]);

//ne pas oublier d’integrer le namespace 
//une reponse est dans le namespace HttpFoundation\Response 
    }
}
?>