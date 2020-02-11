<?php
namespace App\Controller\Content\Contact;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class ContactController extends AbstractController{

    /**
    * @Route("/Kontakt", name="contact")
    */
    public function contactAction() {

        return $this->render('content/contact/contact.html.twig', []);
    }
}