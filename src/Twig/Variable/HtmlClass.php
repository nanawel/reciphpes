<?php

namespace App\Twig\Variable;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class HtmlClass
{
    /** @var Request */
    protected $request;

    public function __construct(RequestStack $requestStack) {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function __toString() {
        $routeParts = explode('_', $this->request->attributes->get('_route'));

        $htmlClass = [
            implode('-', $routeParts)
        ];
        if (count($routeParts) > 1) {
            $htmlClass[] = sprintf('%s-%s', $routeParts[0], $routeParts[1]);
        }
        if (count($routeParts) > 2) {
            $htmlClass[] = "action-{$routeParts[2]}";
        }

        return implode(' ', array_unique($htmlClass));
    }
}
