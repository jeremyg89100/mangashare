<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $birthDate = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    /**
     * @var Collection<int, Manga>
     */
    #[ORM\OneToMany(targetEntity: Manga::class, mappedBy: 'user')]
    private Collection $mangas;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'user')]
    private Collection $articles;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user')]
    private Collection $comments;

    /**
     * @var Collection<int, Follow>
     */
    #[ORM\OneToMany(targetEntity: Follow::class, mappedBy: 'follower')]
    private Collection $followsAsFollower;

    /**
     * @var Collection<int, Follow>
     */

    #[ORM\OneToMany(targetEntity: Follow::class, mappedBy: 'following')]
    private Collection $followsAsFollowing;

    /**
     * @var Collection<int, Reporting>
     */
    #[ORM\OneToMany(targetEntity: Reporting::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $reportingsSent;

    /**
     * @var Collection<int, Reporting>
     */
    #[ORM\OneToMany(targetEntity: Reporting::class, mappedBy: 'target', orphanRemoval: true)]
    private Collection $reportingsReceived;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    public function __construct()
    {
        $this->mangas = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->followsAsFollower = new ArrayCollection();
        $this->followsAsFollowing = new ArrayCollection();
        $this->reportingsSent = new ArrayCollection();
        $this->reportingsReceived = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTime $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Manga>
     */
    public function getMangas(): Collection
    {
        return $this->mangas;
    }

    public function addManga(Manga $manga): static
    {
        if (!$this->mangas->contains($manga)) {
            $this->mangas->add($manga);
            $manga->setUser($this);
        }

        return $this;
    }

    public function removeManga(Manga $manga): static
    {
        if ($this->mangas->removeElement($manga)) {
            // set the owning side to null (unless already changed)
            if ($manga->getUser() === $this) {
                $manga->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
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
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Follow>
     */
    public function getFollowsAsFollower(): Collection
    {
        return $this->followsAsFollower;
    }

    public function addFollowsAsFollower(Follow $follow): static
    {
        if (!$this->followsAsFollower->contains($follow)) {
            $this->followsAsFollower->add($follow);
            $follow->setFollower($this);
        }
        return $this;
    }

    public function removeFollowsAsFollower(Follow $follow): static
    {
        if ($this->followsAsFollower->removeElement($follow)) {
            if ($follow->getFollower() === $this) {
                $follow->setFollower(null);
            }
        }
        return $this;
    }

    public function getFollowsAsFollowing(): Collection
    {
        return $this->followsAsFollowing;
    }

    public function addFollowsAsFollowing(Follow $follow): static
    {
        if (!$this->followsAsFollowing->contains($follow)) {
            $this->followsAsFollowing->add($follow);
            $follow->setFollowing($this);
        }
        return $this;
    }

    public function removeFollowsAsFollowing(Follow $follow): static
    {
        if ($this->followsAsFollowing->removeElement($follow)) {
            if ($follow->getFollowing() === $this) {
                $follow->setFollowing(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Reporting>
     */
    public function getReportingsSent(): Collection
    {
        return $this->reportingsSent;
    }

    public function addReportingsSent(Reporting $reporting): static
    {
        if (!$this->reportingsSent->contains($reporting)) {
            $this->reportingsSent->add($reporting);
            $reporting->setAuthor($this);
        }
        return $this;
    }

    public function removeReportingsSent(Reporting $reporting): static
    {
        if ($this->reportingsSent->removeElement($reporting)) {
            if ($reporting->getAuthor() === $this) {
                $reporting->setAuthor(null);
            }
        }
        return $this;
    }

    public function getReportingsReceived(): Collection
    {
        return $this->reportingsReceived;
    }

    public function addReportingsReceived(Reporting $reporting): static
    {
        if (!$this->reportingsReceived->contains($reporting)) {
            $this->reportingsReceived->add($reporting);
            $reporting->setTarget($this);
        }
        return $this;
    }

    public function removeReportingsReceived(Reporting $reporting): static
    {
        if ($this->reportingsReceived->removeElement($reporting)) {
            if ($reporting->getTarget() === $this) {
                $reporting->setTarget(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires, efface-les ici
    }
}
