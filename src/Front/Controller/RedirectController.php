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
     * @todo would be nice to ensure $domain conforms to some general rules:
     *  - no slash at the end
     *  - no schema in the front
     *
     * @param int    $statusCode [description]
     * @param string $domain     [description]
     * @param bool   $secure     [description]
     */
    public function __construct(int $statusCode, string $domain, bool $secure)
    {
        $this->statusCode = $statusCode;
        $this->domain = $domain;
        $this->secure = $secure;
    }

    public function __invoke(Request $request, Response $response, array $args): void
    {
        $destination = $this->getDestination($args['path']);
        $response->setStatus($this->statusCode);
        $response->addHeader('Location', $destination);
        $response->end(null);
    }

    /**
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    private function getDestination($path): string
    {
        return ($this->secure ? 'https://' : 'http://').$this->domain.'/'.$path;
    }

}
