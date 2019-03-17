<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    /**
     * not mapped
     */
    private $plainPassword;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function __construct()
    {
        $this->isActive = true;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function addRole($role): self
    {
        $this->roles[] = $role;
        return $this;
    }

    public function eraseCredentials()
    {
    }

    /**
     * Present du to interface but not needed thanks to argon2i
     */
    public function getSalt()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param mixed $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }



    /**
     * @param mixed $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }



    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function __toString()
    {
        return $this->username;
    }
}
