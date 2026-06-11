<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $textContent;

    #[ORM\Column(length: 255)]
    private string $miniature;

    #[ORM\Column(length: 80)]
    private string $category;

    #[ORM\Column]
    private bool $published;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'article')]
    private Collection $comments;

    /**
     * @var Collection<int, LikeArticle>
     */
    #[ORM\OneToMany(targetEntity: LikeArticle::class, mappedBy: 'article')]
    private Collection $likeArticles;

    /**
     * @var Collection<int, View>
     */
    #[ORM\OneToMany(targetEntity: View::class, mappedBy: 'article')]
    private Collection $views;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likeArticles = new ArrayCollection();
        $this->views = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTextContent(): string
    {
        return $this->textContent;
    }

    public function setTextContent(string $textContent): static
    {
        $this->textContent = $textContent;

        return $this;
    }

    public function getMiniature(): string
    {
        return $this->miniature;
    }

    public function setMiniature(string $miniature): static
    {
        $this->miniature = $miniature;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

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
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
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
            $likeArticle->setArticle($this);
        }

        return $this;
    }

    public function removeLikeArticle(LikeArticle $likeArticle): static
    {
        $this->likeArticles->removeElement($likeArticle);

        return $this;
    }

    public function isLikedByUser(?User $user): bool
    {
        if (null === $user) {
            return false;
        }

        foreach ($this->likeArticles as $like) {
            if ($like->getUser() === $user) {
                return true;
            }
        }

        return false;
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
            $view->setArticle($this);
        }

        return $this;
    }

    public function removeView(View $view): static
    {
        if ($this->views->removeElement($view)) {
            // set the owning side to null (unless already changed)
            if ($view->getArticle() === $this) {
                $view->setArticle(null);
            }
        }

        return $this;
    }
}
