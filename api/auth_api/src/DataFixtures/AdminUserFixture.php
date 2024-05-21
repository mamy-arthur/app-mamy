<?php

namespace App\DataFixtures;

use App\DataFixtures\Entity\FixtureLog;
use App\Entity\Credentials;
use App\Entity\Role;
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

        $role = new Role();
        $role->name = 'Administrateur système';
        $role->code = '_SYSADM';

        $manager->persist($role);
        $manager->flush();

        $user = new Credentials();
        $user->username = 'admin@app.ad';
        $user->passwordPlain = 'something';
        $user->passwordReset = 'init-token';
        $user->roles = [$role];

        $manager->persist($user);
        $manager->flush();

        $this->logger->info('Fixture done.');

        $this->registerFixture($manager, FixtureLog::class, $this->logger);
    }
}
