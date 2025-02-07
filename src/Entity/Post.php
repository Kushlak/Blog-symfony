<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\CategoryType;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\PostKeyValueStore;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['post:read', 'post:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['post:read', 'post:write'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['post:read', 'post:write'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(['post:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'string', enumType: CategoryType::class)]
    #[Groups(['post:read', 'post:write'])]
    private ?CategoryType $type = null;

    #[ORM\OneToMany(targetEntity: PostKeyValueStore::class, mappedBy: 'post', orphanRemoval: true)]
    #[Groups(['post:read'])]
    private Collection $postKeyValueStores;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post:read'])]
    private ?User $author = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    #[Groups(['post:read'])]
    private Collection $comments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->postKeyValueStores = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getType(): ?CategoryType
    {
        return $this->type;
    }

    public function setType(CategoryType $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
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
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PostKeyValueStore>
     */
    public function getPostKeyValueStores(): Collection
    {
        return $this->postKeyValueStores;
    }

    public function addPostKeyValueStore(PostKeyValueStore $postKeyValueStore): static
    {
        if (!$this->postKeyValueStores->contains($postKeyValueStore)) {
            $this->postKeyValueStores->add($postKeyValueStore);
            $postKeyValueStore->setPost($this);
        }

        return $this;
    }

    public function removePostKeyValueStore(PostKeyValueStore $postKeyValueStore): static
    {
        if ($this->postKeyValueStores->removeElement($postKeyValueStore)) {
            // set the owning side to null (unless already changed)
            if ($postKeyValueStore->getPost() === $this) {
                $postKeyValueStore->setPost(null);
            }
        }

        return $this;
    }
}
