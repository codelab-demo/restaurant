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

        $soups = $menu->getMenuItems('Soups');
        $appetizers = $menu->getMenuItems('Appetizers');
        $mains = $menu->getMenuItems('Main dish');
        $vege = $menu->getMenuItems('Fish and vege');
        $deserts = $menu->getMenuItems('Deserts');
        $drinks = $menu->getMenuItems('Drinks');

        $specials = $menu->findSpecials();

        return $this->render('\Portal\menu.html.twig', [
            'soups' => $soups,
            'appetizers' => $appetizers,
            'mains' => $mains,
            'vege' => $vege,
            'deserts' => $deserts,
            'drinks' => $drinks,
            'specials' => $specials,
        ]);
    }
}
