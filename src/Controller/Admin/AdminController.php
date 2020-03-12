<?php
namespace App\Controller\Admin;

use App\Entity\TreePicture;
use App\Helper\ImagingHelper;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tree;
use App\Entity\TreeInformation;
use Symfony\Component\Finder\Finder;


class AdminController extends AbstractController {

    const ALLOWED_PICTURE_EXTENSIONS = array("png", "gif", "jpg", "jpeg", "webp", "bmp");

    private $imagingHelper;

    public function __construct() {
        $this->imagingHelper = new ImagingHelper();
    }


    /**
     * @Route("/admin", name="admin_index")
     */
    public function adminIndexAction(string $treePicsDir)
    {
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
    public function adminNewTreeAction(Request $request, string $treePicsDirFull, string $thumbnailExtension)
    {
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
        $this->persistTreePictureUploadsForGivenTree($newTree, $pictures, $treePicsDirFull, $thumbnailExtension, $entityManager);

        $entityManager->persist($newTree);
        $entityManager->flush();

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @Route("/admin/editTree/{id}", name="admin_edit_tree", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function adminEditTreeAction($id, Request $request, string $treePicsDirFull, string $thumbnailExtension)
    {
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
        $this->deletePicturesByPostParamsForGivenTree($tree, $postParams, $treePicsDirFull, $thumbnailExtension, $entityManager, $treePicturesRepository);
        $files = $request->files;
        $newPictures = $files->get("pictures");
        $this->persistTreePictureUploadsForGivenTree($tree, $newPictures, $treePicsDirFull, $thumbnailExtension, $entityManager);

        $entityManager->persist($tree);
        $entityManager->flush();

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @Route("/admin/uploadCsvResult", name="admin_upload_csv", methods={"POST"})
     */
    public function adminUploadCsvAction(Request $request, string $treePicsDirFull, string $rawTreePicsForCsvDirFull, string $thumbnailExtension)
    {
        /*
        $this   // SQL-DEBUGGER
        ->get('doctrine')
            ->getConnection()
            ->getConfiguration()
            ->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        */

        // enhance max-runtime of script to 3 mins
        set_time_limit(180);

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();
        $treeRepository = $doctrine->getRepository(Tree::class);
        $treeInformationRepository = $doctrine->getRepository(TreeInformation::class);
        $files = $request->files;

        $csvFile = $files->get("tree_list");
        $newTreesData = $this->csv_to_array($csvFile, "~");

        $errorList = array();

        // create Tree for every Csv-row
        foreach($newTreesData as $rowKey => $treeRow) {
            $rowCount = $rowKey + 2;
            $tree = new Tree();

            $treeNameKeyword = "Baumname";
            $treeName = trim( utf8_encode( $treeRow[$treeNameKeyword] ) );
            unset($treeRow[$treeNameKeyword]);

            // error if treeName is empty
            if(empty($treeName)) {
                array_push(
                    $errorList,
                    "Row $rowCount: required field $treeNameKeyword is empty! Row skipped!"
                );

                continue; // skip whole row
            }

            // error if treeName is not unique
            $doppelgaenger = $treeRepository->findOneBy(["name" => $treeName]);
            if($doppelgaenger !== null){
                array_push(
                    $errorList,
                    "Row $rowCount: Tree with $treeNameKeyword: '$treeName' allready exists in Database - Row skipped!"
                );

                continue; // skip whole row
            }

            $tree->setName($treeName);

            // save all other attributes as TreeInformation entities
            foreach($treeRow as $informationNameRaw => $informationValuesStringRaw) {
                $informationName = trim( utf8_encode($informationNameRaw) );
                $informationValuesString = trim( utf8_encode($informationValuesStringRaw) );

                if($informationName && ($informationValuesString || $informationValuesString === 0 || $informationValuesString === "0")) {

                    $informationValues = explode(";", $informationValuesString);

                    foreach ($informationValues as $informationValue) {
                        $informationValue = trim($informationValue);

                        if ($informationValue || $informationValue === "0" || $informationValue === 0) {
                            $newInformation = $treeInformationRepository->findOneBy(array(
                                    'name' => $informationName,
                                    'value' => $informationValue)
                            );

                            if ($newInformation === null) {
                                $newInformation = new TreeInformation();
                                $newInformation->setName($informationName);
                                $newInformation->setValue($informationValue);
                                $entityManager->persist($newInformation);

                            }
                            $tree->addInformation($newInformation);
                        }
                    }
                }
            }

            // copy Tree-Pictures to their public destination
            $fileSystem = new Filesystem();
            $finder = new Finder();

            $rawTreePicsDirForThisTree = $rawTreePicsForCsvDirFull . "/" . $treeName;
            $rawTreePicsDirectoryWasFound = $fileSystem->exists($rawTreePicsDirForThisTree);

            if($rawTreePicsDirectoryWasFound) {
                $finder->files()->in($rawTreePicsDirForThisTree);
            }
            else{
                array_push(
                    $errorList,
                    "Row $rowCount: Folder with name $treeName could not be found in directory $rawTreePicsForCsvDirFull -> "
                    . "No Pictures are added for this tree."
                );
            }

            if($rawTreePicsDirectoryWasFound && $finder->hasResults()) {

                foreach ($finder as $rawPic) {
                    $rawPicPath = $rawPic->getRealPath();

                    $newPicExtension = strtolower($rawPic->getExtension());
                    $newPicFilename = uniqid();
                    $newPicFilenamePlusExtension = $newPicFilename . "." . $newPicExtension;
                    $newPicPath = $treePicsDirFull . "/" . $newPicFilenamePlusExtension;
                    $newPicIsImageType = in_array($newPicExtension, self::ALLOWED_PICTURE_EXTENSIONS);

                    if($newPicIsImageType) {
                        try {
                            $fileSystem->copy($rawPic->getRealPath(), $newPicPath, true);
                            $this->createThumbnail($treePicsDirFull, $newPicFilename, $thumbnailExtension, $newPicExtension);

                            $newPicEntity = new TreePicture();
                            $newPicEntity->setPath($newPicFilenamePlusExtension);
                            $entityManager->persist($newPicEntity);
                            $tree->addPicture($newPicEntity);
                        } catch (IOExceptionInterface $exception) {
                            array_push(
                                $errorList,
                                "Row $rowCount: Unknown Error while copying picture-file $rawPicPath to $newPicPath - "
                                . "Picture skipped! --- " . $exception->getPath()
                            );
                        }
                    }
                    else{
                        array_push(
                            $errorList,
                            "Row $rowCount: File '$rawPicPath' does not have one of the allowed picture-extensions (" . implode(",", self::ALLOWED_PICTURE_EXTENSIONS) . ") - "
                            . "Picture skipped!"
                        );
                    }
                }
            }

            $entityManager->persist($tree);
            $entityManager->flush();
        }

        $entityManager->flush();

        return $this->render('admin/admin_upload_csv_result.html.twig', [
            "errors" => $errorList
        ]);
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
                if ( (empty($key) && $key !== "0") || (empty($param) && $param !== "0") ) {
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
     * @param $thumbnailExtension
     * @param $entityManager
     * @param $treePicturesRepository
     */
    private function deletePicturesByPostParamsForGivenTree($tree, $postParams, $treePicsDirFull, $thumbnailExtension, $entityManager, $treePicturesRepository) {

        foreach ($postParams as $key => $param) {
            $isDeletePictureParam = strpos($key, "delete-pic-") !== false;

            if($isDeletePictureParam) {
                $id = $param;
                $pic = $treePicturesRepository->find($id);

                if( !empty($pic) ) {
                    $tree->removePicture($pic);

                    try {
                        unlink($treePicsDirFull . "/" . $pic->getPath());
                    } catch (ErrorException $e) {}
                    try {
                        unlink($treePicsDirFull . "/" . $thumbnailExtension . $pic->getPath());
                    } catch (ErrorException $e) {}

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
     * persists pictures from a http-file-input for given tree
     * @param $tree
     * @param $pictures File[] uploaded picture-files via http-request
     * @param $treePicsDirFull
     * @param $thumbnailExtension
     * @param $entityManager
     */
    private function persistTreePictureUploadsForGivenTree($tree, $pictures, $treePicsDirFull, $thumbnailExtension, $entityManager) {

        foreach ($pictures as $pic) {
            $filename = uniqid();
            $extension = strtolower($pic->guessExtension());
            $path = $filename . "." . $extension;
            $isImageType = in_array($extension, self::ALLOWED_PICTURE_EXTENSIONS);

            if ($isImageType) {
                $pic->move($treePicsDirFull, $path);
                $this->createThumbnail($treePicsDirFull, $filename, $thumbnailExtension, $extension);

                $newTreePicture = new TreePicture();
                $newTreePicture->setPath($path);
                $tree->addPicture($newTreePicture);
                $entityManager->persist($newTreePicture);
            }
        }
    }

    /**
     * @param $treePicsDirFull
     * @param $filename
     * @param $thumbnailExtension
     * @param $typeExtension
     */
    private function createThumbnail($treePicsDirFull, $filename, $thumbnailExtension, $typeExtension) {
        $this->imagingHelper->set_img($treePicsDirFull . "/" . $filename . "." . $typeExtension);
        $this->imagingHelper->set_quality(80);
        $this->imagingHelper->set_size(200);
        $this->imagingHelper->save_img($treePicsDirFull . "/" . $thumbnailExtension . $filename . "." . $typeExtension);
        $this->imagingHelper->clear_cache();
    }

    /**
     * Thanks to jaywilliams: http://gist.github.com/385876
     */
    private function csv_to_array($filename='', $delimiter=',')
    {
        if(!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }



}
