<?php

namespace App\DataFixtures;

use App\DataFixtures\Entity\FixtureLog;
use App\Entity\Service;
use App\Entity\User;
use Common\Fixture\OneShotFixtureInterface;
use Common\Fixture\OneShotFixtureTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

class AdminUserFixture extends Fixture implements OneShotFixtureInterface
{
    use OneShotFixtureTrait;

    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function load(ObjectManager $manager)
    {
         $fixtureName = self::class;

        if ($this->isFixtureAlreadyRun($manager, FixtureLog::class)) {
            $this->logger->warning("The fixture current '$fixtureName' has already been registered, it won't be executed then.");
            return;
        }

        $service = new Service();
        $service->code = '_SI';
        $service->name = "Système d'informations";

        $manager->persist($service);
        $manager->flush();

        $user = new User();
        $user->firstName = 'App';
        $user->lastName = 'Admin';
        $user->email = 'admin@app.ad';
        $user->service = $service;

        $manager->persist($user);
        $manager->flush();

        $this->logger->info('Fixture done.');

        $this->registerFixture($manager, FixtureLog::class, $this->logger);
    }
}
