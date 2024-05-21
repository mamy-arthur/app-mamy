<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\UserFormManager;
use App\Services\UserManager;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Common\Http\RequestHelper;
use Common\DTO\ListFetchingParamsDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * User controller.
 * @Route("/", name="api_")
 */
class UserController extends AbstractController
{
    use ControllerTrait;

    protected UserManager $userManager;

    protected UserFormManager $userFormManager;

    protected UserRepository $userRepository;

    public function __construct(
        UserManager $userManager,
        UserFormManager $userFormManager,
        UserRepository $userRepository
    ) {
        $this->userManager = $userManager;
        $this->userFormManager = $userFormManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Lists all users.
     * @Rest\Get("/users")
     *
     * @return Response
     */
    public function getListAction(): Response
    {
        $users = $this->userManager->getUsers();
        return $this->handleView($this->view($users, Response::HTTP_OK));
    }

    /**
     * @Rest\Get("/listing/users")
     *
     * @param Request $request
     * @return Response
     */
    public function getListAction2(Request $request): Response
    {
        $listParameters = RequestHelper::getListFetchingParameters(
            $request,
            new ListFetchingParamsDto([
                'page' => 1,
                'items' => 10,
                'format' => '',
                'filters' => [],
                'orderBy' => ['code' => 'ASC'],
            ]),
        );

        $offset =
            $listParameters->page && $listParameters->items
                ? ($listParameters->page - 1) * $listParameters->items
                : null;

        $items = $this->userRepository->getListing(
            $listParameters->filters,
            $listParameters->orderBy,
            $listParameters->items,
            $offset,
        );

        return $this->handleView($this->view($items, Response::HTTP_OK));
    }

    /**
     * @Rest\Get("/listing/users/count")
     *
     * @return Response
     */
    public function getListCountAction2(Request $request): Response
    {
        $listParameters = RequestHelper::getListFetchingParameters(
            $request,
            new ListFetchingParamsDto([
                'page' => 1,
                'items' => 10,
                'format' => '',
                'filters' => [],
                'orderBy' => ['code' => 'ASC'],
            ]),
        );

        $count = $this->userRepository->getListingCount(
            $listParameters->filters,
        );

        return $this->handleView($this->view($count, Response::HTTP_OK));
    }

    /**
     * Create User.
     * @Rest\Post("/user")
     *
     * @return Response
     */
    public function postAction(Request $request): Response
    {
        $user = new User();
        $validationErrors = $this->userFormManager->validateUser(
            $request,
            $user,
        );

        if ($validationErrors) {
            $response = $this->handleView(
                $this->view($validationErrors, Response::HTTP_BAD_REQUEST),
            );
        } else {
            $this->userManager->saveUser(
                $user,
                $request->request->get('roles'),
            );

            // todo: Add Location header
            $response = $this->handleView(
                $this->view(null, Response::HTTP_CREATED),
            );
        }

        /** @var Response */
        return $response;
    }

    /**
     * Get user by id.
     * @Rest\Get("/user/{user_id}", requirements={"user_id"="\d+"})
     * @ParamConverter("user", options={"mapping"={"user_id"="id"}})
     *
     * @param User $user
     * @return Response
     */
    public function getAction(User $user): Response
    {
        $user = $this->userManager->getFullUser($user);

        return $this->handleView($this->view($user, Response::HTTP_OK));
    }

    /**
     * Get user by username.
     * @Rest\Get("/user/by-username")
     * @param Request $request
     * @return Response
     */
    public function getByUsernameAction(Request $request): Response
    {
        // todo: do not return sensitive data (email, phone number, address...)
        // todo: do not return sensitive data (email, phone number, address...)
        $username = $request->query->get('username');

        if (!$username) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                'The "username" query parameter is required!',
            );
        }

        $user = $this->userManager->getUserByUsername($username);

        if (!$user) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND,
                'User not found for the provided username!',
            );
        }

        return $this->handleView($this->view($user, Response::HTTP_OK));
    }

    /**
     *  Update User.
     * @Rest\Put("/user/{user_id}")
     * @ParamConverter("user", options={"mapping"={"user_id"="id"}})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function updateAction(Request $request, User $user): Response
    {
        $validationErrors = $this->userFormManager->validateUser(
            $request,
            $user,
        );

        if (!$validationErrors) {
            $this->userManager->saveUser(
                $user,
                $request->request->get('roles'),
            );
            $response = $this->handleView($this->view(null, Response::HTTP_OK));
        } else {
            $response = $this->handleView(
                $this->view($validationErrors, Response::HTTP_BAD_REQUEST),
            );
        }

        return $response;
    }

    /**
     * Delete user.
     * @Rest\Delete("/user/{user_id}")
     * @ParamConverter("user", options={"mapping"={"user_id"="id"}})
     *
     * @param User $user
     * @return Response
     */
    public function deleteAction(User $user): Response
    {
        $this->userManager->delete($user);
        $this->view()->setLocation('/users');
        return $this->handleView($this->view(null, Response::HTTP_ACCEPTED));
    }

    /**
     * Get user email.
     * @Rest\Post("/user/reset-password")
     * @param Request $request
     * @return Response
     */
    public function resetPasswordAction(Request $request): Response
    {
        $username = $request->query->get('username');
        if (!$username) {
            return $this->handleView(
                $this->view(
                    ['The "username" query parameter is required!'],
                    Response::HTTP_BAD_REQUEST,
                ),
            );
        }

        $message = $request->request->get('message');
        if (!$message) {
            return $this->handleView(
                $this->view(
                    ['The "message" field is required!'],
                    Response::HTTP_BAD_REQUEST,
                ),
            );
        }

        $user = $this->userManager->getUserByUsername($username);

        if ($user) {
            $passwordToken = RequestHelper::getUrlResponse(
                '/auth-api/pass-reset',
                [
                    'http' => [
                        'header' => "Content-Type: application/json\r\n",
                        'method' => 'POST',
                        'content' => json_encode([
                            'username' => $username,
                        ]),
                    ],
                ],
            );

            $message = str_replace('{{token}}', $passwordToken, $message);

            RequestHelper::getUrlResponse('/messaging-api/send-mail', [
                'http' => [
                    'header' => "Content-Type: application/json\r\n",
                    'method' => 'POST',
                    'content' => json_encode([
                        'to_user' => $username,
                        'subject' => 'Mot de passe oublié',
                        'content' => $message,
                    ]),
                ],
            ]);
        }

        return $this->handleView($this->view(null, Response::HTTP_CREATED));
    }
}
