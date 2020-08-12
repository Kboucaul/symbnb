<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * Permet de creer une annonce
     *
     * @Route("/ads/new", name="ads_create")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        //On crée une annonce
        $ad = new Ad();


        //on cree un formilaire
        $form = $this->createForm(AdType::class, $ad);
        //on demande a doctrine de recuperer les donnees
        $form->handleRequest($request);

        //petites verifications mais inssufisantes
        if ($form->isSubmitted() && $form->isValid())
        {

            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            //on fait persister dans la base de données
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée ! "
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Fonction permettant d'éditer une annonce via un formulaire 
     *
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        //on recupere l'annonce grace a symfony

         //on cree un formulaire
         $form = $this->createForm(AdType::class, $ad);
         //on demande a doctrine de recuperer les donnees
         $form->handleRequest($request);

          //petites verifications mais inssufisantes
        if ($form->isSubmitted() && $form->isValid())
        {

            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            //on fait persister dans la base de données
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications <strong>{$ad->getTitle()}</strong> ont bien été enregistrées ! "
            );
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }


        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }


    /**
     * @Route("/ads/{slug}", name="ads_show")
     */
    public function show($slug, Ad $ad)
    {
        //je recupere l'annonce qui correspnd au slug
        //$ad = $repo->findOneBySlug($slug);
        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }
}
