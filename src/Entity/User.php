<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message:'There is already an account with this username')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Post::class)]
    private Collection $posts;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type:'string', length:255, unique:true)]
    #[Assert\NotBlank()]
    #[Assert\Length(min:5, max:255)]
    private $username;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type:'json', nullable: true)]
    #[Assert\Count(max:3, maxMessage:'You cannot specify more than 3 social links.')]
    #[Assert\Callback([User::class, 'validateSocialLinks'])]

    private $socialLinks = [];

    //for password-reset
    #[ORM\Column(type:'string', length:255, nullable:true)]
    private $resetToken;

    //for password-reset
    #[ORM\Column(type:'datetime', nullable:true)]
    private $tokenExpirationDate;

    public function __construct()
    {
        //$this->socialLinks = new ArrayCollection();
        $this->socialLinks = [];

    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getSocialLinks(): array
    {
        return is_array($this->socialLinks) ? $this->socialLinks : [];
    }

    public function setSocialLinks(?array $socialLinks): self
    {
        $this->socialLinks = $socialLinks ?? [];

        return $this;
    }

    public function addSocialLink(string $link): self
    {
        $socialLinks = $this->getSocialLinks();
        if (count($socialLinks) < 3) {
            $this->socialLinks[] = $link;
        } else {
            throw new \Exception('You cannot add more than 3 social links.');
        }

        return $this;
    }

    public function removeSocialLink(string $link): self
    {
        if (($key = array_search($link, $this->socialLinks)) !== false) {
            unset($this->socialLinks[$key]);
            $this->socialLinks = array_values($this->socialLinks); // Reindex array
        }

        return $this;
    }

    public static function validateSocialLinks(array $socialLinks, ExecutionContextInterface $context): void
    {
        $socialLinks = is_array($socialLinks) ? $socialLinks : [];

        foreach ($socialLinks as $link) {
            if (!preg_match('/(twitter\.com|x\.com|facebook\.com|linkedin\.com)/', $link)) {
                $context->buildViolation('Links must be from Twitter, Facebook, or Linkedin.')
                    ->atPath('socialLinks')
                    ->addViolation();
            }
        }
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // for passwordreset
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }
    // for passwordreset
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }
    // for passwordreset
    public function getTokenExpirationDate(): ?\DateTimeInterface
    {
        return $this->tokenExpirationDate;
    }
    // for passwordreset
    public function setTokenExpirationDate(?\DateTimeInterface $tokenExpirationDate): self
    {
        $this->tokenExpirationDate = $tokenExpirationDate;
        return $this;
    }
    // for passwordreset
    public function isTokenExpired(): bool
    {
        return $this->tokenExpirationDate < new \DateTime();
    }

    public function __toString()
    {
        return $this->email;
    }
}