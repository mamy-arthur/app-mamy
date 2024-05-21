<?php

namespace App\Controller;

use App\Entity\Credentials;
use App\Services\CredentialsFormManager;
use App\Services\CredentialsManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use App\Services\RoleManager;

/**
 * role controller.
 * @Route("/", name="api_")
 */
class AuthController
{
    use ControllerTrait;

    protected CredentialsManager $credentialsManager;

    protected CredentialsFormManager $credentialsFormManager;

    protected UserPasswordEncoderInterface $passwordEncoder;

    protected Security $security;

    protected RoleManager $roleManager;

    public function __construct(
        CredentialsManager $credentialsManager,
        CredentialsFormManager $credentialsFormManager,
        UserPasswordEncoderInterface $passwordEncoder,
        Security $security,
        RoleManager $roleManager
    ) {
        $this->credentialsManager = $credentialsManager;
        $this->credentialsFormManager = $credentialsFormManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->security = $security;
        $this->roleManager = $roleManager;
    }

    /**
     * @param Request $request
     * @Rest\Post("/credentials")
     * @return Response
     */
    public function saveCredentialsAction(Request $request): Response
    {
        $username = $request->request->get('username');

        if (!$username) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                'The "username" field is required!',
            );
        }

        $existing = $this->credentialsManager->getEntity([
            'username' => $username,
        ]);

        $credentials = $existing ?? new Credentials();

        $errors = $this->credentialsFormManager->validateFormData(
            $request,
            $credentials,
        );

        if (!$errors) {
            $this->credentialsManager->save($credentials);

            $responseBody = $credentials->passwordReset
                ? ['password_reset' => $credentials->passwordReset]
                : null;

            $response = $this->handleView(
                $this->view(
                    $responseBody,
                    $existing ? Response::HTTP_OK : Response::HTTP_CREATED,
                ),
            );
        } else {
            $response = $this->handleView(
                $this->view($errors, Response::HTTP_BAD_REQUEST),
            );
        }

        return $response;
    }

    /**
     * @Rest\Get("/pass-reset/{token}")
     * @ParamConverter("credentials", options={"mapping"={"token"="passwordReset"}})
     * @param Credentials $credentials
     * @return Response
     */
    public function checkPasswordResetTokenAction(
        Credentials $credentials
    ): Response {
        return $this->handleView($this->view(null, Response::HTTP_OK));
    }

    /**
     * @Rest\Post("/pass-reset/{token}")
     * @ParamConverter("credentials", options={"mapping"={"token"="passwordReset"}})
     * @param Credentials $credentials
     * @param Request $request
     * @return Response
     */
    public function resetPasswordAction(
        Credentials $credentials,
        Request $request
    ): Response {
        $password = $request->request->get('password');
        $passwordCheck = $request->request->get('password_check');

        if (!$password || !$passwordCheck || $password != $passwordCheck) {
            throw new BadRequestException(
                'Empty or invalid values have been provided!',
            );
        }

        $credentials->passwordPlain = $password;
        $credentials->passwordReset = null;

        $this->credentialsManager->save($credentials);

        return $this->handleView($this->view(null, Response::HTTP_OK));
    }

    /**
     * @Rest\Post("/pass-reset")
     * @param Request $request
     * @return Response
     */
    public function makePasswordResetTokenAction(Request $request): Response
    {
        $username = $request->request->get('username');

        if (!$username) {
            throw new BadRequestException('The "username" field is required!');
        }

        /** @var Credentials $credentials */
        $credentials = $this->credentialsManager->getRepository()->findOneBy([
            'username' => $username,
        ]);

        if (!$credentials) {
            throw new NotFoundHttpException(
                'The provided credentials could not be matched!',
            );
        }

        $credentials->passwordReset = $this->credentialsManager->getPasswordResetToken(
            $credentials->username,
        );

        $this->credentialsManager->save($credentials);

        return $this->handleView(
            $this->view($credentials->passwordReset, Response::HTTP_OK),
        );
    }

    /**
     * @Rest\Get("/pass-reset")
     * @param Request $request
     * @return Response
     */
    public function getPasswordResetTokenAction(Request $request): Response
    {
        $username = $request->query->get('username');

        if (!$username) {
            throw new BadRequestException('The "username" must be provided!');
        }

        /** @var Credentials $credentials */
        $credentials = $this->credentialsManager->getRepository()->findOneBy([
            'username' => $username,
        ]);

        if (!$credentials) {
            throw new NotFoundHttpException(
                'The provided credentials could not be matched!',
            );
        }

        return $this->handleView(
            $this->view($credentials->passwordReset, Response::HTTP_OK),
        );
    }

    /**
     * @Rest\Post("/login")
     * @return void
     */
    public function loginAction(): void
    {
        // do nothing
    }

    /**
     * @Rest\Get("/user")
     * @return Response
     */
    public function getUserAction(): Response
    {
        $user = $this->security->getUser();
        return $this->handleView(
            $this->view(
                $user,
                $user ? Response::HTTP_OK : Response::HTTP_UNAUTHORIZED,
            ),
        );
    }

    /**
     * @Rest\Get("/user-permissions")
     * @return Response
     */
    public function getUserPermissions()
    {
        $user = $this->security->getUser();

        $userRoles = $this->roleManager->getRoles($user->email);

        $permissions = [];

        foreach ($userRoles as $role) {
            $permissions = array_merge(
                $permissions,
                $role->permissions->getValues(),
            );
        }

        return $this->handleView($this->view($permissions, Response::HTTP_OK));
    }
}
