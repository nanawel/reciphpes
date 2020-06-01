<?php

namespace App\EventListener;


use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;

class AddOrUpdateRecipeTags
{
//    public function __invoke(OnFlushEventArgs $event) {
//        $entityManager = $event->getEntityManager();
//        $uow = $entityManager->getUnitOfWork();
//
//        foreach ($uow->getScheduledDocumentInsertions() as $keyEntity => $entity) {
//            if ($entity instanceof Recipe) {
//                $this->upsertTags($entityManager, $entity);
//            }
//        }
//        foreach ($uow->getScheduledDocumentUpdates() as $keyEntity => $entity) {
//            if ($entity instanceof Recipe) {
//                $this->upsertTags($entityManager, $entity);
//            }
//        }
//    }
//
//    protected function upsertTags(EntityManagerInterface $entityManager, Recipe $recipe) {
//        /** @var DocumentRepository $tagRepository */
//        $tagRepository = $entityManager->getRepository(Tag::class);
//
//        foreach ($recipe->getTags() as $tagName) {
//            if (! $tagRepository->find($tagName)) {
//                $tag = (new Tag());
//                $tag->id = $tagName;
//
//                $entityManager->persist($tag);
//            }
//        }
//    }
}
