<?php

namespace Chefkoch\ExampleClient\Tests;

use Chefkoch\ExampleClient\Gateway\HttpRecipeGateway;
use Chefkoch\ExampleClient\Gateway\RecipeGatewayInterface;
use Http\Adapter\Guzzle6\Client;
use Madkom\PactBrokerClient\HttpBrokerClient;
use Madkom\PactBrokerClient\RequestBuilder;
use Madkom\PactClient\Application\ConsumerPactBuilder;
use Madkom\PactClient\Domain\Interaction\InteractionFactory;
use Madkom\PactClient\Http\HttpMockServiceCollaborator;

class RecipeContractTest extends \PHPUnit_Framework_TestCase
{

    /** @var RecipeGatewayInterface */
    private $recipeGateway;
    /** @var HttpMockServiceCollaborator */
    private $serviceCollaborator;
    /** @var ConsumerPactBuilder */
    private $pactContractBuilder;

    /** @var HttpBrokerClient */
    private $pactBrokerClient;

    public function setup()
    {
        $mockHost = getenv('PACT_MOCK_HOST');
        $brokerHost = getenv('PACT_BROKER_HOST');
        $this->recipeGateway = new HttpRecipeGateway(
            new Client(),
            $mockHost
        );
        $this->serviceCollaborator = new HttpMockServiceCollaborator(new Client(), $mockHost, 'my-client', 'my-provider');
        $this->pactContractBuilder = new ConsumerPactBuilder(new InteractionFactory());
        $this->pactBrokerClient = new HttpBrokerClient($brokerHost, new Client(), new RequestBuilder());
    }

    public function testRecipeContract()
    {
        $interaction = $this->pactContractBuilder
            ->given("Recipe exists")
            ->uponReceiving('A recipe')
            ->with([
                'method' => 'get',
                'path' => '/recipes/1'
            ])
            ->willRespondWith([
                'status' => 200,
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => [
                    'id' => '1',
                    'title' => 'Cheese-Burger'
                ]
            ])
            ->interactionFromBuild();

        $this->serviceCollaborator->setupInteraction($interaction);

        $recipe = $this->recipeGateway->getRecipe('1');
        $this->assertEquals('1', $recipe->getId());
        $this->assertEquals('Cheese-Burger', $recipe->getTitle());

        $this->serviceCollaborator->verify();
        $this->serviceCollaborator->finishProviderVerificationProcess();

        $this->pactBrokerClient->publishPact('my-provider', 'my-client', '1.0.0', '/opt/contracts/my-client-my-provider.json');
    }

}
