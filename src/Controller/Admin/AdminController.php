<?php
namespace App\Controller\Admin;

use App\Entity\TreePicture;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_index")
     */
    public function adminIndexAction(string $treePicsDir)
    {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();

        $trees = $doctrine->getRepository(Tree::class)->findAll();

        return $this->render('admin/admin_index.html.twig', [
            "trees" => $trees,
            "treePicsDir" => $treePicsDir . "/"
        ]);
    }


    /**
     * @Route("/admin/deleteTree/{id}", name="admin_delete_tree", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function adminDeleteTreeAction($id, string $treePicsDirFull)
    {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();
        $treeRepository = $doctrine->getRepository(Tree::class);

        $tree = $treeRepository->find($id);

        if (!empty($tree)) {

            // delete tree-pics
            foreach ($tree->getPictures() as $pic) {
                try {
                    unlink($treePicsDirFull . "/" . $pic->getPath());
                } catch (ErrorException $e) {
                }

                $entityManager->remove($pic);
            }

            $entityManager->remove($tree);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_index');
    }


    /**
     * @Route("/admin/newTree", name="admin_new_tree", methods={"POST"})
     */
    public function adminNewTreeAction(Request $request, string $treePicsDirFull)
    {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();
        $treeRepository = $doctrine->getRepository(Tree::class);
        $treeInformationRepository = $doctrine->getRepository(TreeInformation::class);
        $postParams = $request->request;

        $name = $postParams->get('name');
        $name = trim($name);

        if (empty($name)) {
            throw new HttpException(400, "Tree-Name must not be empty!");
        }

        $duplicateTree = $treeRepository->findOneBy(["name" => $name]);
        if ($duplicateTree !== null) {
            throw new HttpException(400, "Tree-Name must be unique!");
        }

        $newTree = new Tree();
        $newTree->setName($name);

        // persist tree-informations
        $treeInformations = $this->getTreeInformationsFromPostParams($postParams);
        $this->persistTreeInformationsForGivenTree($newTree, $treeInformations, $treeInformationRepository, $entityManager);

        // upload tree-pictures and persist their path
        $files = $request->files;
        $pictures = $files->get("pictures");
        $this->persistTreePicturesForGivenTree($newTree, $pictures, $treePicsDirFull, $entityManager);

        $entityManager->persist($newTree);
        $entityManager->flush();

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @Route("/admin/editTree/{id}", name="admin_edit_tree", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function adminEditTreeAction($id, Request $request, string $treePicsDirFull)
    {
        // TODO check if authenticated

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();
        $treeRepository = $doctrine->getRepository(Tree::class);
        $treeInformationRepository = $doctrine->getRepository(TreeInformation::class);
        $treePicturesRepository = $doctrine->getRepository(TreePicture::class);
        $postParams = $request->request;

        $tree = $treeRepository->find($id);
        if ( empty($tree) ) {
            throw new HttpException(400, "Tree with ID " . $id . " could not be found.");
        }

        $newName = $postParams->get('name');
        $newName = trim($newName);
        $oldName = $tree->getName();

        if ($newName !== $oldName) {
            if ( empty($newName) ) {
                throw new HttpException(400, "Tree-Name must not be empty!");
            }

            $duplicateTree = $treeRepository->findOneBy(["name" => $newName]);
            if ($duplicateTree !== null) {
                throw new HttpException(400, "Tree-Name must be unique!");
            }

            $tree->setName($newName);
        }

        // edit Informations
        $tree->clearInformations();
        $treeInformations = $this->getTreeInformationsFromPostParams($postParams);
        $this->persistTreeInformationsForGivenTree($tree, $treeInformations, $treeInformationRepository, $entityManager);

        // edit pictures
        $this->deletePicturesByPostParamsForGivenTree($tree, $postParams, $treePicsDirFull, $entityManager, $treePicturesRepository);
        $files = $request->files;
        $newPictures = $files->get("pictures");
        $this->persistTreePicturesForGivenTree($tree, $newPictures, $treePicsDirFull, $entityManager);

        $entityManager->persist($tree);
        $entityManager->flush();

        return $this->redirectToRoute('admin_index');
    }


    /////////////////// PRIVATE METHODS ///////////////////

    /**
     * search all tree-information-names in post-params and link them to their corresponding values. return associative array with info-entries.
     * @param $postParams
     * @return array
     */
    private function getTreeInformationsFromPostParams($postParams)
    {
        $treeInformations = array();

        foreach ($postParams as $key => $param) {
            $isInformation = strpos($key, "information") !== false;

            if ($isInformation) {
                if (empty($key) || empty($param)) {
                    throw new HttpException(400, "Tree-Information Names and Values must not be empty!");
                }

                $isInformationName = strpos($key, "information-name-") !== false;

                if ($isInformationName) {
                    $informationName = trim($param);
                    $informationNumber = str_replace("information-name-", "", $key);

                    $informationValue = $postParams->get("information-value-$informationNumber");
                    $informationValue = trim($informationValue);

                    array_push($treeInformations, array("name" => $informationName, "value" => $informationValue));
                }
            }
        }

        return $treeInformations;
    }

    /**
     * @param $tree
     * @param $postParams
     * @param $treePicsDirFull
     * @param $entityManager
     * @param $treePicturesRepository
     */
    private function deletePicturesByPostParamsForGivenTree($tree, $postParams, $treePicsDirFull, $entityManager, $treePicturesRepository) {

        foreach ($postParams as $key => $param) {
            $isDeletePictureParam = strpos($key, "delete-pic-") !== false;

            if($isDeletePictureParam) {
                $id = $param;
                $pic = $treePicturesRepository->find($id);

                if( !empty($pic) ) {
                    $tree->removePicture($pic);

                    try {
                        unlink($treePicsDirFull . "/" . $pic->getPath());
                    } catch (ErrorException $e) {
                    }

                    $entityManager->remove($pic);
                }
            }
        }
    }

    /**
     * @param $tree
     * @param $treeInformations
     * @param $treeInformationRepository
     * @param $entityManager
     */
    private function persistTreeInformationsForGivenTree($tree, $treeInformations, $treeInformationRepository, $entityManager)
    {

        foreach ($treeInformations as $info) {
            $duplicateInformation = $treeInformationRepository->findOneBy([
                "name" => $info["name"],
                "value" => $info["value"]
            ]);
            if ($duplicateInformation === null) {
                $newInformation = new TreeInformation();
                $newInformation->setName($info["name"]);
                $newInformation->setValue($info["value"]);
            } else {
                $newInformation = $duplicateInformation;
            }

            $tree->addInformation($newInformation);
            $entityManager->persist($newInformation);
        }
    }

    /**
     * @param $tree
     * @param $pictures
     * @param $treePicsDirFull
     * @param $entityManager
     */
    private function persistTreePicturesForGivenTree($tree, $pictures, $treePicsDirFull, $entityManager) {

        foreach ($pictures as $pic) {
            $filename = uniqid();
            $extension = strtolower($pic->guessExtension());
            $path = $filename . "." . $extension;
            $isImageType = in_array($extension, array("png", "gif", "jpg", "jpeg", "webp"));

            if ($isImageType) {
                $pic->move($treePicsDirFull, $path);
                $newTreePicture = new TreePicture();
                $newTreePicture->setPath($path);
                $tree->addPicture($newTreePicture);
                $entityManager->persist($newTreePicture);
            }
        }
    }



}
