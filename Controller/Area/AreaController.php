<?php

namespace App\Controller\Area;

use App\Entity\Area;
use App\Repository\AreaRepository;
use PHPZlc\PHPZlc\Responses\Responses;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AreaController extends AbstractController
{
    /**
     * 得到有层级的省市区数据
     */
    public function children(AreaRepository $areaRepository)
    {
        $data['area'] = $areaRepository->getArrayChildren($areaRepository->findAll());

        return Responses::success('成功得到数据', $data);
    }
}