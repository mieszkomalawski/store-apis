<?php
/**
 * Created by PhpStorm.
 * User: mmalawski
 * Date: 29/04/2018
 * Time: 13:23
 */

namespace Store\Catalog;


class ProductCategory
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /**
     * Category constructor.
     * @param string $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}