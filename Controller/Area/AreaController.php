<?php

namespace App\Controller\Area;

use PHPZlc\PHPZlc\Responses\Responses;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AreaController extends AbstractController
{
    /**
     * 得到有层级的省市区数据
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|void
     */
    public function children()
    {
        $areaRepository = $this->getDoctrine()->getRepository('App:Area');
        $data['area'] = $areaRepository->getArrayChildren($areaRepository->findAll());

        return Responses::success('成功得到数据', $data);
    }
}