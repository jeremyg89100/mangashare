<?php

namespace App\Entity;

use App\Repository\MangaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaRepository::class)]
class Manga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $synopsis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $miniature = null;

    #[ORM\Column(length: 10)]
    private string $readingDirection;

    #[ORM\Column(length: 20)]
    private string $status;

    #[ORM\Column]
    private int $views;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'mangas')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /**
     * @var Collection<int, Chapter>
     */
    #[ORM\OneToMany(targetEntity: Chapter::class, mappedBy: 'manga', orphanRemoval: true, cascade: ['persist'])]
    private Collection $chapters;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'manga', orphanRemoval: true, cascade: ['remove'])]
    private Collection $comments;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'manga', orphanRemoval: true, cascade: ['remove'])]
    private Collection $likes;

    /**
     * @var array<int, string>
     */
    #[ORM\Column(length: 80)]
    private array $categories = [];

    /**
     * @var Collection<int, View>
     */
    #[ORM\OneToMany(targetEntity: View::class, mappedBy: 'manga')]
    private Collection $mangaViews;

    public function __construct()
    {
        $this->chapters = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->mangaViews = new ArrayCollection();
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

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): static
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getMiniature(): ?string
    {
        return $this->miniature;
    }

    public function setMiniature(?string $miniature): static
    {
        $this->miniature = $miniature;

        return $this;
    }

    public function getReadingDirection(): string
    {
        return $this->readingDirection;
    }

    public function setReadingDirection(string $readingDirection): static
    {
        $this->readingDirection = $readingDirection;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): static
    {
        $this->views = $views;

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
     * @return Collection<int, Chapter>
     */
    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    public function addChapter(Chapter $chapter): static
    {
        if (!$this->chapters->contains($chapter)) {
            $this->chapters->add($chapter);
            $chapter->setManga($this);
        }

        return $this;
    }

    public function removeChapter(Chapter $chapter): static
    {
        $this->chapters->removeElement($chapter);

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
            $comment->setManga($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getManga() === $this) {
                $comment->setManga(null);
            }
        }

        return $this;
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
            $like->setManga($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        $this->likes->removeElement($like);

        return $this;
    }

    public function isLikedByUser(?User $user): bool
    {
        if (null === $user) {
            return false;
        }

        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array<int, string> $categories
     */
    public function setCategories(array $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection<int, View>
     */
    public function getMangaViews(): Collection
    {
        return $this->mangaViews;
    }

    public function addMangaView(View $mangaView): static
    {
        if (!$this->mangaViews->contains($mangaView)) {
            $this->mangaViews->add($mangaView);
            $mangaView->setManga($this);
        }

        return $this;
    }

    public function removeMangaView(View $mangaView): static
    {
        if ($this->mangaViews->removeElement($mangaView)) {
            // set the owning side to null (unless already changed)
            if ($mangaView->getManga() === $this) {
                $mangaView->setManga(null);
            }
        }

        return $this;
    }
}
