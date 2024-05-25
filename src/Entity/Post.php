<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Resource\SelfCheckingResourceChecker;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
     #[ORM\JoinColumn(nullable: false)]
     private ?User $author = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 2000)]
    private ?string $text = null;

    #[ORM\Column]
    private ?int $status = null;

     #[ORM\Column(type:'datetime', nullable:false)]
    private $date;

     #[ORM\ManyToOne(inversedBy: 'posts')]
     #[ORM\JoinColumn(nullable: false)]
     private ?Category $category = null;

     #[ORM\Column]
     private ?int $likeCount = null;

     #[ORM\Column]
     private ?int $dislikeCount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getLikeCount(): ?int
    {
        return $this->likeCount;
    }

    public function setLikeCount(int $likeCount): static
    {
        $this->likeCount = $likeCount;

        return $this;
    }
    
    public function incrementLikeCount(): self 
    {
        $this->likeCount++;

        return $this;
    }

    public function getDislikeCount(): ?int
    {
        return $this->dislikeCount;
    }

    public function setDislikeCount(int $dislikeCount): static
    {
        $this->dislikeCount = $dislikeCount;

        return $this;
    }

    public function incrementDislikeCount(): self
    {
        $this->dislikeCount++;

        return $this;
    }


}