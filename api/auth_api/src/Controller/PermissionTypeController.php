<?php

namespace App\Controller;

use App\Entity\PermissionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Role;
use App\Services\PermissionTypeManager;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * permissiontype controller.
 * @Route("/", name="api_")
 */
class PermissionTypeController
{
    use ControllerTrait;

    private $permissionTypeManager;

    public function __construct(PermissionTypeManager $permissionTypeManager)
    {
        $this->permissionTypeManager = $permissionTypeManager;
      
       
    }

     /**
     * Lists all permissions types.
     * @Rest\Get("/permissions-types")
     *
     * @return Response
     */
    public function getListAction(Request $request): Response
    {

        $permissionsTypes = $this->permissionTypeManager->getEntities();

        return $this->handleView($this->view($permissionsTypes, Response::HTTP_OK));
    }

    /**
     * Get permissionType.
     * @Rest\Get("/permission_type/{id}")
     * @ParamConverter("permissionType", options={"mapping"={"id"="id"}})
     *
     * @param PermissionType permissionType
     * @return Response
     */
    public function getAction(PermissionType $permissionType): Response
    {
        return $this->handleView($this->view($permissionType, Response::HTTP_OK));
    }



}
