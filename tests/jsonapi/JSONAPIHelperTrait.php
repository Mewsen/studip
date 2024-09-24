<?php

use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;
use WoohooLabs\Yang\JsonApi\Schema\Document;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

trait JSONAPIHelperTrait
{
    protected JSONAPITester $tester;

    protected function _before()
    {
        DBManager::getInstance()->setConnection(
            'studip',
            $this->getModule('\\Helper\\StudipDb')->dbh
        );
    }

    protected function withStudipEnv(array $credentials, callable $fn)
    {
        // Create global template factory if neccessary
        $has_template_factory = isset($GLOBALS['template_factory']);
        if (!$has_template_factory) {
            $GLOBALS['template_factory'] = new Flexi\Factory($GLOBALS['STUDIP_BASE_PATH'] . '/templates');
        }

        $result = $this->tester->withPHPLib($credentials, $fn);

        if (!$has_template_factory) {
            unset($GLOBALS['template_factory']);
        }

        return $result;
    }

    protected function sendMockRequest(string $route, string $handler, array $credentials, array $variables = [], array $options = []): JsonApiResponse
    {
        $options = array_merge([
            'method'                => 'GET',
            'considered_successful' => [200],
            'json_body'             => null,
        ], $options);

        $app = $this->tester->createApp(
            $credentials,
            strtolower($options['method']),
            $route,
            $handler
        );

        $evaluated_route = preg_replace_callback(
            '/\{(.+?)(:[^}]+)?}/',
            function ($match) use ($variables) {
                $key = $match[1];
                if (!isset($variables[$key])) {
                    throw new Exception("No variable '{$key}' defined");
                }
                return $variables[$key];
            },
            $route
        );

        $requestBuilder = $this->tester->createRequestBuilder($credentials);
        $requestBuilder->setUri($evaluated_route)->setMethod(strtoupper($options['method']));

        if (isset($options['json_body'])) {
            $requestBuilder->setJsonApiBody($options['json_body']);

        }

        /** @var JsonApiResponse $response */
        $response = $this->withStudipEnv($credentials, function () use ($app, $requestBuilder) {
            return $this->tester->sendMockRequest($app, $requestBuilder->getRequest());
        });

        if ($options['considered_successful']) {
            $this->assertTrue(
                $response->isSuccessful($options['considered_successful']),
                'Actual status code is ' . $response->getStatusCode()
            );
        }

        return $response;
    }

    protected function getSingleResourceDocument(JsonApiResponse $response): Document
    {
        $this->assertTrue($response->hasDocument());

        $document = $response->document();
        $this->assertTrue($document->isSingleResourceDocument());

        return $document;
    }

    protected function getResourceCollectionDocument(JsonApiResponse $response): Document
    {
        $this->assertTrue($response->hasDocument());

        $document = $response->document();
        $this->assertTrue($document->isResourceCollectionDocument());

        return $document;
    }

    protected function assertHasRelations(ResourceObject $resource, ...$relations)
    {
        foreach ($relations as $relation) {
            $this->assertTrue($resource->hasRelationship($relation));
        }
    }

    protected function getResourceFromResponse(JsonApiResponse $response): ResourceObject
    {
        return $this->getSingleResourceDocument($response)->primaryResource();
    }
}
