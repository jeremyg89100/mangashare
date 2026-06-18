<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private string $pseudo;

    #[ORM\Column(length: 255)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $birthDate = null;

    /**
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, Manga>
     */
    #[ORM\OneToMany(targetEntity: Manga::class, cascade: ['remove'], mappedBy: 'user')]
    private Collection $mangas;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, cascade: ['remove'], mappedBy: 'user')]
    private Collection $articles;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, cascade: ['remove'], mappedBy: 'user')]
    private Collection $comments;

    /**
     * @var Collection<int, Follow>
     */
    #[ORM\OneToMany(targetEntity: Follow::class, cascade: ['remove'], mappedBy: 'follower')]
    private Collection $followsAsFollower;

    /**
     * @var Collection<int, Follow>
     */
    #[ORM\OneToMany(targetEntity: Follow::class, cascade: ['remove'], mappedBy: 'following')]
    private Collection $followsAsFollowing;

    /**
     * @var Collection<int, Reporting>
     */
    #[ORM\OneToMany(targetEntity: Reporting::class, cascade: ['remove'], mappedBy: 'author', orphanRemoval: true)]
    private Collection $reportingsSent;

    /**
     * @var Collection<int, Reporting>
     */
    #[ORM\OneToMany(targetEntity: Reporting::class, cascade: ['remove'], mappedBy: 'target', orphanRemoval: true)]
    private Collection $reportingsReceived;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, cascade: ['remove'], mappedBy: 'user')]
    private Collection $notifications;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, cascade: ['remove'], mappedBy: 'user', orphanRemoval: true)]
    private Collection $likes;

    /**
     * @var Collection<int, LikeArticle>
     */
    #[ORM\OneToMany(targetEntity: LikeArticle::class, cascade: ['remove'], mappedBy: 'user')]
    private Collection $likeArticles;

    /**
     * @var Collection<int, View>
     */
    #[ORM\OneToMany(targetEntity: View::class, cascade: ['remove'], mappedBy: 'user')]
    private Collection $views;

    #[ORM\Column]
    private bool $enabled = true;

    #[ORM\Column]
    private bool $isReported = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reportReason = null;

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
        $this->likes = new ArrayCollection();
        $this->likeArticles = new ArrayCollection();
        $this->views = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
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

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeImmutable $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
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
        $this->mangas->removeElement($manga);

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
        $this->articles->removeElement($article);

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
        $this->comments->removeElement($comment);

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
        $this->followsAsFollower->removeElement($follow);

        return $this;
    }

    /**
     * @return Collection<int, Follow>
     */
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
        $this->followsAsFollowing->removeElement($follow);

        return $this;
    }

    public function isFollowedBy(?self $currentUser): bool
    {
        if (null === $currentUser) {
            return false;
        }

        foreach ($this->followsAsFollowing as $follow) {
            if ($follow->getFollower() === $currentUser) {
                return true;
            }
        }

        return false;
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
        $this->reportingsSent->removeElement($reporting);

        return $this;
    }

    /**
     * @return Collection<int, Reporting>
     */
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
        $this->reportingsReceived->removeElement($reporting);

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
        $this->notifications->removeElement($notification);

        return $this;
    }

    public function getUserIdentifier(): string
    {
        \assert('' !== $this->email);

        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires, efface-les ici
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getUser() === $this) {
                $this->likes->removeElement($like);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LikeArticle>
     */
    public function getLikeArticles(): Collection
    {
        return $this->likeArticles;
    }

    public function addLikeArticle(LikeArticle $likeArticle): static
    {
        if (!$this->likeArticles->contains($likeArticle)) {
            $this->likeArticles->add($likeArticle);
            $likeArticle->setUser($this);
        }

        return $this;
    }

    public function removeLikeArticle(LikeArticle $likeArticle): static
    {
        $this->likeArticles->removeElement($likeArticle);

        return $this;
    }

    /**
     * @return Collection<int, View>
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(View $view): static
    {
        if (!$this->views->contains($view)) {
            $this->views->add($view);
            $view->setUser($this);
        }

        return $this;
    }

    public function removeView(View $view): static
    {
        // user est non-nullable côté View : on se contente de détacher la vue
        // de la collection (cf. removeLikeArticle, même contrainte).
        $this->views->removeElement($view);

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isReported(): bool
    {
        return $this->isReported;
    }

    public function setIsReported(bool $isReported): static
    {
        $this->isReported = $isReported;

        return $this;
    }

    public function getReportReason(): ?string
    {
        return $this->reportReason;
    }

    public function setReportReason(?string $reportReason): static
    {
        $this->reportReason = $reportReason;

        return $this;
    }
}
