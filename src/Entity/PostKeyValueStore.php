<?php


namespace App\Entity;

use App\Repository\PostKeyValueStoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostKeyValueStoreRepository::class)]
class PostKeyValueStore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'keyValueStores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[ORM\Column]
    private ?string $key = null;

    #[ORM\Column(type: 'json')]
    private ?array $value = null;

    // Constructor
    public function __construct()
    {
        // Initialize any collections or default values if needed
    }

    // Getter and Setter for Id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and Setter for Post
    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;
        return $this;
    }

    // Getter and Setter for Key
    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    // Getter and Setter for Value
    public function getValue(): ?array
    {
        return $this->value;
    }

    public function setValue(array $value): self
    {
        $this->value = $value;
        return $this;
    }
}

