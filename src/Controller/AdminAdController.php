<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index", requirements={"page": "\d+"})
     */
    public function index(AdRepository $repo, $page = 1, PaginationService $pagination)
    {

        //METHODE FIND QUI PERMET DE TROUVER UN ENREGISTREMENT GRACE A SON ID
        //recherche par id
        //$ad = $repo->find(300);

        //recherche par criteres (multiples ou non)
        //Agit comme des conditions and
        /*$ad = $repo->findOneBy([
            'price' => 183,
            'id' => 320
        ]);
        */

        // Trouver plusieurs annonces
        //$ads = $repo->findBy([criteres], [Orders], Limite, Offset(debut))
        //$ads = $repo->findBy([], [], 5, 0);
        //dump($ads);
        $pagination->setEntityClass(Ad::class)
                    ->setCurrentPage($page);

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    /**
     * Undocumented function
     *
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     * 
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, EntityManagerInterface $manager, Request $request)
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée !"
            );
           return $this->redirectToRoute('admin_ads_index');
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }
    /**
     * Permet de supprimer une annonce
     *
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     * 
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        if (count($ad->getBookings()) == 0)
        {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
            );
        }
        else
        {
            $this->addFlash(
                'warning',
                "L'annonce <strong>{$ad->getTitle()}</strong> n'a pas pu être supprimée car elle comporte des réservations !"
            );
        }
        return $this->redirectToRoute("admin_ads_index");
    }
}
