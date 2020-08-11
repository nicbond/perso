<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopRepository")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_shop_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "app_shop_update",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "app_shop_delete",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 *
 */
class Shop
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=4, max=255, allowEmptyString=false)
     */
    private $nameShop;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=255, allowEmptyString=false)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^[0-9]{5}$/")
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=4, max=255, allowEmptyString=false)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=255, allowEmptyString=false)
     */
    private $image;

    /**
     * @ORM\Column(type="float")
     */
    private $offer;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true, "default":0})
     */
    private $id_shop;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameShop(): ?string
    {
        return $this->nameShop;
    }

    public function setNameShop(string $nameShop): self
    {
        $this->nameShop = $nameShop;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getOffer(): ?float
    {
        return $this->offer;
    }

    public function setOffer(float $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getIdShop(): ?int
    {
        return $this->id_shop;
    }

    public function setIdShop(?int $id_shop): self
    {
        $this->id_shop = $id_shop;

        return $this;
    }
}
