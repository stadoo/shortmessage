<?php

namespace App\Entity;

use App\Repository\UserLikesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserLikesRepository::class)]
class UserLikes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $uid = null;

    #[ORM\Column]
    private ?int $postid = null;

    #[ORM\Column]
    private ?int $like_status = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?int
    {
        return $this->uid;
    }

    public function setUid(int $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getPostid(): ?int
    {
        return $this->postid;
    }

    public function setPostid(int $postid): static
    {
        $this->postid = $postid;

        return $this;
    }

    public function getLikeStatus(): ?int
    {
        return $this->like_status;
    }

    public function setLikeStatus(int $like_status): static
    {
        $this->like_status = $like_status;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }
}