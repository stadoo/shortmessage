<?php

namespace App\Entity;

use App\Repository\LikesHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikesHistoryRepository::class)]
class LikesHistory
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
    private ?int $likestatus = null;

    #[ORM\Column(type:'datetime', nullable:false)]
    private $date;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUid(): ?int
    {
        return $this->uid;
    }

    public function setUid(int $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getLikestatus(): ?int
    {
        return $this->likestatus;
    }

    public function setLikestatus(int $likestatus): static
    {
        $this->likestatus = $likestatus;

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
}