<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Services\ServiceManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Service controller.
 * @Route("/", name="api_")
 */
class ServiceController
{
    use ControllerTrait;

    protected ServiceManager $serviceManager;

    protected FormFactoryInterface $formFactory;

    public function __construct(ServiceManager $serviceManager, FormFactoryInterface $formFactory)
    {
        $this->serviceManager = $serviceManager;
        $this->formFactory = $formFactory;
    }

    /**
     * @Rest\Get("/services")
     *
     * @return Response
     */
    public function getListAction(): Response
    {
        $services = $this->serviceManager->getServices();

        return $this->handleView($this->view($services, Response::HTTP_OK));
    }

    /**
     * Create service.
     * @Rest\Post("/service")
     *
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request): Response
    {
        $service = new Service();
        $form = $this->formFactory->create(ServiceType::class, $service);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->serviceManager->createService($service);

            $response = $this->handleView($this->view(null, Response::HTTP_CREATED));
        } else {
            $response = $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));
        }

        return $response;
    }
}
