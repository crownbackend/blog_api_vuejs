<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @Groups("article")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("article")
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Groups("article")
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Groups("article")
     * @ORM\Column(type="string", length=255)
     */
    private $image_name;

    /**
     * @Groups("article")
     * @ORM\Column(type="boolean")
     */
    private $Published;

    /**
     * @Groups("article")
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="articles")
     */
    private $category;

    /**
     * @Groups("article")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageName()
    {
        return $this->image_name;
    }

    public function setImageName($image_name)
    {
        $this->image_name = $image_name;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->Published;
    }

    public function setPublished(bool $Published): self
    {
        $this->Published = $Published;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
