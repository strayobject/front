<?php
declare(strict_types=1);

namespace Front\Controller;

use Aerys\{Response, Request};

class PageController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response, array $args): void
    {
        $content = $this->twig->render(sprintf('pages/%s.html', $args['page']), ['title' => $args['page']]);
        $response->end($content);
    }

}
