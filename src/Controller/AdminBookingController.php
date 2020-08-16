<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginationService;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     */
    public function index(BookingRepository $repo, $page = 1, PaginationService $pagination)
    {
        $pagination->setEntityClass(Booking::class)
                    ->setCurrentPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Fonction permettant d'editer une reservation via un formulaire
     *
     * @Route("admin/bookings/{id}/edit", name="admin_bookings_edit")
     * 
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return void
     */
    public function edit(Booking $booking, EntityManagerInterface $manager, Request $request)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {   //on update le montant au cas ou nus avons changer le nombre de nuit ou l'annonce
            //$booking->setAmount(0); //avec preUpdate il changera
            //ou
            //$booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();
            $this->addFlash(
                'success',
                'La réservation n°'. $booking->getId() .' a bien été modifiée !'
            );
            return $this->redirectToRoute('admin_bookings_index');
        }
        
        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }



    /**
     * Foction permettant de supprimer une reservation
     *
     * @Route("/admin/bookings/{id}/delete", name ="admin_bookings_delete")
     * 
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager)
    {
        $manager->remove($booking);
        $manager->flush();
        $this->addFlash(
            'success',
            'La réservation a bien été supprimée !'
        );
        return $this->redirectToRoute('admin_bookings_index');
    }
}
