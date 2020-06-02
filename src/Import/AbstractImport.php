<?php

namespace App\Import;

use Doctrine\ORM\EntityManager;

/**
 * Class AbstractImport
 *
 * @see https://www.jesuisundev.com/massive-import-via-symfony2-command-depuis-fichier-csv/
 *
 * @package App\Import
 */
class AbstractImport
{
    protected $entityManager;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function import() {
        // Getting php array of data from CSV
        $data = $this->get($input, $output);

        // Turning off doctrine default logs queries for saving memory
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        // Define the size of record, the frequency for persisting the data and the current index of records
        $size = count($data);
        $batchSize = 20;
        $i = 1;

        // Processing on each row of data
        foreach ($data as $row) {

            $user = $this->entityManager->getRepository('AcmeAcmeBundle:User')
                ->findOneByEmail($row['email']);

            // If the user doest not exist we create one
            if (! is_object($user)) {
                $user = new User();
                $user->setEmail($row['email']);
            }

            // Updating info
            $user->setLastName($row['lastname']);
            $user->setFirstName($row['firstname']);

            // Do stuff here !

            // Persisting the current user
            $this->entityManager->persist($user);

            // Each 20 users persisted we flush everything
            if (($i % $batchSize) === 0) {

                $this->entityManager->flush();
                // Detaches all objects from Doctrine for memory save
                $this->entityManager->clear();

                // Advancing for progress display on console
                $progress->advance($batchSize);

                $now = new \DateTime();
                $output->writeln(' of users imported ... | ' . $now->format('d-m-Y G:i:s'));

            }

            $i++;

        }

        // Flushing and clear data on queue
        $this->entityManager->flush();
        $this->entityManager->clear();

        // Ending the progress bar process
        $progress->finish();
    }
}
