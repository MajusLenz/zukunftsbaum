<?php
namespace App\Controller\Content\Detail;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class DetailController extends AbstractController{

    /**
     * @Route("/detail/{id}", name="detail", requirements={"id"="\d+"})
     */
    public function detailAction($id, string $treePicsDir) {
        $doctrine = $this->getDoctrine();
        $treeRepository = $doctrine->getRepository(Tree::class);

        $tree = $treeRepository->find($id);

        if (empty($tree)) {
            throw new NotFoundHttpException("Tree not found!");
        }

        // TODO transform "20+" etc. in vernünftige wörter

        return $this->render('content/detail/detail.html.twig', [
            "treePicsDir" => $treePicsDir . "/",
            "tree" => $tree
        ]);
    }



}