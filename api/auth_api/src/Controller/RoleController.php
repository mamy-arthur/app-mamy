<?php

namespace App\Controller;

use App\Entity\Role;
use App\Services\RoleFormManager;
use App\Services\RoleManager;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * role controller.
 * @Route("/", name="api_")
 */
class RoleController
{
    use ControllerTrait;

    protected RoleManager $roleManager;
    protected RoleFormManager $roleFormManager;

    public function __construct(RoleManager $roleManager, RoleFormManager $roleFormManager)
    {
        $this->roleManager = $roleManager;
        $this->roleFormManager = $roleFormManager;
    }

    /**
     * Lists all roles.
     * @Rest\Get("/roles")
     *
     * @return Response
     */
    public function getListAction(Request $request): Response
    {
        $username = $request->query->get('username');

        $roles = $this->roleManager->getRoles($username);

        return $this->handleView($this->view($roles, Response::HTTP_OK));
    }

    /**
     * Create role.
     * @Rest\Post("/role")
     *
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request): Response
    {
        $role = new Role();
        $errorsValidation = $this->roleFormManager->validateRole($request, $role);

        if (!$errorsValidation) {
            $this->roleManager->save($role);
            return $this->handleView($this->view(null, Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($errorsValidation, Response::HTTP_BAD_REQUEST));
    }

    /**
     * Get role.
     * @Rest\Get("/role/{role_id}")
     * @ParamConverter("role", options={"mapping"={"role_id"="id"}})
     *
     * @param Role $role
     * @return Response
     */
    public function getAction(Role $role): Response
    {
        return $this->handleView($this->view($role, Response::HTTP_OK));
    }

    /**
     *  Update role.
     * @Rest\Put("/role/{role_id}")
     * @ParamConverter("role", options={"mapping"={"role_id"="id"}})
     * @return Response
     */
    public function updateAction(Request $request, role $role): Response
    {   
        $errorsValidation = $this->roleFormManager->validaterole($request, $role);

        if (!$errorsValidation) {
            $this->roleManager->save($role);
            return $this->handleView($this->view(Response::HTTP_OK));
        }
        return $this->handleView($this->view($errorsValidation));


    }

    /**
     * Search role.
     * @Rest\Delete("/role/{role_id}")
     * @ParamConverter("role", options={"mapping"={"role_id"="id"}})
     *
     * @return Response
     */
    public function deleteAction(Role $role): Response
    {
        $this->roleManager->delete($role);
        $this->view()->setLocation("/roles");
        return $this->handleView($this->view(null, Response::HTTP_ACCEPTED));
    }

    /**
     * Lists all roles codes.
     * @Rest\Get("/roles_codes")
     *
     * @return Response
     */

    public function getRolesCodes(): Response
    {
        $roles = $this->roleManager->getRoles();

        $rolesCodes = array_map(function ($role) {
            return $role->code;

        }, $roles);

        return $this->handleView($this->view($rolesCodes, Response::HTTP_OK));
    }

}
