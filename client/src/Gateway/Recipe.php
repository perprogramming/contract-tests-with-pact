<?php

namespace Chefkoch\ExampleClient\Gateway;

class Recipe
{

    /** @var string */
    private $id;
    /** @var string */
    private $title;

    /**
     * @param string $id
     * @param string $title
     */
    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
