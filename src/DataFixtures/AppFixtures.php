<?php

namespace App\DataFixtures;

use App\Entity\TimeOfYear;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const DEFAULT_TIMES_OF_YEAR = [
        'spring' => 'Printemps',
        'summer' => 'Été',
        'fall' => 'Automne',
        'winter' => 'Hiver',
    ];

    public function load(ObjectManager $manager) {
        foreach (self::DEFAULT_TIMES_OF_YEAR as $code => $name) {
            $manager->persist(
                (new TimeOfYear())
                    ->setCode($code)
                    ->setName($name)
            );
        }

        $manager->flush();
    }
}
