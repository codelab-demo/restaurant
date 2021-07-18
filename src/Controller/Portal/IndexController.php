<?php

namespace App\Controller\Portal;

use App\Repository\MenuRepository;
use App\Service\DaysHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(MenuRepository $menu): Response
    {

        $specials = $menu->findSpecials();

        $days = DaysHelper::daysOfWeek();

        return $this->render('Portal/index.html.twig', [

            'specials' => $specials,
            'days' => $days
        ]);
    }
}
