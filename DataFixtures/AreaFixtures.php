<?php

namespace App\DataFixtures;

use App\Entity\Area;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AreaFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $manager->getRepository(Area::class)->builtInData();
    }
}
