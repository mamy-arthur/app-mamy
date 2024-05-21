<?php

namespace App\Controller;

use App\Entity\Notice;
use App\Repository\NoticeRepository;
use App\Services\NoticeFormManager;
use App\Services\NoticeManager;
use Common\DTO\ListFetchingParamsDto;
use Common\Enum\NoticeTypeEnum;
use Common\Http\RequestHelper;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Notice controller.
 * @Route("/", name="api_")
 */
class NoticeController
{
    use ControllerTrait;

    protected NoticeManager $noticeManager;
    protected NoticeFormManager $noticeFormManager;
    protected RouterInterface $router;
    protected NoticeRepository $noticeRepository;

    public function __construct(
        NoticeManager $noticeManager,
        NoticeFormManager $noticeFormManager,
        RouterInterface $router,
        NoticeRepository $noticeRepository
    ) {
        $this->noticeManager = $noticeManager;
        $this->noticeFormManager = $noticeFormManager;
        $this->router = $router;
        $this->noticeRepository = $noticeRepository;
    }

    /**
     * Lists all notices.
     * @Rest\Get("/notices")
     *
     * @param Request $request
     * @return Response
     */
    public function getListAction(Request $request): Response
    {
        $listParameters = RequestHelper::getListingFetchingParameters($request);
        $notices = $this->noticeManager->getListing($listParameters);
        return $this->handleView($this->view($notices, Response::HTTP_OK));
    }

    /**
     * Create a notice.
     * @Rest\Post("/notice")
     *
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request): Response
    {
        $notice = new Notice();
        $errorsValidation = $this->noticeFormManager->validateForm(
            $request,
            $notice,
        );

        if (!$errorsValidation) {
            $this->noticeManager->save($notice);
            $newLocation = $this->router->generate(
                'api_app_notice_get',
                ['notice_id' => $notice->id],
                UrlGeneratorInterface::RELATIVE_PATH,
            );
            $response = $this->handleView(
                $this->view(null, Response::HTTP_CREATED, [
                    'Location' => $newLocation,
                ]),
            );
        } else {
            $response = $this->handleView(
                $this->view($errorsValidation, Response::HTTP_BAD_REQUEST),
            );
        }

        return $response;
    }

    /**
     * Lists count notices.
     * @Rest\Get("/notices/count")
     *
     * @return Response
     */
    public function getListCountAction(): Response
    {
        return $this->handleView(
            $this->view(
                $this->noticeManager->getEntitiesCount(),
                Response::HTTP_OK,
            ),
        );
    }

    /**
     * Get a notice.
     * @Rest\Get("/notice/{notice_id}")
     * @ParamConverter("notice", options={"mapping"={"notice_id"="id"}})
     *
     * @param Notice $notice
     * @return Response
     */
    public function getAction(Notice $notice): Response
    {
        return $this->handleView($this->view($notice, Response::HTTP_OK));
    }

    /**
     *  Update a notice.
     * @Rest\Put("/notice/{id}")
     * @ParamConverter("notice", options={"mapping"={"id"="id"}})
     * @param Request $request
     * @param Notice $notice
     * @return Response
     */
    public function updateAction(Request $request, Notice $notice): Response
    {
        $errorsValidation = $this->noticeFormManager->validateForm(
            $request,
            $notice,
        );

        if (!$errorsValidation) {
            $this->noticeManager->save($notice);
            $response = $this->handleView($this->view(null, Response::HTTP_OK));
        } else {
            $response = $this->handleView(
                $this->view($errorsValidation, Response::HTTP_BAD_REQUEST),
            );
        }

        return $response;
    }

    /**
     * Delete a notice.
     * @Rest\Delete("/notice/{id}")
     * @ParamConverter("notice", options={"mapping"={"id"="id"}})
     *
     * @param Notice $notice
     * @return Response
     */
    public function deleteAction(Notice $notice): Response
    {
        $this->noticeManager->delete($notice);
        return $this->handleView($this->view(null, Response::HTTP_NO_CONTENT));
    }

    /**
     * Get notices types.
     * @Rest\Get("/notices-types")
     *
     * @return Response
     */
    public function getNoticesTypesAction(): Response
    {
        return $this->handleView(
            $this->view(
                array_values(
                    (new \ReflectionClass(
                        NoticeTypeEnum::class,
                    ))->getConstants(),
                ),
                Response::HTTP_OK,
            ),
        );
    }

    /**
     * Lists all notices to display.
     * @Rest\Get("/notices-to-show")
     *
     * @param Request $request
     * @return Response
     */
    public function getNoticesDisplayedAction(Request $request): Response
    {
        $noticeType = $request->query->get('notice_type');

        return $this->handleView(
            $this->view(
                $this->noticeRepository->findNoticesByType($noticeType),
                Response::HTTP_OK,
            ),
        );
    }
}
