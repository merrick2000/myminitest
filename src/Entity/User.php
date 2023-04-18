<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Please enter a password")
     * @Assert\Length(min=6, minMessage="Your password should be at least {{ limit }} characters")
     */
    private $password;

    /**
     * @ORM\Column(type="float")
     */
    private $credits;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="purchasedBy", orphanRemoval=true)
     */
    private $orders;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->roles[] = 'ROLE_USER';
        $this->setCredits(0.0);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCredits(): ?float
    {
        return $this->credits;
    }

    public function setCredits(float $credits): self
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setPurchasedBy($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getPurchasedBy() === $this) {
                $order->setPurchasedBy(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function addRole(string $role): void
    {
        if(!in_array($role,$this->getRoles())){
            $roles = $this->roles;
            $roles[] = $role;
            $this->roles = $roles;
        }
    }

    public function getSalt()
    {
        // leave empty if you're using bcrypt or argon2i
        return null;
    }
    
    public function eraseCredentials()
    {
        // if you store any plain-text password in this entity, erase it here
    }
}
