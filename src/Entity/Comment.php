<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Le contenu du commentaire est obligatoire.")]
    #[Assert\Length(min: 2, minMessage: "Le commentaire doit contenir au moins {{ limit }} caractères.")]
    private ?string $body = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    #[Assert\Choice(
        choices: ["visible", "hidden", "solution"],
        message: "Statut invalide. Choisis: visible, hidden, solution."
    )]
    private ?string $status = "visible";

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: "Le nombre de likes doit être >= 0.")]
    private int $likes = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

<<<<<<< HEAD
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;
=======
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;
>>>>>>> main

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, name: "post_id", referencedColumnName: "id")]
    private ?Post $post = null;

<<<<<<< HEAD
    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private ?Users $user = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'replies')]
    #[ORM\JoinColumn(name: "replay_id", referencedColumnName: "id")]
    private ?self $replay = null;
=======
    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Users $user = null;

    
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'replies')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?self $parentComment = null;
>>>>>>> main

    /**
     * @var Collection<int, self>
     */
<<<<<<< HEAD
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'replay')]
=======
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parentComment', cascade: ['remove'])]
>>>>>>> main
    private Collection $replies;

    public function __construct()
    {
<<<<<<< HEAD
        $this->replies = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
=======
        $this->createdAt = new \DateTimeImmutable();
        $this->replies = new ArrayCollection();
>>>>>>> main
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): static
    {
        $this->likes = $likes;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
<<<<<<< HEAD
        $this->created_at = $created_at;

=======
        $this->createdAt = $createdAt;
>>>>>>> main
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

<<<<<<< HEAD
    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

=======
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
>>>>>>> main
        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;
        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getParentComment(): ?self
    {
        return $this->parentComment;
    }

    public function setParentComment(?self $parentComment): static
    {
        $this->parentComment = $parentComment;
        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(self $reply): static
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
<<<<<<< HEAD
            $reply->setReplay($this);
=======
            $reply->setParentComment($this);
>>>>>>> main
        }
        return $this;
    }

    public function removeReply(self $reply): static
    {
        if ($this->replies->removeElement($reply)) {
<<<<<<< HEAD
            // set the owning side to null (unless already changed)
            if ($reply->getReplay() === $this) {
                $reply->setReplay(null);
=======
            if ($reply->getParentComment() === $this) {
                $reply->setParentComment(null);
>>>>>>> main
            }
        }
        return $this;
    }
}
