<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProgressRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProgressRepository::class)
 */
class Progress
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\OneToOne(targetEntity=Question::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Question $question;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isRight;

    /**
     * @ORM\ManyToOne(targetEntity=Result::class, inversedBy="progress", cascade={"persist"})
     */
    private ?Result $result;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getIsRight(): bool
    {
        return $this->isRight;
    }

    public function setIsRight(bool $isRight): self
    {
        $this->isRight = $isRight;

        return $this;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }

    public function setResult(?Result $result): self
    {
        $this->result = $result;

        return $this;
    }
}