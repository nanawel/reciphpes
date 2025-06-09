<?php

namespace App\Command\Import;

use App\Import\AbstractImport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'app:import:entities')]
class Entities extends Command
{
    const OPT_ENTITY_TYPE = 'entity-type';
    const OPT_VALIDATION_ONLY = 'validate-only';
    const ARG_INPUT_FILE = 'csv-file';

    public function __construct(
        protected \Symfony\Component\DependencyInjection\ContainerInterface $container,
        protected \App\Entity\Registry                                      $entityRegistry,
        string                                                              $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure() {
        $this
            ->addOption(
                self::OPT_ENTITY_TYPE,
                't',
                InputOption::VALUE_OPTIONAL,
                'Entity type',
                'recipe'
            )
            ->addOption(
                self::OPT_VALIDATION_ONLY,
                'a',
                InputOption::VALUE_NONE,
                'Validation only'
            )
            ->addArgument(
                self::ARG_INPUT_FILE,
                InputArgument::REQUIRED,
                'The input CSV file'
            );
    }

    /**
     * @param InputInterface $input
     * @param ConsoleOutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityType = $input->getOption(self::OPT_ENTITY_TYPE);
        $csvFile = $input->getArgument(self::ARG_INPUT_FILE);

        if (!$importClass = $this->entityRegistry->getEntityConfig($entityType, 'import_class')) {
            throw new \InvalidArgumentException('Unsupported entity type: ' . $entityType);
        }

        /** @var AbstractImport $importService */
        $importService = $this->container->get($importClass);

        $output->writeln("Validating file $csvFile...");

        $errors = [];
        if ($importService->validate($csvFile, $entityType, $errors, ['output' => $output])) {
            $output->getErrorOutput()->writeln('<error>Validation failed :(</error>');
            foreach ($errors as $row => $entityErrors) {
                foreach ($entityErrors as $error) {
                    $output->getErrorOutput()->writeln(
                        sprintf(
                            '<error>[Entity row #%d] %s</error>',
                            $row,
                            $error
                        )
                    );
                }
            }
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        $output->writeln("<info>Validating passed!</info>");

        if ($input->getOption(self::OPT_VALIDATION_ONLY)) {
            $output->writeln('Validation only, skipping import.');

        } else {
            $output->writeln("Importing file $csvFile...");
            $importService->import($csvFile, $entityType, ['output' => $output]);
            $output->writeln('');
            $output->writeln("<info>Import complete!</info>");
        }

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }
}
