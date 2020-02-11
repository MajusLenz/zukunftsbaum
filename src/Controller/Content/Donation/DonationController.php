<?php
namespace App\Controller\Content\Donation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class DonationController extends AbstractController{

    /**
    * @Route("/Spenden", name="donation")
    */
    public function donationAction() {

        return $this->render('content/donation/donation.html.twig', []);
    }
}