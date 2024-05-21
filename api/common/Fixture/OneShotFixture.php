<?php


namespace Common\Fixture;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

abstract class OneShotFixture extends Fixture implements OneShotFixtureInterface
{
    use OneShotFixtureTrait;

    abstract protected function doLoad(ObjectManager $manager);
    abstract protected function getFixtureLogClassname(): string;

    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $fixtureName = static::class;

        if ($this->isFixtureAlreadyRun($manager, $this->getFixtureLogClassname())) {
            $this->logger->warning("The fixture current '$fixtureName' has already been registered, it won't be executed then.");
            return;
        }

        $this->doLoad($manager);

        $this->logger->info('Fixture done.');

        $this->registerFixture($manager, $this->getFixtureLogClassname(), $this->logger);
    }
}
