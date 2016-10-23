<?php
declare(strict_types=1);

namespace Front\Controller;

use Aerys\{Response, Request};

class RedirectController
{
    /**
     * HTTP status code
     * @var integer
     */
    private $statusCode;

    /**
     * domain name
     * @var string
     */
    private $domain;

    /**
     * secure connection flag
     * @var bool
     */
    private $secure;

    /**
     * should we pass the path forward
     * @var bool
     */
    private $pathForward;

    /**
     * @todo would be nice to ensure $domain conforms to some general rules:
     *  - no slash at the end
     *  - no schema in the front
     *
     * @param int    $statusCode [description]
     * @param string $domain     [description]
     * @param bool   $secure     [description]
     */
    public function __construct(int $statusCode, string $domain, bool $secure, bool $pathForward)
    {
        $this->statusCode = $statusCode;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->pathForward = $pathForward;
    }

    public function __invoke(Request $request, Response $response, array $args): void
    {
        $path = $this->pathForward ? $args['path'] : '';
        $destination = $this->getDestination($path);
        $response->setStatus($this->statusCode);
        $response->addHeader('Location', $destination);
        $response->end(null);
    }

    /**
     * @param  string $path
     * @return string
     */
    private function getDestination(string $path): string
    {
        return ($this->secure ? 'https://' : 'http://').$this->domain.'/'.$path;
    }

}
