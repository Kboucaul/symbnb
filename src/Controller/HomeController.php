<?php 

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*Dans src on est dans le NameSpace : App\Controller 
**notre classe s’appelle en vrai App\Controller\HomeController 
*/ 

class HomeController extends AbstractController{ 


 /** 
     *@Route("/hello/{prenom}/{age}", name="hello")
    */
    public function hello($prenom = "anonyme", $age = 18)
    {
        //$prenom = "Jog";
       // return new Response("Bonjour " . $prenom . " vous avez " . $age . " ans!");
        return $this->render("hello.html.twig", [
            "prenom" => $prenom,
            "age" => $age
        ]);
    }

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
    public function home() { 

    /* 
    **Retourner un objet reponse 
    */ 
    /*
    ** avec twig render prends 2 arguments :
    ** le liens du fichier twig et un tableau associatifs 
    **  d'arguments a passer au template twig
    */
    $tab = [
       "Lior"=>31,
       "Jog"=>25,
       "Anne"=>55 
    ];
    return $this->render("home.html.twig", [
        "title"=>"Bonjour a vous :)",
        "age"=>12,
        "tableau"=>$tab
        ]);

//ne pas oublier d’integrer le namespace 
//une reponse est dans le namespace HttpFoundation\Response 
    }
}
?>