<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields = {"email"},
 *  message = "L'email que vous avez indique est deja utilise"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre message doit faire minimum 8 caracteres")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Votre confirmation de mot de passe n'est pas identique au mot de passe !")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;


     /**
 * @ORM\Column(type="json")
 */
private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

        /*
     * Get token
     */
    public function getToken()
    {
        return $this->token;
    }
 
    /*
     * Set token
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function eraseCredentials()
    {
        
    }

    public function getConfirmationToken()
    {

    }

    public function setConfirmationToken()
    {

    }



    public function getSalt()
    {
        
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
}