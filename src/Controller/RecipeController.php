<?php

namespace App\Controller;


use App\Entity\Location;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RecipeController extends DocumentController
{
    protected function _getEntityConfig($config = null) {
        return $this->getEntityRegistry()->getEntityConfig('recipe', $config);
    }

    /**
     * @ParamConverter("entity", class="App:Recipe")
     *
     * @inheritDoc
     */
    public function show($entity) {
        return parent::show($entity);
    }

    /**
     * @ParamConverter("entity", class="App:Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function edit(Request $request, object $entity = null) {
        return parent::edit($request, $entity);
    }

    /**
     * @ParamConverter("entity", class="App:Recipe", isOptional="true")
     *
     * @inheritDoc
     */
    public function delete(Request $request, object $entity = null) {
        return parent::delete($request, $entity);
    }

    public function searchTags(Request $request) {
        $term = $request->get('term');

        /** @var TagRepository $repository */
        $repository = $this->getEntityManager()->getRepository(Tag::class);

        $result = [];
        foreach ($repository->findLike($term) as $tag) {
            $result[] = $tag->getName();
        }

        return $this->json($result);
    }
}
