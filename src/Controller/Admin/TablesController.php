<?php

namespace App\Controller\Admin;

use App\Entity\Board;
use App\Repository\BoardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class TablesController extends AbstractController
{

    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * List of all tables
     *
     * @Route("/admin/tables", name="app_admin_tables")
     */
    public function showTables(BoardRepository $boardRepository): Response
    {
        $tables = $boardRepository->findAll();
        return $this->render('Admin/tablesList.html.twig', [
            'tables'=>$tables
        ]);
    }

    /**
     * Edit tables
     *
     * @Route("/admin/table/{id}/edit", name="app_admin_table_edit")
     */
    public function editTable(Board $table, Request $request,EntityManagerInterface $em): Response
    {
        $persons = intVal($request->request->get('persons'));
        $minPersons = intVal($request->request->get('minPersons'));
        $name = filter_var($request->request->get('name'),FILTER_SANITIZE_STRING);
        $type = filter_var($request->request->get('table'),FILTER_SANITIZE_STRING);

        if($table) {
            if ($request->isMethod('POST')) {

                if($name == '') {
                $errors[] = 'Name cannot be empty.';
                $this->addFlash('error','Name cannot be empty.');
                }

                if($persons < $minPersons) {
                    $errors[] = 'Minumum persons can be greater then max persons.';
                    $this->addFlash('error','Minumum persons can be greater then max persons.');
                }
                if($minPersons < 1) {
                    $errors[] = 'Minumum persons can\'t be less than 1.';
                    $this->addFlash('error','Minumum persons can\'t be less than 1.');
                }
                if(empty($errors)) {
                    $table->setNumberOfPersons($persons);
                    $table->setMinNumberOfPersons($minPersons);
                    $table->setName($name);
                    if ($type == 'chef') {
                        $table->setIsChef(true);
                    } elseif ($type == 'family') {
                        $table->setIsFamily(true);
                    }
                    $em->persist($table);
                    $em->flush();

                    $this->addFlash('success', 'Table has been updated.');
                }
                return new RedirectResponse($this->router->generate('app_admin_tables'));
            }
            return $this->render('Admin/tableEdit.html.twig', [
                'table'=>$table
            ]);
        }
        return new RedirectResponse($this->router->generate('app_admin_tables'));
    }
}
