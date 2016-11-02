<?php

namespace Chefkoch\ExampleClient\Gateway;

interface RecipeGatewayInterface
{

    /**
     * @param string $id
     * @return Recipe
     */
    public function getRecipe(string $id): Recipe;

}
