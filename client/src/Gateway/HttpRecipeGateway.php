<?php

namespace Chefkoch\ExampleClient\Gateway;

use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;

class HttpRecipeGateway implements RecipeGatewayInterface
{

    /** @var HttpClient */
    private $client;
    /** @var string */
    private $host;

    /**
     * @param HttpClient $client
     * @param string $host
     */
    public function __construct(HttpClient $client, string $host)
    {
        $this->client = $client;
        $this->host = $host;
    }

    /**
     * @param string $id
     * @return Recipe
     */
    public function getRecipe(string $id): Recipe
    {
        $request = new Request('GET', "http://{$this->host}/recipes/{$id}");
        $response = $this->client->sendRequest($request);
        $data = json_decode($response->getBody(), true);

        return new Recipe($data['id'], $data['title']);
    }
}
