<?php

/**
 * LICENSE: The MIT License (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * https://github.com/azure/azure-storage-php/LICENSE
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @link      https://github.com/azure/azure-storage-php
 */

namespace MicrosoftAzure\Storage\Common\Internal;

use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\Validate;
use MicrosoftAzure\Storage\Common\Internal\Utilities;
use MicrosoftAzure\Storage\Common\Internal\RetryMiddlewareFactory;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Promise\EachPromise;

/**
 * Base class for all services rest proxies.
 *
 * @category  Microsoft
 * @package   MicrosoftAzure\Storage\Common\Internal
 * @author    Azure Storage PHP SDK <dmsh@microsoft.com>
 * @copyright 2016 Microsoft Corporation
 * @license   https://github.com/azure/azure-storage-php/LICENSE
 * @version   Release: 0.11.0
 * @link      https://github.com/azure/azure-storage-php
 */
class ServiceRestProxy extends RestProxy
{
    /**
     * @var string
     */
    private $_accountName;

    /**
     *
     * @var \Uri
     */
    private $_psrUri;

    /**
     * @var array
     */
    private $_options;

    /**
     * Initializes new ServiceRestProxy object.
     *
     * @param string      $uri            The storage account uri.
     * @param string      $accountName    The name of the account.
     * @param ISerializer $dataSerializer The data serializer.
     * @param array       $options        Array of options for the service
     */
    public function __construct($uri, $accountName, $dataSerializer, $options = [])
    {
        if ($uri[strlen($uri)-1] != '/') {
            $uri = $uri . '/';
        }

        parent::__construct($dataSerializer, $uri);

        $this->_accountName = $accountName;
        $this->_psrUri = new \GuzzleHttp\Psr7\Uri($uri);
        $this->_options = array_merge(array('http' => array()), $options);
    }

    /**
     * Gets the account name.
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->_accountName;
    }

    /**
     * Filter the request using the filters. This is for users to create
     * request.
     * @param \GuzzleHttp\Psr7\Request $request The request to be filtered.
     *
     * @return \GuzzleHttp\Psr7\Request          The filtered request.
     */
    protected function requestWithFilter($request)
    {
        // Apply filters to the requests
        foreach ($this->getFilters() as $filter) {
            $request = $filter->handleRequest($request);
        }
        return $request;
    }

    /**
     * Static helper function to create a usable client for the proxy.
     * The clientOptions can contain the following keys that will affect
     * the way retry handler is created and applied.
     * handler:               HandlerStack, if set, this function will not
     *                        create a handler stack. It will still construct
     *                        a default retry handler if not specified by the
     *                        following parameters.
     * have_retry_middleware: boolean, true if the handler is already specified
     *                        in the handler stack.
     * retry_middleware:      Middleware, if specified this method will not create
     *                        a default retry middle ware.
     * @param  array $clientOptions Added options for client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function createClient($clientOptions)
    {
        //If retry handler is not defined by the user, create a default
        //handler.
        $stack = null;
        if (array_key_exists('handler', $this->_options['http'])) {
            $stack = $this->_options['http']['handler'];
        } elseif (array_key_exists('handler', $clientOptions)) {
            $stack = $clientOptions['handler'];
        } else {
            $stack = HandlerStack::create();
            $clientOptions['handler'] = $stack;
        }

        //If retry middle ware is specified, push it to the client.
        //Otherwise use the default middle ware.
        if (array_key_exists('have_retry_middleware', $clientOptions) &&
            $clientOptions['have_retry_middleware'] == true) {
            //do nothing
        } elseif (array_key_exists('retry_middleware', $clientOptions)) {
            //push the retry middleware to the handler stack.
            $stack->push($clientOptions['retry_middleware']);
        } else {
            //construct the default retry middleware and push to the handler.
            $stack->push(RetryMiddlewareFactory::create(), 'retry_middleware');
        }
        return (new \GuzzleHttp\Client(
            array_merge(
                $this->_options['http'],
                array(
                    "defaults" => array(
                        "allow_redirects" => true,
                        "exceptions" => true,
                        "decode_content" => true,
                    ),
                    'cookies' => true,
                    'verify' => false,
                    // For testing with Fiddler
                    //'proxy' => "localhost:8888",
                ),
                $clientOptions
            )
        ));
    }

    /**
     * Send the requests concurrently. Number of concurrency can be modified
     * by inserting a new key/value pair with the key 'number_of_concurrency'
     * into the $clientOptions.
     *
     * @param  array    $requests              An array holding all the
     *                                         initialized requests. If empty,
     *                                         the first batch will be created
     *                                         using the generator.
     * @param  callable $generator             the generator function to
     *                                         generate request upon fullfilment
     * @param  int      $expectedStatusCode    The expected status code for each
     *                                         of the request.
     * @param  array    $clientOptions         an array of additional options
     *                                         for the client.
     *
     * @return array
     */
    protected function sendConcurrent(
        $requests,
        $generator,
        $expectedStatusCode,
        $clientOptions = []
    ) {
        //set the number of concurrency to default value if not defined
        //in the array.
        $numberOfConcurrency = Resources::NUMBER_OF_CONCURRENCY;
        if (array_key_exists('number_of_concurrency', $clientOptions)) {
            $numberOfConcurrency = $clientOptions['number_of_concurrency'];
            unset($clientOptions['number_of_concurrency']);
        }
        //create the client
        $client = $this->createClient($clientOptions);

        $promises = \call_user_func(
            function () use ($requests, $generator, $client) {
                $sendAsync = function ($request) use ($client) {
                    $options = $request->getMethod() == 'HEAD'?
                        array('decode_content' => false) : array();
                    return $client->sendAsync($request, $options);
                };
                foreach ($requests as $request) {
                    yield $sendAsync($request);
                }
                while (is_callable($generator) && ($request = $generator())) {
                    yield $sendAsync($request);
                }
            }
        );

        $eachPromise = new EachPromise($promises, [
            'concurrency' => $numberOfConcurrency,
            'fulfilled' => function ($response, $index) use ($expectedStatusCode) {
                //the promise is fulfilled, evaluate the response
                self::throwIfError(
                    $response->getStatusCode(),
                    $response->getReasonPhrase(),
                    $response->getBody(),
                    $expectedStatusCode
                );
            },
            'rejected' => function ($reason, $index) {
                //Still rejected even if the retry logic has been applied.
                //Throwing exception.
                throw $reason;
            }
        ]);
        
        return $eachPromise->promise()->wait();
    }

    /**
     * Create the request to be sent.
     *
     * @param  string $method         The method of the HTTP request
     * @param  array $headers        The header field of the request
     * @param  array $queryParams    The query parameter of the request
     * @param  array $postParameters The HTTP POST parameters
     * @param  string $path           URL path
     * @param  string $body           Request body
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function createRequest(
        $method,
        $headers,
        $queryParams,
        $postParameters,
        $path,
        $body = Resources::EMPTY_STRING
    ) {
        // add query parameters into headers
        $uri = $this->_psrUri;
        if ($path != null) {
            $uri = $uri->withPath($path);
        }

        if ($queryParams != null) {
            $queryString = Psr7\build_query($queryParams);
            $uri = $uri->withQuery($queryString);
        }

        // add post parameters into bodys
        $actualBody = null;
        if (empty($body)) {
            if (empty($headers['content-type'])) {
                $headers['content-type'] = 'application/x-www-form-urlencoded';
                $actualBody = Psr7\build_query($postParameters);
            }
        } else {
            $actualBody = $body;
        }

        $request = new Request(
            $method,
            $uri,
            $headers,
            $actualBody
        );

        //add content-length to header
        $bodySize = $request->getBody()->getSize();
        if ($bodySize > 0) {
            $request = $request->withHeader('content-length', $bodySize);
        }
        // Apply filters to the requests
        return $this->requestWithFilter($request);
    }

    /**
     * Sends HTTP request with the specified parameters.
     *
     * @param string $method         HTTP method used in the request
     * @param array  $headers        HTTP headers.
     * @param array  $queryParams    URL query parameters.
     * @param array  $postParameters The HTTP POST parameters.
     * @param string $path           URL path
     * @param int    $statusCode     Expected status code received in the response
     * @param string $body           Request body
     * @param array  $clientOptions  Guzzle Client options
     *
     * @return GuzzleHttp\Psr7\Response
     */
    protected function send(
        $method,
        $headers,
        $queryParams,
        $postParameters,
        $path,
        $statusCode,
        $body = Resources::EMPTY_STRING,
        $clientOptions = []
    ) {
        $request = $this->createRequest(
            $method,
            $headers,
            $queryParams,
            $postParameters,
            $path,
            $body
        );
        $client = $this->createClient($clientOptions);

        try {
            $options = $request->getMethod() == 'HEAD'?
                array('decode_content' => false) : array();
            $response = $client->send($request, $options);
            self::throwIfError(
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $response->getBody(),
                $statusCode
            );
            return $response;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                self::throwIfError(
                    $response->getStatusCode(),
                    $response->getReasonPhrase(),
                    $response->getBody(),
                    $statusCode
                );
                return $response;
            } else {
                throw $e;
            }
        }
    }

    protected function sendContext($context)
    {
        return $this->send(
            $context->getMethod(),
            $context->getHeaders(),
            $context->getQueryParameters(),
            $context->getPostParameters(),
            $context->getPath(),
            $context->getStatusCodes(),
            $context->getBody()
        );
    }

    /**
     * Throws ServiceException if the recieved status code is not expected.
     *
     * @param string $actual   The received status code.
     * @param string $reason   The reason phrase.
     * @param string $message  The detailed message (if any).
     * @param string $expected The expected status codes.
     *
     * @return none
     *
     * @static
     *
     * @throws ServiceException
     */
    public static function throwIfError($actual, $reason, $message, $expected)
    {
        $expectedStatusCodes = is_array($expected) ? $expected : array($expected);

        if (!in_array($actual, $expectedStatusCodes)) {
            throw new ServiceException($actual, $reason, $message);
        }
    }

    /**
     * Adds optional header to headers if set
     *
     * @param array           $headers         The array of request headers.
     * @param AccessCondition $accessCondition The access condition object.
     *
     * @return array
     */
    public function addOptionalAccessConditionHeader($headers, $accessCondition)
    {
        if (!is_null($accessCondition)) {
            $header = $accessCondition->getHeader();

            if ($header != Resources::EMPTY_STRING) {
                $value = $accessCondition->getValue();
                if ($value instanceof \DateTime) {
                    $value = gmdate(
                        Resources::AZURE_DATE_FORMAT,
                        $value->getTimestamp()
                    );
                }
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    /**
     * Adds optional header to headers if set
     *
     * @param array           $headers         The array of request headers.
     * @param AccessCondition $accessCondition The access condition object.
     *
     * @return array
     */
    public function addOptionalSourceAccessConditionHeader(
        $headers,
        $accessCondition
    ) {
        if (!is_null($accessCondition)) {
            $header     = $accessCondition->getHeader();
            $headerName = null;
            if (!empty($header)) {
                switch ($header) {
                    case Resources::IF_MATCH:
                        $headerName = Resources::X_MS_SOURCE_IF_MATCH;
                        break;
                    case Resources::IF_UNMODIFIED_SINCE:
                        $headerName = Resources::X_MS_SOURCE_IF_UNMODIFIED_SINCE;
                        break;
                    case Resources::IF_MODIFIED_SINCE:
                        $headerName = Resources::X_MS_SOURCE_IF_MODIFIED_SINCE;
                        break;
                    case Resources::IF_NONE_MATCH:
                        $headerName = Resources::X_MS_SOURCE_IF_NONE_MATCH;
                        break;
                    default:
                        throw new \Exception(Resources::INVALID_ACH_MSG);
                        break;
                }
            }
            $value = $accessCondition->getValue();
            if ($value instanceof \DateTime) {
                $value = gmdate(
                    Resources::AZURE_DATE_FORMAT,
                    $value->getTimestamp()
                );
            }

            $this->addOptionalHeader($headers, $headerName, $value);
        }

        return $headers;
    }

    /**
     * Adds HTTP POST parameter to the specified
     *
     * @param array  $postParameters An array of HTTP POST parameters.
     * @param string $key            The key of a HTTP POST parameter.
     * @param string $value          the value of a HTTP POST parameter.
     *
     * @return array
     */
    public function addPostParameter(
        $postParameters,
        $key,
        $value
    ) {
        Validate::isArray($postParameters, 'postParameters');
        $postParameters[$key] = $value;
        return $postParameters;
    }

    /**
     * Groups set of values into one value separated with Resources::SEPARATOR
     *
     * @param array $values array of values to be grouped.
     *
     * @return string
     */
    public static function groupQueryValues($values)
    {
        Validate::isArray($values, 'values');
        $joined = Resources::EMPTY_STRING;

        sort($values);

        foreach ($values as $value) {
            if (!is_null($value) && !empty($value)) {
                $joined .= $value . Resources::SEPARATOR;
            }
        }

        return trim($joined, Resources::SEPARATOR);
    }

    /**
     * Adds metadata elements to headers array
     *
     * @param array $headers  HTTP request headers
     * @param array $metadata user specified metadata
     *
     * @return array
     */
    protected function addMetadataHeaders($headers, $metadata)
    {
        $this->validateMetadata($metadata);

        $metadata = $this->generateMetadataHeaders($metadata);
        $headers  = array_merge($headers, $metadata);

        return $headers;
    }

    /**
     * Generates metadata headers by prefixing each element with 'x-ms-meta'.
     *
     * @param array $metadata user defined metadata.
     *
     * @return array.
     */
    public function generateMetadataHeaders($metadata)
    {
        $metadataHeaders = array();

        if (is_array($metadata) && !is_null($metadata)) {
            foreach ($metadata as $key => $value) {
                $headerName = Resources::X_MS_META_HEADER_PREFIX;
                if (strpos($value, "\r") !== false
                    || strpos($value, "\n") !== false
                ) {
                    throw new \InvalidArgumentException(Resources::INVALID_META_MSG);
                }

                // Metadata name is case-presrved and case insensitive
                $headerName                     .= $key;
                $metadataHeaders[$headerName] = $value;
            }
        }

        return $metadataHeaders;
    }

    /**
     * Gets metadata array by parsing them from given headers.
     *
     * @param array $headers HTTP headers containing metadata elements.
     *
     * @return array.
     */
    public function getMetadataArray($headers)
    {
        $metadata = array();
        foreach ($headers as $key => $value) {
            $isMetadataHeader = Utilities::startsWith(
                strtolower($key),
                Resources::X_MS_META_HEADER_PREFIX
            );

            if ($isMetadataHeader) {
                // Metadata name is case-presrved and case insensitive
                $MetadataName = str_ireplace(
                    Resources::X_MS_META_HEADER_PREFIX,
                    Resources::EMPTY_STRING,
                    $key
                );
                $metadata[$MetadataName] = $value;
            }
        }

        return $metadata;
    }

    /**
     * Validates the provided metadata array.
     *
     * @param mix $metadata The metadata array.
     *
     * @return none
     */
    public function validateMetadata($metadata)
    {
        if (!is_null($metadata)) {
            Validate::isArray($metadata, 'metadata');
        } else {
            $metadata = array();
        }

        foreach ($metadata as $key => $value) {
            Validate::isString($key, 'metadata key');
            Validate::isString($value, 'metadata value');
        }
    }
}
