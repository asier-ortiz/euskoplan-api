<?php

namespace App\Console\Commands;

use Database\Helpers\DataManager;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CollectionsCron extends Command
{
    protected $signature = 'collections:cron';

    protected $description = 'Updates database collections';

    private DataManager $dataManager;

    private DatabaseSeeder $databaseSeeder;

    public function __construct()
    {
        parent::__construct();
        $this->dataManager = new DataManager();
        $this->databaseSeeder = new DatabaseSeeder();
    }

    public function handle(): int
    {
        info("Starting database collections update Cron Job at " . now());

        $this->dataManager->fetchResource('accommodations', 'es');
        $this->dataManager->fetchResource('accommodations', 'eu');
        $this->dataManager->fetchResource('caves', 'es');
        $this->dataManager->fetchResource('caves', 'eu');
        $this->dataManager->fetchResource('culturals', 'es');
        $this->dataManager->fetchResource('culturals', 'eu');
        $this->dataManager->fetchResource('events', 'es');
        $this->dataManager->fetchResource('events', 'eu');
        $this->dataManager->fetchResource('fairs', 'es');
        $this->dataManager->fetchResource('fairs', 'eu');
        $this->dataManager->fetchResource('museums', 'es');
        $this->dataManager->fetchResource('museums', 'eu');
        $this->dataManager->fetchResource('naturals', 'es');
        $this->dataManager->fetchResource('naturals', 'eu');
        $this->dataManager->fetchResource('restaurants', 'es');
        $this->dataManager->fetchResource('restaurants', 'eu');
        $this->dataManager->fetchResource('localities', 'es');
        $this->dataManager->fetchResource('localities', 'eu');
        $this->databaseSeeder->run();

        info("Finishing database collections update Cron Job at " . now());

        return CommandAlias::SUCCESS;
    }
}
