<?php


namespace App\DataFixtures;


use App\Entity\Board;
use App\Entity\Menu;
use App\Entity\Reservation;
use App\Entity\ReservationDetail;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

     private $passwordEncoder;


     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
       }

    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
        $this->loadTables($manager);
        $this->loadMenu($manager);
        $this->loadReservation($manager);

    }

    public function loadUser(ObjectManager $manager)
    {


        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword(
             $user,
             'admin'
        ));
        $manager->persist(($user));
        $manager->flush();
    }


    public function loadTables(ObjectManager $manager)
    {

        for ($i = 1; $i <= 12; $i++) {
            $table = new Board();

            $table->setNumberOfPersons(4);
            $table->setTooltip("A standard table for up to 4 people to enjoying dining gourmet food");
            $table->setName('#'.$i.' - Standard table');
            if(in_array($i,array(1,2))) {
                $table->setIsChef(true);
                $table->setName('#'.$i.' - Chef table');
                $table->setTooltip("Private chef’s table allow you and your guests to enjoy the theatre of the kitchen and meet the chef");
            }


            if(in_array($i,array(3,4))) {
                $table->setMinNumberOfPersons(3);
                $table->setName('#'.$i.' - Family table');
                $table->setIsFamily(true);
                $table->setNumberOfPersons(6);
                $table->setTooltip("Family table offer spacious table for up to 6 guests in the side of the restaurant to make a private atmosphere");

            }

            $this->addReference('table_'.$i, $table);
            $manager->persist($table);
        }

        $manager->flush();
    }

    public function loadMenu(ObjectManager $manager) {
        $faker = \Faker\Factory::create();

        $i=1;

        $menu = new Menu();
        $menu->setName("Tomato soup");
        $menu->setCategory("Soups");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setSpecial("1");
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('soup_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Chiken soup");
        $menu->setCategory("Soups");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('soup_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Corn soup");
        $menu->setCategory("Soups");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('soup_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Fish soup");
        $menu->setCategory("Soups");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('soup_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Onion soup");
        $menu->setCategory("Soups");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('soup_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Pumpkin soup");
        $menu->setCategory("Soups");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('soup_'.$i++, $menu);
        $manager->persist($menu);

        $i=1;

        $menu = new Menu();
        $menu->setName("Shrimp tempura");
        $menu->setCategory("Appetizers");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('appetizer_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Fried cheese");
        $menu->setCategory("Appetizers");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('appetizer_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Beef tartar");
        $menu->setCategory("Appetizers");
        $menu->setSpecial("2");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('appetizer_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Beef carpaccio");
        $menu->setCategory("Appetizers");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('appetizer_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Onion rings");
        $menu->setCategory("Appetizers");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('appetizer_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Crispy prawns");
        $menu->setCategory("Appetizers");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('appetizer_'.$i++, $menu);
        $manager->persist($menu);


        /**
        * Mains
        */
        $i=1;
        $menu = new Menu();
        $menu->setName("Chicken breast");
        $menu->setCategory("Main dish");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('main_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("New York steak");
        $menu->setCategory("Main dish");
        $menu->setSpecial("3");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('main_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("BBQ Burger");
        $menu->setCategory("Main dish");
        $menu->setSpecial("7");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('main_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Spaghetti");
        $menu->setCategory("Main dish");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('main_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Lasagne");
        $menu->setCategory("Main dish");
        $menu->setSpecial("4");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('main_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Beef Sirloin");
        $menu->setCategory("Main dish");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('main_'.$i++, $menu);
        $manager->persist($menu);

        /**
        * Fish & vege
        */
        $i=1;
        $menu = new Menu();
        $menu->setName("Roast Salmon");
        $menu->setCategory("Fish and vege");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('fishvege_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Grilled trout");
        $menu->setCategory("Fish and vege");
        $menu->setSpecial("5");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('fishvege_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Smoked Salmon");
        $menu->setCategory("Fish and vege");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('fishvege_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Mushroom Risotto");
        $menu->setCategory("Fish and vege");
        $menu->setSpecial("6");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('fishvege_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Caesar Salad");
        $menu->setCategory("Fish and vege");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('fishvege_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Trotilla Espanola");
        $menu->setCategory("Fish and vege");
        $menu->setPrice($faker->numberBetween(10,25));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('fishvege_'.$i++, $menu);
        $manager->persist($menu);

        /**
        * Deserts
        */
        $i=1;
        $menu = new Menu();
        $menu->setName("Apple Pie");
        $menu->setCategory("Deserts");
        $menu->setPrice($faker->numberBetween(10,15));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('desert_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Brownie");
        $menu->setCategory("Deserts");
        $menu->setPrice($faker->numberBetween(10,15));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('desert_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Black Forest Cake");
        $menu->setCategory("Deserts");
        $menu->setPrice($faker->numberBetween(10,15));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('desert_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Flan Caramel");
        $menu->setCategory("Deserts");
        $menu->setPrice($faker->numberBetween(10,15));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('desert_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Tiramisu");
        $menu->setCategory("Deserts");
        $menu->setPrice($faker->numberBetween(10,15));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('desert_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Banana Split");
        $menu->setCategory("Deserts");
        $menu->setPrice($faker->numberBetween(10,15));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('desert_'.$i++, $menu);
        $manager->persist($menu);


        /**
         * Drinks & Beverages
         */
        $i=1;
        $menu = new Menu();
        $menu->setName("Bottle of red wine");
        $menu->setCategory("Drinks");
        $menu->setPrice($faker->numberBetween(15,20));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('drink_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Bottle of white wine");
        $menu->setCategory("Drinks");
        $menu->setPrice($faker->numberBetween(10,15));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('drink_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Bottle of Prosseco");
        $menu->setCategory("Drinks");
        $menu->setPrice($faker->numberBetween(10,15));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('drink_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Sparkling Water");
        $menu->setCategory("Drinks");
        $menu->setPrice($faker->numberBetween(5,10));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('drink_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Black Tea");
        $menu->setCategory("Drinks");
        $menu->setPrice($faker->numberBetween(5,10));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('drink_'.$i++, $menu);
        $manager->persist($menu);

        $menu = new Menu();
        $menu->setName("Coffe with milk");
        $menu->setCategory("Drinks");
        $menu->setPrice($faker->numberBetween(5,10));
        $menu->setImage(Urlizer::urlize($menu->getName()).".jpg");
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference('drink_'.$i++, $menu);
        $manager->persist($menu);


        $manager->flush();
    }

    public function loadReservation($manager)
    {
        $faker = \Faker\Factory::create();
        $hours = [
            1 => '16:00',
            2 => '18:00',
            3 => '20:00',
            4 => '22:00',
        ];
        $menu = [
            1 => "soup",
            2 => "appetizer",
            3 => "main",
            4 => "fishvege",
            5 => "desert",
            6 => "drink"
        ];
        //iterate days
        for ($i = 0; $i <= 13; $i++) {

            //iterate hours
            for ($j = 1; $j <= 4; $j++) {
                for ($m = 1; $m <= 12; $m++) {
                    if ($faker->boolean(60)) {
                        $time = new \DateTime(date('d-m-Y H:i',strtotime(date ("Y-m-d"). "  $hours[$j]")));
                        $time->modify('+ '.$i.' days');
                        $reservation = new Reservation();
                        $reservation->setDate(\DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime(('+ '.$i.' days')))));
                        $reservation->setTime(\DateTime::createFromFormat('H:i', $hours[$j]));
                        $reservation->setReservationDate($time);
                        $reservation->setTableDetails($this->getReference('table_' . $m));
                        if ($reservation->getTableDetails()->getIsFamily()) {
                            $reservation->setNumberOfPersons($faker->numberBetween(3, 6));
                        } else {
                            $reservation->setNumberOfPersons($faker->numberBetween(1, 4));
                        }
                        $reservation->setHash(hash('sha1', $reservation->getDate()->format('Y-m-d') . $reservation->getTime()->format('H:i') . $reservation->getTableDetails()->getId()));
                        $reservation->setStatus('Accepted');
                        $reservation->setIp($faker->ipv4);
                        $reservation->setContactName($faker->name);
                        $reservation->setContactPhone($faker->phoneNumber);
                        $manager->persist($reservation);

                    }
                }
            }
        }
        for ($i = 1; $i < date('d'); $i++) {

            //iterate hours
            for ($j = 1; $j <= 4; $j++) {
                for ($m = 1; $m <= 12; $m++) {
                    if ($faker->boolean(60)) {
                        $time = new \DateTime(date('d-m-Y H:i',strtotime(date ("Y-m")."-".$i." ".$hours[$j])));
//                        $time->modify('+ '.$i.' days');
                        $reservation = new Reservation();
                        $reservation->setDate(\DateTime::createFromFormat('Y-m-d', date('Y-m') . '-' . $i));
                        $reservation->setTime(\DateTime::createFromFormat('H:i', $hours[$j]));
                        $reservation->setReservationDate($time);
                        $reservation->setTableDetails($this->getReference('table_'.$m));
                        if ($reservation->getTableDetails()->getIsFamily()) {
                            $reservation->setNumberOfPersons($faker->numberBetween(3, 6));
                        } else {
                            $reservation->setNumberOfPersons($faker->numberBetween(1, 4));
                        }
                        $reservation->setHash(hash('sha1', $reservation->getDate()->format('Y-m-d') . $reservation->getTime()->format('H:i') . $reservation->getTableDetails()->getId()));
                        $reservation->setStatus('Closed');
                        $reservation->setIp($faker->ipv4);
                        $reservation->setContactName($faker->name);
                        $reservation->setContactPhone($faker->phoneNumber);
                        $manager->persist($reservation);


                        for ($k = 1; $k <= $reservation->getNumberOfPersons(); $k++) {
                            //soup
                            if ($faker->boolean(60)) {
                                $name = "soup_" . $faker->numberBetween(1, 6);

                                if ($this->hasReference('reservation_details_'.$reservation->getHash().'_'.$name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }
                                $manager->persist($reservationDetails);
                            }
                            //apetizer
                            if ($faker->boolean(90)) {
                                $name = "appetizer_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_'.$reservation->getHash().'_'.$name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }
                                $manager->persist($reservationDetails);
                            }
                            if ($faker->boolean(60)) {
                                $name = "main_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_'.$reservation->getHash().'_'.$name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }

                                $manager->persist($reservationDetails);
                            } else {
                                $name = "fishvege_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_'.$reservation->getHash().'_'.$name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }
                                $manager->persist($reservationDetails);
                            }
                            if ($faker->boolean(60)) {
                                $name = "desert_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_'.$reservation->getHash().'_'.$name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }
                                $manager->persist($reservationDetails);
                            }
                            if ($faker->boolean(50)) {
                                $name = "drink_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_'.$reservation->getHash().'_'.$name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }
                                $manager->persist($reservationDetails);
                            }

                        }

                    }

                }
            }


        }
        $manager->flush();
    }

    protected function increaseQuantity($reservation, $name) {
        $referenceName = 'reservation_details_'.$reservation->getHash().'_'.$name;
        $reservationDetails = $this->getReference($referenceName);
        $reservationDetails->setQuantity($reservationDetails->getQuantity()+1);
        $reservationDetails->setPrice($reservationDetails->getPrice()+$reservationDetails->getName()->getPrice());
        return $reservationDetails;

    }

    protected function newReservationDetails($reservation, $name) {
        $reservationDetails = new ReservationDetail();
        $reservationDetails->setReservationId($reservation);
        $reservationDetails->setName($this->getReference($name));
        $reservationDetails->setQuantity(1);
        $reservationDetails->setReservationId($reservation);
        $reservationDetails->setPrice($reservationDetails->getName()->getPrice());
        $reservationDetails->setTaxValue($reservationDetails->getName()->getPrice() * 0.15);
        $referenceName = 'reservation_details_'.$reservation->getHash().'_'.$name;
        $this->addReference($referenceName, $reservationDetails);
        return $reservationDetails;

    }
}