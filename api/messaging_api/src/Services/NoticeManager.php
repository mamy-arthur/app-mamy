<?php

namespace App\Services;

use App\Entity\Notice;
use Common\Entity\AbstractEntityHandler;
use Common\DTO\ListFetchingParamsDto;
use Doctrine\Common\Collections\Collection;

class NoticeManager extends AbstractEntityHandler
{
    /**
     * @return string
     */
    public function getEntityClassname(): string
    {
        return Notice::class;
    }

    public function getListing(ListFetchingParamsDto $listParameters): array
    {
        return $this->getRepository()->getNoticesListing($listParameters);
    }
}
