<?php

namespace App\Controller;

use App\Entity\UploadedFile;
use App\Repository\UploadedFileRepository;
use App\Services\UploadedFileFormManager;
use App\Services\UploadedFileManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Files controller.
 * @Route("/", name="api_")
 */
class FilesController
{
    use ControllerTrait;

    protected UploadedFileFormManager $fileFormManager;
    protected UploadedFileManager $fileManager;
    protected UploadedFileRepository $fileRepository;
    protected ParameterBagInterface $parameterBag;
    protected RouterInterface $router;

    public function __construct(
        UploadedFileFormManager $fileFormManager,
        UploadedFileManager     $fileManager,
        UploadedFileRepository  $fileRepository,
        ParameterBagInterface   $parameterBag,
        RouterInterface         $router
    )
    {
        $this->fileFormManager = $fileFormManager;
        $this->fileManager = $fileManager;
        $this->fileRepository = $fileRepository;
        $this->parameterBag = $parameterBag;
        $this->router = $router;
    }

    /**
     * @Rest\Post("/file")
     * @param Request $request
     * @return Response
     */
    public function uploadAction(Request $request): Response
    {
        $fileData = new UploadedFile();
        $fileData->file = $request->files->get('file');

        $validationErrors = $this->fileFormManager->validateForm($request, $fileData);


        if (!$validationErrors) {
            $this->fileManager->save($fileData);

            $newLocation = $this->router->generate('api_app_files_getfile', ['fileId' => $fileData->fileName], UrlGeneratorInterface::RELATIVE_PATH);
            $response = $this->handleView($this->view(['filename' => $fileData->fileName], Response::HTTP_CREATED, ['Location' => $newLocation]));
        } else {
            $response = $this->handleView($this->view($validationErrors, Response::HTTP_BAD_REQUEST));
        }

        return $response;
    }

    /**
     * @Rest\Get("/file/{fileId}")
     * @param $fileId
     * @param Request $request
     * @return Response
     */
    public function getFileAction($fileId, Request $request): Response
    {
        /** @var UploadedFile $fileData */
        $fileData = $this->fileRepository->findOne($fileId);

        $response = $this->handleView($this->view('The requested file was not found.', Response::HTTP_NOT_FOUND));

        if ($fileData) {
            $filePath = implode(DIRECTORY_SEPARATOR, [
                $this->parameterBag->get('kernel.project_dir'),
                'public', 'files',
                $fileData->fileName
            ]);

            $response = new BinaryFileResponse($filePath);
        }

        return $response;
    }
}
