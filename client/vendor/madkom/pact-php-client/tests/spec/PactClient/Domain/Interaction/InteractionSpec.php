<?php

namespace spec\Madkom\PactClient\Domain\Interaction;

use Madkom\PactClient\Application\Pact;
use Madkom\PactClient\Domain\Interaction\Communication\Body;
use Madkom\PactClient\Domain\Interaction\Communication\Header;
use Madkom\PactClient\Domain\Interaction\Communication\Method;
use Madkom\PactClient\Domain\Interaction\Communication\Path;
use Madkom\PactClient\Domain\Interaction\Communication\Query;
use Madkom\PactClient\Domain\Interaction\Communication\StatusCode;
use Madkom\PactClient\Domain\Interaction\Description;
use Madkom\PactClient\Domain\Interaction\Interaction;
use Madkom\PactClient\Domain\Interaction\InteractionRequest;
use Madkom\PactClient\Domain\Interaction\InteractionResponse;
use Madkom\PactClient\Domain\Interaction\ProviderState;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class InteractionSpec
 * @package spec\Madkom\PactClient\Domain\Interaction
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin Interaction
 */
class InteractionSpec extends ObjectBehavior
{

    public function let(ProviderState $providerState, Description $description, InteractionRequest $interactionRequest, InteractionResponse $interactionResponse)
    {
        $this->beConstructedWith($providerState, $description, $interactionRequest, $interactionResponse);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(\JsonSerializable::class);
    }

    function it_should_return_as_array()
    {
        $description   = new Description('A request for foo');
        $providerState = new ProviderState('foo exists');

        $request = $this->createRequest(Method::GET, '/client', [
            "name" => "franek"
        ], [
            "accept" => "application/json"
        ]);
        $response = $this->createResponse(StatusCode::OK_CODE, [], [
            "Content-Type" => "application/json"
        ]);

        $this->beConstructedWith($providerState, $description, $request, $response);

        \PHPUnit_Framework_Assert::assertEquals(json_encode([
            "description"       => 'A request for foo',
            "provider_state"    => 'foo exists',
            "request" => [
                "method"  => "get",
                "path"    => "/client",
                "headers" => [
                    "accept" => "application/json"
                ],
                "body" => [
                    "name" => "franek"
                ]
            ],
            "response" => [
                "status"  => 200,
                "headers" => [
                    "Content-Type" => "application/json"
                ]
            ]
        ]), json_encode($this->jsonSerialize()->getWrappedObject()));
    }

    function it_should_return_correct_array_for_nested_matchers()
    {
        $description   = new Description('A request for foo');
        $providerState = new ProviderState('foo exists');

        $request = $this->createRequest(
            Method::GET,
            '/client',
            [],
            [
                "accept" => "application/json"
            ]
        );
        $response = $this->createResponse(
            StatusCode::OK_CODE,
            [
                "match" => Pact::eachLike(
                    Pact::eachLike([
                        "size"      => Pact::like(10),
                        "colour"    => Pact::term("red", "red|green|blue"),
                        "tag"       => Pact::eachLike([
                            Pact::like("jumper"),
                            Pact::like("shirt")
                        ], 2)
                    ])
                )
            ],
            [
                "Content-Type" => "application/json"
            ]
        );

        $this->beConstructedWith($providerState, $description, $request, $response);

        \PHPUnit_Framework_Assert::assertEquals(json_encode([
            "description"       => 'A request for foo',
            "provider_state"    => 'foo exists',
            "request" => [
                "method"  => "get",
                "path"    => "/client",
                "headers" => [
                    "accept" => "application/json"
                ]
            ],
            "response" => [
                "status"  => 200,
                "headers" => [
                    "Content-Type" => "application/json"
                ],
                "body" => [
                    "match" =>
                        [
                            "json_class" => "Pact::ArrayLike",
                            "contents" => [
                                "json_class" => "Pact::ArrayLike",
                                "contents" =>[
                                    "size" => [
                                        "json_class" => "Pact::SomethingLike",
                                        "contents"   => 10
                                    ],
                                    "colour" => [
                                        "json_class" => "Pact::Term",
                                        "data" => [
                                            "generate" => "red",
                                            "matcher" => [
                                                "json_class" => "Regexp",
                                                "o"          => 0,
                                                "s"          => "red|green|blue"
                                            ]
                                        ]
                                    ],
                                    "tag" => [
                                        "json_class" => "Pact::ArrayLike",
                                        "contents"   => [
                                            [
                                                "json_class" => "Pact::SomethingLike",
                                                "contents" => "jumper"
                                            ],
                                            [
                                                "json_class" => "Pact::SomethingLike",
                                                "contents"   => "shirt"
                                            ]
                                        ],
                                        "min" => 2
                                    ]
                                ],
                                "min" => 1
                            ],
                            "min" => 1
                        ]
                ]
            ]
        ]), json_encode($this->jsonSerialize()->getWrappedObject()));
    }

    function it_should_set_null_values_when_no_values_provided()
    {
        $description   = new Description('A request for foo');
        $providerState = new ProviderState('foo exists');
        $request  = $this->createRequest("get", "/client");
        $response = $this->createResponse(200);

        $this->beConstructedWith($providerState, $description, $request, $response);

        \PHPUnit_Framework_Assert::assertEquals(json_encode([
            "description"       => 'A request for foo',
            "provider_state"    => 'foo exists',
            "request" => [
                "method"  => "get",
                "path"    => "/client"
            ],
            "response" => [
                "status"  => 200
            ]
        ]), json_encode($this->jsonSerialize()->getWrappedObject()));
    }

    function it_should_not_contains_provider_state_if_is_empty()
    {
        $description   = new Description('A request for foo');
        $providerState = new ProviderState('');
        $request  = $this->createRequest("get", "/client");
        $response = $this->createResponse(200);

        $this->beConstructedWith($providerState, $description, $request, $response);

        \PHPUnit_Framework_Assert::assertEquals(json_encode([
            "description"       => 'A request for foo',
            "request" => [
                "method"  => "get",
                "path"    => "/client"
            ],
            "response" => [
                "status"  => 200
            ]
        ]), json_encode($this->jsonSerialize()->getWrappedObject()));
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $body
     * @param array $header
     * @param array $query
     *
     * @return InteractionRequest
     */
    private function createRequest($method, $path, array $body = [], array $header = [], array $query = [])
    {
        $requestBody = new Body($body);
        $requestHeader = new Header($header);
        $requestQuery = new Query($query);

        return new InteractionRequest(new Method($method), new Path($path), $requestBody, $requestHeader, $requestQuery);
    }

    /**
     * @param int   $statusCode
     * @param array $body
     * @param array $header
     *
     * @return InteractionResponse
     */
    private function createResponse($statusCode, array $body = [], array $header = [])
    {
        $requestBody = new Body($body);
        $requestHeader = new Header($header);

        return new InteractionResponse(new StatusCode($statusCode), $requestBody, $requestHeader);
    }

}
