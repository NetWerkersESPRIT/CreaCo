<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use App\Entity\Mission;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(nullable: true)]
    private ?int $groupid = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numtel = null;

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
     * @var Collection<int, Idea>
     */
    #[ORM\OneToMany(targetEntity: Idea::class, mappedBy: 'creator')]
    private Collection $ideas;

    /**
     * @var Collection<int, Idea>
     */
    #[ORM\ManyToMany(targetEntity: Idea::class, mappedBy: 'usedBy')]
    private Collection $ideasUsed;

    /**
     * @var Collection<int, Mission>
     */
    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'assignedBy')]
    private Collection $missionsCreated;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'issuedBy')]
    private Collection $tasksIssued;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'assumedBy')]
    private Collection $tasks;



    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->ideas = new ArrayCollection();
        $this->ideasUsed = new ArrayCollection();
        $this->missionsCreated = new ArrayCollection();
        $this->tasksIssued = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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
     * @return Collection<int, Idea>
     */
    public function getIdeas(): Collection
    {
        return $this->ideas;
    }

    public function addIdea(Idea $idea): static
    {
        if (!$this->ideas->contains($idea)) {
            $this->ideas->add($idea);
            $idea->setCreator($this);
        }

        return $this;
    }

    public function removeIdea(Idea $idea): static
    {
        if ($this->ideas->removeElement($idea)) {
            // set the owning side to null (unless already changed)
            if ($idea->getCreator() === $this) {
                $idea->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Idea>
     */
    public function getIdeasUsed(): Collection
    {
        return $this->ideasUsed;
    }

    public function addIdeasUsed(Idea $ideasUsed): static
    {
        if (!$this->ideasUsed->contains($ideasUsed)) {
            $this->ideasUsed->add($ideasUsed);
            $ideasUsed->addUsedBy($this);
        }

        return $this;
    }

    public function removeIdeasUsed(Idea $ideasUsed): static
    {
        if ($this->ideasUsed->removeElement($ideasUsed)) {
            $ideasUsed->removeUsedBy($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissionsCreated(): Collection
    {
        return $this->missionsCreated;
    }

    public function addMissionsCreated(Mission $missionsCreated): static
    {
        if (!$this->missionsCreated->contains($missionsCreated)) {
            $this->missionsCreated->add($missionsCreated);
            $missionsCreated->setAssignedBy($this);
        }

        return $this;
    }

    public function removeMissionsCreated(Mission $missionsCreated): static
    {
        if ($this->missionsCreated->removeElement($missionsCreated)) {
            // set the owning side to null (unless already changed)
            if ($missionsCreated->getAssignedBy() === $this) {
                $missionsCreated->setAssignedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasksIssued(): Collection
    {
        return $this->tasksIssued;
    }

    public function addTasksIssued(Task $tasksIssued): static
    {
        if (!$this->tasksIssued->contains($tasksIssued)) {
            $this->tasksIssued->add($tasksIssued);
            $tasksIssued->setIssuedBy($this);
        }

        return $this;
    }

    public function removeTasksIssued(Task $tasksIssued): static
    {
        if ($this->tasksIssued->removeElement($tasksIssued)) {
            // set the owning side to null (unless already changed)
            if ($tasksIssued->getIssuedBy() === $this) {
                $tasksIssued->setIssuedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setAssumedBy($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getAssumedBy() === $this) {
                $task->setAssumedBy(null);
            }
        }

        return $this;
    }


}
