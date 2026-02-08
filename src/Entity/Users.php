<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(nullable: true)]
    private ?int $groupid = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numtel = null;

<<<<<<< HEAD
=======
    #[ORM\Column(nullable: true)]
    private ?int $managerId = null;

    #[ORM\Column(nullable: true)]
    private ?int $creatorId = null;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $posts;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $comments;

    /**
     * @var Collection<int, CollabRequest>
     */
    #[ORM\OneToMany(targetEntity: CollabRequest::class, mappedBy: 'creator')]
    private Collection $collabRequestsCreated;

    /**
     * @var Collection<int, CollabRequest>
     */
    #[ORM\OneToMany(targetEntity: CollabRequest::class, mappedBy: 'revisor')]
    private Collection $collabRequestsRevised;

    /**
     * @var Collection<int, Contract>
     */
    #[ORM\OneToMany(targetEntity: Contract::class, mappedBy: 'creator')]
    private Collection $contracts;

    /**
     * @var Collection<int, Collaborator>
     */
    #[ORM\OneToMany(targetEntity: Collaborator::class, mappedBy: 'addedBy')]
    private Collection $addedCollaborators;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->collabRequestsCreated = new ArrayCollection();
        $this->collabRequestsRevised = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->addedCollaborators = new ArrayCollection();
    }

>>>>>>> main
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = [$this->role ?: 'ROLE_USER'];

        return array_unique($roles);
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {

    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getGroupid(): ?int
    {
        return $this->groupid;
    }

    public function setGroupid(int $groupid): static
    {
        $this->groupid = $groupid;

        return $this;
    }

    public function getNumtel(): ?string
    {
        return $this->numtel;
    }

    public function setNumtel(?string $numtel): static
    {
        $this->numtel = $numtel;

        return $this;
    }
<<<<<<< HEAD
=======

    public function getManagerId(): ?int
    {
        return $this->managerId;
    }

    public function setManagerId(?int $managerId): static
    {
        $this->managerId = $managerId;

        return $this;
    }

    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }

    public function setCreatorId(?int $creatorId): static
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    public function hasManager(): bool
    {
        return $this->managerId !== null;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {

            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {

            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CollabRequest>
     */
    public function getCollabRequestsCreated(): Collection
    {
        return $this->collabRequestsCreated;
    }

    public function addCollabRequestCreated(CollabRequest $collabRequest): static
    {
        if (!$this->collabRequestsCreated->contains($collabRequest)) {
            $this->collabRequestsCreated->add($collabRequest);
            $collabRequest->setCreator($this);
        }

        return $this;
    }

    public function removeCollabRequestCreated(CollabRequest $collabRequest): static
    {
        if ($this->collabRequestsCreated->removeElement($collabRequest)) {
            if ($collabRequest->getCreator() === $this) {
                $collabRequest->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CollabRequest>
     */
    public function getCollabRequestsRevised(): Collection
    {
        return $this->collabRequestsRevised;
    }

    public function addCollabRequestRevised(CollabRequest $collabRequest): static
    {
        if (!$this->collabRequestsRevised->contains($collabRequest)) {
            $this->collabRequestsRevised->add($collabRequest);
            $collabRequest->setRevisor($this);
        }

        return $this;
    }

    public function removeCollabRequestRevised(CollabRequest $collabRequest): static
    {
        if ($this->collabRequestsRevised->removeElement($collabRequest)) {
            if ($collabRequest->getRevisor() === $this) {
                $collabRequest->setRevisor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Collaborator>
     */
    public function getAddedCollaborators(): Collection
    {
        return $this->addedCollaborators;
    }

    public function addAddedCollaborator(Collaborator $collaborator): static
    {
        if (!$this->addedCollaborators->contains($collaborator)) {
            $this->addedCollaborators->add($collaborator);
            $collaborator->setAddedBy($this);
        }

        return $this;
    }

    public function removeAddedCollaborator(Collaborator $collaborator): static
    {
        if ($this->addedCollaborators->removeElement($collaborator)) {
            // set the owning side to null (unless already changed)
            if ($collaborator->getAddedBy() === $this) {
                $collaborator->setAddedBy(null);
            }
        }

        return $this;
    }
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): static
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts->add($contract);
            $contract->setCreator($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): static
    {
        if ($this->contracts->removeElement($contract)) {
            if ($contract->getCreator() === $this) {
                $contract->setCreator(null);
            }
        }
        return $this;
    }
>>>>>>> main
}
