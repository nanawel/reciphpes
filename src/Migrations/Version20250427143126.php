<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Version20250427143126 extends AbstractMigration
    implements ContainerAwareInterface
{
    protected ContainerInterface $container;

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var IngredientRepository $repository */
        $repository = $em->getRepository(Ingredient::class);

        /** @var Ingredient $ingredient */
        foreach ($repository->findAll() as $ingredient) {
            $ingredient->setSortName($ingredient->getName());
            $em->persist($ingredient);
        }

        $em->flush();
    }

    public function down(Schema $schema): void
    {
    }
}
