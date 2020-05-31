<?php


namespace App\EventListener;


use App\Document\Recipe;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class AddOrUpdateRecipeTags
{
    public function __invoke(OnFlushEventArgs $event) {
        $documentManager = $event->getDocumentManager();
        $uow = $documentManager->getUnitOfWork();

        foreach ($uow->getScheduledDocumentInsertions() as $keyEntity => $entity) {
            if ($entity instanceof Recipe) {
                $this->upsertTags($documentManager, $entity);
            }
        }
        foreach ($uow->getScheduledDocumentUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Recipe) {
                $this->upsertTags($documentManager, $entity);
            }
        }
    }

    protected function upsertTags(DocumentManager $documentManager, Recipe $recipe) {
        /** @var DocumentRepository $tagRepository */
        $tagRepository = $documentManager->getRepository(Recipe\Tag::class);

        foreach ($recipe->getTags() as $tagName) {
            if (! $tagRepository->find($tagName)) {
                $tag = (new Recipe\Tag());
                $tag->id = $tagName;

                $documentManager->persist($tag);
            }
        }
    }
}
