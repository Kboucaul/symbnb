<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * Affiche le formulaire de réservation
     * 
     * 
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);

            /* Si les dates ne sont pas dispo
            **    => Message d'erreur
            */
            if (!$booking->isBookableDates()) {
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisies ne peuvent être reservées : elles sont déjà prises !lk"
                );
            }
            else
            {

            /*
            **  Sinon enregistrement et redirection 
            */
            
            $manager->persist($booking);
            $manager->flush();

            /*
            **Le parametre withAlert ira se mettre a la suite de la route comme un parametre get
            **  ex: /booking/123?withAlert=true
            **
            **  Ainsi a la creation d ela reservation l'utilisateur verra s'afficher un message
            */
            return $this->redirectToRoute("booking_show", [
                'id' => $booking->getId(),
                'withAlert' => true
                ]);
            }
        }

        return $this->render('booking/book.html.twig', [
         'ad' => $ad,
         'form' => $form->createView()
        ]);
    }
    /**
     * Fonction permettant l'affichage d'un e page de réservation
     *
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @Route("/booking/{id}", name="booking_show")
     * 
     * @return Response
     *
    */
    public function show(Booking $booking, EntityManagerInterface $manager, Request $request)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre commentaire a bien été pris en compte!"
            );
        }



        return $this->render('/booking/show.html.twig',[
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}
