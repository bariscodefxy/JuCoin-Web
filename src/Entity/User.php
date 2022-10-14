<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $steamid = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    private ?int $coins = null;

    #[ORM\Column]
    private ?int $registertime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSteamid(): ?string
    {
        return $this->steamid;
    }

    public function setSteamid(string $steamid): self
    {
        $this->steamid = $steamid;

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getCoins(): ?int
    {
        return $this->coins;
    }

    public function setCoins(int $coins): self
    {
        $this->coins = $coins;

        return $this;
    }

    public function getRegistertime(): ?int
    {
        return $this->registertime;
    }

    public function setRegistertime(int $registertime): self
    {
        $this->registertime = $registertime;

        return $this;
    }
}
