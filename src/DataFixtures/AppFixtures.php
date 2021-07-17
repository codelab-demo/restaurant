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

            if (in_array($i, array(3, 4))) {
                $table = $this->createTable(
                    4,
                    'Chef table',
                    $i,
                    false,
                    true,
                    6,
                    'Private chef’s table allow you and your guests to enjoy the theatre of the kitchen and meet the chef');

            } else if (in_array($i, array(3, 4))) {
                $table = $this->createTable(
                    6,
                    'Family table',
                    $i,
                    true,
                    false,
                    6,
                    'Family table offer spacious table for up to 6 guests in the side of the restaurant to make a private atmosphere');
            } else {
                $table = $this->createTable(
                    4,
                    'Standard table',
                    $i,
                    false,
                    false,
                    6,
                    'A standard table for up to 4 people to enjoying dining gourmet food');
            }

            $this->addReference('table_' . $i, $table);
            $manager->persist($table);
        }

        $manager->flush();
    }

    /**
     * @param Board $table
     * @param int $i
     */
    public function createTable(int $minNumberOfPersons, $title, int $i, bool $isFamily, bool $isChef, int $maxNumberOfPersons, $tooltip): Board
    {
        $table = new Board();

        $table->setMinNumberOfPersons($minNumberOfPersons);
        $table->setName('#' . $i . ' - ' . $title);
        $table->setIsFamily($isFamily);
        $table->setIsChef($isChef);
        $table->setNumberOfPersons($maxNumberOfPersons);
        $table->setTooltip($tooltip);

        return $table;
    }

    public function loadMenu(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $this->createSoups($faker, $manager, [1 => 1]);

        $this->createAppetizers($faker, $manager, [2 => 2]);

        $this->createMainDish($faker, $manager, [1 => 3, 2 => 7, 5 => 4]);

        $this->createFishVege($faker, $manager, [1 => 5, 3 => 6]);

        $this->createDeserts($faker, $manager, []);

        $this->createDrinks($faker, $manager, []);

        $manager->flush();
    }

    private function createSoups($faker, $manager, array $special)
    {
        $category = 'Soups';
        $key = 'soup';
        $dishes = ['Tomato soup', 'Chiken soup', 'Corn soup', 'Fish soup', 'Onion soup', 'Pumpkin soup'];
        for ($i = 0; $i <= 5; $i++) {
            $this->createMenuItem($faker, $manager, $category, $dishes[$i], $key, $special, $i);
        }
    }

    /**
     * @param $faker
     * @return array
     */
    public function createMenuItem($faker, $manager, $category, $name, $key, $special, $i): array
    {
        $menu = new Menu();
        foreach ($special as $item) {
            if ($i === $item[0]) {
                $menu->setSpecial($item[1]);
            }
        }

        $menu->setName($name);
        $menu->setCategory($category);
        $menu->setPrice($faker->numberBetween(10, 25));
        $menu->setImage(Urlizer::urlize($menu->getName()) . '.jpg');
        $menu->setDescription($faker->sentence($nbWords = 10, $variableNbWords = true));
        $this->addReference($key . '_' . $i, $menu);
        $manager->persist($menu);
    }

    private function createAppetizers($faker, ObjectManager $manager, array $special)
    {
        $category = 'Appetizers';
        $key = 'appetizer';
        $dishes = ['Shrimp tempura', 'Fried cheese', 'Beef tartar', 'Beef carpaccio', 'Onion rings', 'Crispy prawns'];
        for ($i = 0; $i <= 5; $i++) {
            $this->createMenuItem($faker, $manager, $category, $dishes[$i], $key, $special, $i);
        }
    }

    private function createMainDish($faker, ObjectManager $manager, array $special)
    {
        $category = 'Main dish';
        $key = 'main';
        $dishes = ['Chicken breast', 'New York steak', 'BBQ Burger', 'Spaghetti', 'Lasagne', 'Beef Sirloin'];
        for ($i = 0; $i <= 5; $i++) {
            $this->createMenuItem($faker, $manager, $category, $dishes[$i], $key, $special, $i);
        }
    }

    private function createFishVege($faker, ObjectManager $manager, array $special)
    {
        $category = 'Fish and vege';
        $key = 'fishvege';
        $dishes = ['Roast Salmon', 'Grilled trout', 'Smoked Salmon', 'Mushroom Risotto', 'Caesar Salad', 'Trotilla Espanola'];
        for ($i = 0; $i <= 5; $i++) {
            $this->createMenuItem($faker, $manager, $category, $dishes[$i], $key, $special, $i);
        }
    }

    private function createDeserts($faker, ObjectManager $manager, array $special)
    {
        $category = 'Deserts';
        $key = 'desert';
        $dishes = ['Apple Pie', 'Brownie', 'Black Forest Cake', 'Flan Caramel', 'Tiramisu', 'Banana Split'];
        for ($i = 0; $i <= 5; $i++) {
            $this->createMenuItem($faker, $manager, $category, $dishes[$i], $key, $special, $i);
        }
    }

    private function createDrinks($faker, ObjectManager $manager, array $special)
    {
        $category = 'Drinks';
        $key = 'drink';
        $dishes = ['Bottle of red wine', 'Bottle of white wine', 'Bottle of Prosseco', 'Sparkling Water', 'Black Tea', 'Coffe with milk'];
        for ($i = 0; $i <= 5; $i++) {
            $this->createMenuItem($faker, $manager, $category, $dishes[$i], $key, $special, $i);
        }
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
                        $time = new \DateTime(date('d-m-Y H:i', strtotime(date("Y-m-d") . "  $hours[$j]")));
                        $time->modify('+ ' . $i . ' days');
                        $reservation = new Reservation();
                        $reservation->setDate(\DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime(('+ ' . $i . ' days')))));
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
                        $time = new \DateTime(date('d-m-Y H:i', strtotime(date("Y-m") . "-" . $i . " " . $hours[$j])));
                        $reservation = new Reservation();
                        $reservation->setDate(\DateTime::createFromFormat('Y-m-d', date('Y-m') . '-' . $i));
                        $reservation->setTime(\DateTime::createFromFormat('H:i', $hours[$j]));
                        $reservation->setReservationDate($time);
                        $reservation->setTableDetails($this->getReference('table_' . $m));
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

                                if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }
                                $manager->persist($reservationDetails);
                            }
                            //apetizer
                            if ($faker->boolean(90)) {
                                $name = "appetizer_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }
                                $manager->persist($reservationDetails);
                            }
                            if ($faker->boolean(60)) {
                                $name = "main_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }

                                $manager->persist($reservationDetails);
                            } else {
                                $name = "fishvege_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }
                                $manager->persist($reservationDetails);
                            }
                            if ($faker->boolean(60)) {
                                $name = "desert_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                                    $reservationDetails = $this->increaseQuantity($reservation, $name);
                                } else {
                                    $reservationDetails = $this->newReservationDetails($reservation, $name);
                                }
                                $manager->persist($reservationDetails);
                            }
                            if ($faker->boolean(50)) {
                                $name = "drink_" . $faker->numberBetween(1, 6);
                                if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
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

    protected function increaseQuantity($reservation, $name)
    {
        $referenceName = 'reservation_details_' . $reservation->getHash() . '_' . $name;
        $reservationDetails = $this->getReference($referenceName);
        $reservationDetails->setQuantity($reservationDetails->getQuantity() + 1);
        $reservationDetails->setPrice($reservationDetails->getPrice() + $reservationDetails->getName()->getPrice());
        return $reservationDetails;

    }

    protected function newReservationDetails($reservation, $name)
    {
        $reservationDetails = new ReservationDetail();
        $reservationDetails->setReservationId($reservation);
        $reservationDetails->setName($this->getReference($name));
        $reservationDetails->setQuantity(1);
        $reservationDetails->setReservationId($reservation);
        $reservationDetails->setPrice($reservationDetails->getName()->getPrice());
        $reservationDetails->setTaxValue($reservationDetails->getName()->getPrice() * 0.15);
        $referenceName = 'reservation_details_' . $reservation->getHash() . '_' . $name;
        $this->addReference($referenceName, $reservationDetails);
        return $reservationDetails;

    }
}