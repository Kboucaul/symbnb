<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService {
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->route        = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->templatePath = $templatePath;
    }

    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function getData()
     {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !
            Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        //1)Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        //2)Demander au repo de trouver les elements
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        //3)Renvoyer les elements   
        return $data;
     }

     public function getPages()
     {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !
            Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
         // 1) Connaitre le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
         // 2) Faire la division, l'arrondi
        $pages = ceil($total / $this->limit);
         // 3) return
         return $pages;
     }

    //setter
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    //getter
    public function getEntityClass()
    {
        return $this->entityClass;
    }

     //setter
     public function setLimit($limit)
     {
         $this->limit = $limit;
         return $this;
     }
 
     //getter
     public function getLimit()
     {
         return $this->limit;
     }

      //setter
      public function setTemplatePath($templatePath)
      {
          $this->templatePath = $templatePath;
          return $this;
      }
  
      //getter
      public function getTemplatePath()
      {
          return $this->templatePath;
      }

      //setter
      public function setRoute($route)
      {
          $this->route = $route;
          return $this;
      }
  
      //getter
      public function getRoute()
      {
          return $this->route;
      }
 

     //setter
     public function setCurrentPage($currentPage)
     {
         if ($currentPage <= 0)
            $currentPage = 1;
         $this->currentPage = $currentPage;
         return $this;
     }
 
     //getter
     public function getCurrentPage()
     {
         return $this->currentPage;
     }
}