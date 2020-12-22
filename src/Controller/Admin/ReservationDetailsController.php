<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Entity\Reservation;
use App\Entity\ReservationDetail;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ReservationDetailsController extends AbstractController
{

    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    /**
     * Delete a Reservation Detail
     *
     * @Route("/admin/reservation_details/{id}/delete", name="app_admin_reservationDetails_delete")
     */
    public function delete(ReservationDetail $reservationDetail, EntityManagerInterface $em): Response
    {
        // if reservationDetails exists
        if($reservationDetail) {
            $hash = $reservationDetail->getReservationId()->getHash();
            $em->remove($reservationDetail);
            $em->flush();
            $this->addFlash('success','Reservation details has been deleted.');
        } else {
            return new RedirectResponse($this->router->generate('app_admin_reservations'));
        }
        return new RedirectResponse($this->router->generate('app_admin_reservation_details', array('hash' => $hash)));
    }
    /**
     * Edit a Reservation Detail
     *
     * @Route("/admin/reservation_details/{id}/edit", name="app_admin_reservationDetails_edit")
     */
    public function edit(ReservationDetail $reservationDetail, EntityManagerInterface $em, Request $request): Response
    {
        // if reservationDetails exists
        if($reservationDetail) {
            // hash for redirect page
            $hash = $reservationDetail->getReservationId()->getHash();

            // if form send and reservation is not closed
            if ($request->isMethod('POST') && $reservationDetail->getReservationId()->getStatus()=="Accepted") {

                $isValidToken = $this->isCsrfTokenValid($reservationDetail->getId(), $request->request->get('token'));

                if(!$isValidToken) {
                    throw new BadRequestHttpException('Wrong request');
                }

                // checking if posted data are correct
                $quantity = intVal($request->request->get('quantity'));
                $price = floatval($request->request->get('price'));

                $error = 0;
                if($quantity < 1 || $quantity > 10) {
                    $this->addFlash('error', 'You can change quantity to range 0..10');
                    $error = 1;
                }
                if($price < 0.01  || $price > 100) {
                    $this->addFlash('error', 'You can change price to range 0..100');
                    $error = 1;
                }

                // if data are correct, save it and return
                if(!$error) {
                   $reservationDetail->setPrice($quantity*$price);
                   $reservationDetail->setQuantity($quantity);
                   $em->persist($reservationDetail);
                   $em->flush();
                    $this->addFlash('success', 'Position '.$reservationDetail->getName()->getName().' has been modified.');
                   return new RedirectResponse($this->router->generate('app_admin_reservation_details', array('hash' => $hash)));
                }
            }
        }
        return $this->render('Admin/reservationDetailItem.twig', [
                'item' => $reservationDetail
        ]);
    }

    /**
     * Add a new Reservation Detail
     *
     * @Route("/admin/reservation_details/{hash}/add", name="app_admin_reservationDetails_add")
     */
    public function add(Reservation $reservation, EntityManagerInterface $em, Request $request): Response
    {
        $menuRepo = $em->getRepository(Menu::class);

        // if base reservation exists
        if($reservation) {
            $hash = $reservation->getHash();

            // if form send and reservation is not closed
            if ($request->isMethod('POST') && $reservation->getStatus()=="Accepted") {

                // checking if posted data are correct
                $isValidToken = $this->isCsrfTokenValid($reservation->getId(), $request->request->get('token'));

                if(!$isValidToken) {
                    throw new BadRequestHttpException('Wrong request');
                }

                $quantity = intVal($request->request->get('quantity'));
                $price = floatval($request->request->get('price'));
                $item = intval($request->request->get('item'));

                $error = 0;
                if($quantity < 1 || $quantity > 10) {
                    $this->addFlash('error', 'You can change quantity to range 0..10');
                    $error = 1;
                }
                if($price < 0.01  || $price > 100) {
                    $this->addFlash('error', 'You can change price to range 0..100');
                    $error = 1;
                }

                $menuItem = $menuRepo->find($item);

                if(!$menuItem) {
                    $this->addFlash('error', 'Wrong menu position.');
                    $error = 1;
                }

                // if no errors create datails and save it
                if(!$error) {
                    $reservationDetail = new ReservationDetail();
                    $reservationDetail->setPrice($quantity*$price);
                    $reservationDetail->setQuantity($quantity);
                    $reservationDetail->setName($menuItem);
                    $reservationDetail->setReservationId($reservation);
                    $reservationDetail->setTaxValue($quantity*$price*0.15);
                    $em->persist($reservationDetail);
                    $em->flush();
                    $this->addFlash('success', 'Position '.$menuItem->getName().' has been added.');
                    return new RedirectResponse($this->router->generate('app_admin_reservation_details', array('hash' => $hash)));
                }
            }
        }

        // if reservation no exists, we go here
        $menuAll = $menuRepo->findAll();

        // prepare data for templates
        $menuItems = [];
        foreach ($menuAll as $item) {
            $menuObj = new \stdClass();
            $menuObj->name = $item->getName();
            $menuObj->price = $item->getPrice();
            $menuObj->id = $item->getId();
            $menuItems[$item->getCategory()][] = $menuObj;
        }


        return $this->render('Admin/addReservationDetail.html.twig', [
            'menuItems'=> $menuItems,
            'id'=>$reservation->getId()
        ]);
    }



}
