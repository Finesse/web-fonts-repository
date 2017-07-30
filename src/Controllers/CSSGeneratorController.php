<?php

namespace Src\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Src\Services\WebfontCSSGenerator;

/**
 * Class CSSGeneratorController
 *
 * The controller for generating webfonts CSS files.
 *
 * @author Finesse
 * @package Src\Controllers
 */
class CSSGeneratorController
{
    /**
     * @var ContainerInterface Dependencies container
     */
    protected $container;

    /**
     * @param ContainerInterface $container Dependencies container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Runs the controller action.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Getting and checking the request data
        $requestParams = $request->getQueryParams();
        if (!isset($requestParams['family'])) {
            return $this->createErrorResponse('The `family` query parameter is not set');
        }
        try {
            $requestedFonts = $this->parseFamilyParameter($requestParams['family']);
        } catch (\InvalidArgumentException $error) {
            return $this->createErrorResponse($error->getMessage());
        }

        // Generating the CSS code
        /** @var WebfontCSSGenerator $webfontCSSGenerator */
        $webfontCSSGenerator = $this->container->get('webfontCSSGenerator');
        try {
            $cssCode = $webfontCSSGenerator->makeCSS($requestedFonts);
        } catch (\InvalidArgumentException $error) {
            return $this->createErrorResponse($error->getMessage());
        }

        // Sending the response
        return $response
            ->withHeader('Content-Type', 'text/css; charset=UTF-8')
            ->write($cssCode);
    }

    /**
     * Converts family parameter value to families data array.
     *
     * @param string $value Parameter value. Example: `Open Sans:400,700|Roboto:100,100i,400,400i`.
     * @return array[] Value data. Example:
     * <pre>
     *  [
     *      'Open Sans' => ['400', '700'],
     *      'Roboto'    => ['100', '100i', '400', '400i']
     *  ]
     * </pre>
     * @throws \InvalidArgumentException If the parameter value is formatted badly. The message may be sent back to the
     *     client.
     */
    protected function parseFamilyParameter(string $value): array
    {
        $result = [];
        $families = explode('|', $value);

        foreach ($families as $family) {
            $familyDetails = explode(':', $family, 2);

            $name = $familyDetails[0];
            if ($name === '') {
                throw new \InvalidArgumentException('An empty font family name is set');
            }

            $stylesValue = $familyDetails[1] ?? null;
            if ($stylesValue === null || $stylesValue === '') {
                $stylesValue = '400';
            }

            $styles = explode(',', $stylesValue);
            $result[$name] = $styles;
        }

        return $result;
    }

    /**
     * Creates a response with the client side error message.
     *
     * @param string $message Error message for the client
     * @param int $status HTTP status code
     * @return ResponseInterface
     *
     * @see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes HTTP status codes
     */
    protected function createErrorResponse(string $message, int $status = 422): ResponseInterface
    {
        return $this->container->get('response')
            ->withStatus($status)
            ->write($message);
    }
}
