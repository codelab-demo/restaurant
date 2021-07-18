<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MenuController extends AbstractController
{

    private $router;
    private $logger;

    public function __construct(RouterInterface $router, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * @Route("/admin/menu", name="app_admin_menu")
     */
    public function showAll(MenuRepository $menuRepository): Response
    {
        $menu = $menuRepository->findAll();

        return $this->render('Admin/menuList.html.twig', [
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/admin/menu/{id}/edit", name="app_admin_menu_edit")
     */
    public function editMenu(Menu $menu, Request $request, EntityManagerInterface $em,ValidatorInterface $validator): Response
    {

        if($menu)

            $menuRepo = $em->getRepository(Menu::class);

            if ($request->isMethod('POST')) {


                $price = floatVal($request->request->get('price'));
                $special = intVal($request->request->get('special'));
                $name = filter_var($request->request->get('name'), FILTER_SANITIZE_STRING);
                $category = filter_var($request->request->get('category'), FILTER_SANITIZE_STRING);
                $description = trim(filter_var($request->request->get('description'), FILTER_SANITIZE_STRING));

                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $request->files->get('image');
                $errors = [];

                if ($uploadedFile) {
                    $this->ValidateImage($uploadedFile, $validator, $errors);
                }

                $this->validateInput($price, $errors, $name);

                if (empty($errors)) {

                    if ($special) {

                        $this->replaceSpecial($menuRepo, $special, $menu, $em);
                    }
                    if($uploadedFile) {
                        $newFilename = $this->moveUploadedFile($uploadedFile);
                        $menu->setImage($newFilename);
                    }

                    $menu->setSpecial($special);
                    $menu->setName($name);
                    $menu->setPrice($price);
                    $menu->setCategory($category);
                    $menu->setDescription($description);

                    $em->persist($menu);
                    $em->flush();

                    $this->logger->info('Menu item '.$menu->getId().' has been edited');
                    $this->addFlash('success', 'Menu item has been edited and saved.');
                    return new RedirectResponse($this->router->generate('app_admin_menu'));
                }
            }

        $days = array(
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        );

        return $this->render('Admin/menuEdit.html.twig', [
            'menu' => $menu,
            'daysOfWeek' => $days,
            'categories' => array_map('current', $menuRepo->getCategories())
        ]);
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param ValidatorInterface $validator
     * @return array
     */
    public function ValidateImage(UploadedFile $uploadedFile, ValidatorInterface $validator, &$errors): array
    {
            $violations = $validator->validate(
                $uploadedFile,
                new Image([
                    'maxSize' => '2M'
                ])
            );

            if ($violations->count() > 0) {
                /** @var ConstraintViolation $violation */
                $violation = $violations[0];
                $errors[] = 'Wrong image.';
                $this->addFlash('error', $violation->getMessage());
            }

    }

    /**
     * @param float $price
     * @param $errors
     * @param $name
     * @return mixed
     */
    public function validateInput(float $price, &$errors, $name)
    {
        if ($price < 0.01) {
            $errors[] = 'Price must be greater than 0.';
            $this->addFlash('error', 'Price must be greater than 0.');
        }

        if ($name == '') {
            $errors[] = 'Name cannot be empty.';
            $this->addFlash('error', 'Name cannot be empty.');
        }

    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string
     */
    public function moveUploadedFile(UploadedFile $uploadedFile): string
    {
        $destination = $this->getParameter('kernel.project_dir') . '/public/img/menu/';

        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
        $uploadedFile->move(
            $destination,
            $newFilename
        );
        return $newFilename;
    }

    /**
     * @param $menuRepo
     * @param int $special
     * @param Menu $menu
     * @param EntityManagerInterface $em
     */
    public function replaceSpecial($menuRepo, int $special, Menu $menu, EntityManagerInterface $em): void
    {
        $menuOld = $menuRepo->findOneBy([
            'special' => $special
        ]);
        if ($menuOld) {
            if ($menuOld->getId() != $menu->getId()) {
                $menuOld->setSpecial();
                $em->persist($menuOld);
                $em->flush();
            }
        }
    }


}
