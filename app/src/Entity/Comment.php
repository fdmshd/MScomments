<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *@Groups ("main")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups ("main")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime")
     * @Groups ("main")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Groups ("main")
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups ("main")
     */
    private $object_name;

    /**
     * @ORM\Column(type="integer")
     * @Groups ("main")
     */
    private $object_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUserid(): ?int
    {
        return $this->user_id;
    }

    public function setUserid(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getObjectName(): ?string
    {
        return $this->object_name;
    }

    public function setObjectName(string $object_name): self
    {
        $this->object_name = $object_name;

        return $this;
    }

    public function getObjectId(): ?int
    {
        return $this->object_id;
    }

    public function setObjectId(int $object_id): self
    {
        $this->object_id = $object_id;

        return $this;
    }
}
