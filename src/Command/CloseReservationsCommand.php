<?php


namespace App\Command;


use App\Entity\Menu;
use App\Entity\ReservationDetail;
use App\Repository\MenuRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CloseReservationsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:close';
    private $entityManager;
    private $reservationRepository;
    private $menuRepository;
    private $faker;
    private $references = [];

    public function __construct(EntityManagerInterface $entityManager, ReservationRepository $reservationRepository, MenuRepository $menuRepository)
    {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
        $this->menuRepository = $menuRepository;
        $this->faker = \Faker\Factory::create();

        parent::__construct();
    }

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reservationToCloseList = $this->reservationRepository->findNonclosedReservations();

        $MenuList = $this->menuRepository->findAll();

        $soupCounter = $appetizersCounter = $mainDishCounter = $fishCounter = $desertsCounter = $drinkCounter = 0;
        foreach ($MenuList as $menu) {
            if ($menu->getCategory() === "Soups") {
                $category = 'soup';
                $soupCounter++;
                $menuName = $category . '_' . $soupCounter;
            }
            if ($menu->getCategory() === "Appetizers") {
                $category = 'appetizer';
                $appetizersCounter++;
                $menuName = $category . '_' . $appetizersCounter;
            }
            if ($menu->getCategory() === "Main dish") {
                $category = 'main';
                $mainDishCounter++;
                $menuName = $category . '_' . $mainDishCounter;
            }
            if ($menu->getCategory() === "Fish and vege") {
                $category = 'fishvege';
                $fishCounter++;
                $menuName = $category . '_' . $fishCounter;
            }
            if ($menu->getCategory() === "Deserts") {
                $category = 'desert';
                $desertsCounter++;
                $menuName = $category . '_' . $desertsCounter;
            }
            if ($menu->getCategory() === "Drinks") {
                $category = 'drink';
                $drinkCounter++;
                $menuName = $category . '_' . $drinkCounter;
            }
            $this->addReference($menuName, $menu);
        }


        foreach ($reservationToCloseList as $reservation) {
            for ($k = 1; $k <= $reservation->getNumberOfPersons(); $k++) {
                //soup
                if ($this->faker->boolean(60)) {
                    $name = "soup_" . $this->faker->numberBetween(1, 6);

                    if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                        $this->increaseQuantity($reservation, $name);
                    } else {
                        $this->newReservationDetails($reservation, $name);
                    }

                }
                //apetizer
                if ($this->faker->boolean(90)) {
                    $name = "appetizer_" . $this->faker->numberBetween(1, 6);
                    if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                        $this->increaseQuantity($reservation, $name);
                    } else {
                        $this->newReservationDetails($reservation, $name);
                    }
                }
                if ($this->faker->boolean(60)) {
                    $name = "main_" . $this->faker->numberBetween(1, 6);
                    if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                        $this->increaseQuantity($reservation, $name);
                    } else {
                        $this->newReservationDetails($reservation, $name);
                    }
                } else {
                    $name = "fishvege_" . $this->faker->numberBetween(1, 6);
                    if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                        $this->increaseQuantity($reservation, $name);
                    } else {
                        $this->newReservationDetails($reservation, $name);
                    }
                }
                if ($this->faker->boolean(60)) {
                    $name = "desert_" . $this->faker->numberBetween(1, 6);
                    if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                        $this->increaseQuantity($reservation, $name);
                    } else {
                        $this->newReservationDetails($reservation, $name);
                    }
                }
                if ($this->faker->boolean(50)) {
                    $name = "drink_" . $this->faker->numberBetween(1, 6);
                    if ($this->hasReference('reservation_details_' . $reservation->getHash() . '_' . $name)) {
                        $this->increaseQuantity($reservation, $name);
                    } else {
                        $this->newReservationDetails($reservation, $name);
                    }
                }

            }
            $reservation->setStatus("Closed");
        }
        $this->entityManager->flush();
        return Command::SUCCESS;

    }

    protected function addReference(string $reference, $obj)
    {
        $this->references[$reference] = $obj;
    }

    protected function hasReference(string $reference)
    {
        return array_key_exists($reference, $this->references);
    }

    protected function increaseQuantity($reservation, $name)
    {
        $referenceName = 'reservation_details_' . $reservation->getHash() . '_' . $name;
        $reservationDetails = $this->getReference($referenceName);
        $reservationDetails->setQuantity($reservationDetails->getQuantity() + 1);
        $reservationDetails->setPrice($reservationDetails->getPrice() + $reservationDetails->getName()->getPrice());
        $this->entityManager->persist($reservationDetails);
    }

    protected function getReference(string $reference)
    {
        return $this->references[$reference];
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
        $this->entityManager->persist($reservationDetails);

    }
}