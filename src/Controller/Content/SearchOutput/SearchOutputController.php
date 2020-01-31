<?php
namespace App\Controller\Content\SearchOutput;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class SearchOutputController extends AbstractController{

    /**
     * @Route("/suchergebnis", name="search_output")
     */
    public function searchOutputAction(string $treePicsDir) {

        $doctrine = $this->getDoctrine();

        $trees = $doctrine->getRepository(Tree::class)->findAll();

        // find informations that should be searched with and save them in extra place to make them better accessible for twig
        $searchableInformationNames = ["Wuchshöhe", "Wuchsbreite", "Lichtbedarf", "Bienenweide"];

        foreach($trees as $index => $tree) {
            $searchableInformations = array();

            foreach($tree->getInformations() as $info) {
                $infoIsSearchable = in_array($info->getName(), $searchableInformationNames);

                if($infoIsSearchable) {
                    $searchableInformations[] = $info;
                }
            }

            $trees[$index]->setSearchableInformations($searchableInformations);
        }

        return $this->render('content/searchOutput/searchOutput.html.twig', [
            "trees" => $trees,
            "treePicsDir" => $treePicsDir . "/"
        ]);
    }



}