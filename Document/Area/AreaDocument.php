<?php

namespace App\Document\Area;

use phpDocumentor\Reflection\Type;
use PHPZlc\Document\Document;

class AreaDocument extends Document
{
    public function add()
    {
        $this->setGroup('地址');
        return parent::add();
    }

   public function setUrl($url)
   {
       return parent::setUrl('/area'.$url);
   }

   public function childrenAction()
   {
       $this->add()
           ->setTitle('得到所有层级的省市区数据')
           ->setUrl('/children')
           ->setReturn(
               <<<EOF
<pre>
"{
    "code": 0,
    "msg": "成功得到数据",
    "msgInfo": [],
    "data": {
        "area": [
            {
                "child": [
                    {
                        "id": "340000",
                        "name": "安徽省",
                        "parent_id": "0",
                        "child": [
                            {
                                "id": "340800",
                                "name": "安庆市",
                                "parent_id": "340000",
                                "child": [
                                    {
                                        "id": "340823",
                                        "name": "枞阳县"
                                        "parent_id": "340800"
                                    },
 }"
</pre>
EOF

           )
           ->generate();
   }
}