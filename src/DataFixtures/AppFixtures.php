<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        //Je cree un role admin

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        //Je crée un nouvelle utilisateur qui aura le role admin
        $adminUser = new User();
        $adminUser->setFirstName('Jog')
                    ->setLastName('Boucault')
                    ->setEmail('kevinboucault@gmail.com')
                    ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                    ->setPicture("https://media-exp1.licdn.com/dms/image/C4D03AQE8DNGWxg9r-w/profile-displayphoto-shrink_400_400/0?e=1602720000&v=beta&t=C_4acaD7r9lHnFAjdzyBAU3w53hFvKrODvUWaxN3cmI")
                    ->setIntroduction("Salut moi c'est Jog, je suis le créateur et l'administrateur de ce site :)")
                    ->setDescription("Je suis passionné par le développement web et par l'innovation au sens large du terme. Venez discuter avec moi!")
                    ->addUserRole($adminRole);
        $manager->persist($adminUser);


        $users = [];
        $genres = ['men', 'women'];
        //nous gerons les utilisateurs
        for($i = 1; $i <= 10; $i++)
        {
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = "https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';
            $picture = $picture.$genre.'/'.$pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>'.join('</p><p>', $faker->paragraphs(3)). '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);
            $manager->persist($user);
            $users[] = $user;  
        }
        //nous gerons les annonces
        for($i = 1; $i <= 10; $i++)
        {
            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350); //(hauteur/largeur)
            $introduction = $faker->paragraph(2);
            $content = '<p>'.join('</p><p>', $faker->paragraphs(5)). '</p>';

            $user = $users[mt_rand(0, count($users) - 1)];
            $ad = new Ad();
            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);


            for ($j = 1; $j <= mt_rand(2, 5); $j++)
            {
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence())
                        ->setAd($ad);
                $manager->persist($image);
            }
            // gestion des reservation

            for ($j = 1; $j <= mt_rand(0, 50); $j++)
            {
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months'); //date a partir de 6 mois auparavant
                
                $startDate = $faker->dateTimeBetween('-3 months');
              
                //on calcul une duree de sejour
                $duration = mt_rand(1, 7);
                //on clone la startDate pour ne pas la modifier
                $endDate = (clone $startDate)->modify("+$duration days");

                //On calcul le montant
                $amount = $duration * $ad->getPrice();

                //On cherche le booker l'utilisateur qui reserve

                $booker = $users[mt_rand(0, count($users) - 1)];

                //on commentaire ?
                //si 0 ->rien
                // si 1 -> un commentaire
                $commentOrNot = mt_rand(0, 1);
                if ($commentOrNot == 0)
                    $comment = "";
                else
                    $comment = $faker->paragraph(2); 

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setCreatedAt($createdAt)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setAmount($amount)
                        ->setComment($comment);

                $manager->persist($booking);

                //gestion des commentaires
                if (mt_rand(0, 1))
                {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph(1))
                            ->setRating(mt_rand(1, 5))
                            ->setAuthor($booker)
                            ->setAd($ad);
                    $manager->persist($comment);
                }
            }
            $manager->persist($ad);
        }
        $manager->flush();
    }
}
