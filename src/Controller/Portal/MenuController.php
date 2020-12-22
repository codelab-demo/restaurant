<?php

namespace App\Controller\Portal;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/menu", name="app_menu")
     */
    public function index(MenuRepository $menu): Response
    {

        $soups = $menu->findBy(
            [
                'category' => 'Soups'
            ]
        );

        $appetizers = $menu->findBy(
            [
                'category' => 'Appetizers'
            ]
        );

        $mains = $menu->findBy(
            [
                'category' => 'Main dish'
            ]
        );

        $vege = $menu->findBy(
            [
                'category' => 'Fish and vege'
            ]
        );
        $deserts = $menu->findBy(
            [
                'category' => 'Deserts'
            ]
        );
        $drinks = $menu->findBy(
            [
                'category' => 'Drinks'
            ]
        );

        $specials = $menu->findSpecials();

        $days = array(
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        );

        return $this->render('\Portal\menu.html.twig', [
            'soups' => $soups,
            'appetizers' => $appetizers,
            'mains' => $mains,
            'vege' => $vege,
            'deserts' => $deserts,
            'drinks' => $drinks,
            'specials' => $specials,
            'days' => $days

        ]);
    }
}
