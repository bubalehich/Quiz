<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuizRepository::class)
 */
class Quiz
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint")
     */
    private $is_active;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $leader;

    /**
     * @ORM\Column(type="integer")
     */
    private $users_count;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsActive(): ?int
    {
        return $this->is_active;
    }

    public function setIsActive(int $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->create_date;
    }

    public function setCreateDate(\DateTimeInterface $create_date): self
    {
        $this->create_date = $create_date;

        return $this;
    }

    public function getLeader(): ?int
    {
        return $this->leader;
    }

    public function setLeader(?int $leader): self
    {
        $this->leader = $leader;

        return $this;
    }

    public function getUsersCount(): ?int
    {
        return $this->users_count;
    }

    public function setUsersCount(int $users_count): self
    {
        $this->users_count = $users_count;

        return $this;
    }
}
