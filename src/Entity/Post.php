<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\UserOwnedInterface;
use App\Repository\PostRepository;
use App\Controller\PostCountController;
use App\Controller\PostPublishController;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[
    ApiResource(
        normalizationContext: ['groups' =>  ['read:collection']],
        denormalizationContext: ['groups' => ['write:Post']],
        paginationClientItemsPerPage: false,
        collectionOperations: [
            'get',
            'post',
            'countPost' => [
                'method' => 'GET',
                'path' => '/posts/count',
                'controller' => PostCountController::class,
                'read' => false,
                'filters' =>  [],
                'pagination_enabled' => false,
                'openapi_context' => [
                    'summary' => "Permet de compter les articles",
                    'parameters' => [
                        ['in' => 'query',
                        'name' => 'online',
                        'schema' => [
                            'type' => 'integer',
                            'maximum' => 1,
                            'minimum' => 0
                            ],
                        'description' => 'Filtre les articles en ligne'
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Nombre de post',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'integer',
                                        'example' => 3
                                    ]
                                ]
                            ]
                        ]
                    ]
                ] 
            ] 
        ],
        itemOperations: [
            'put',
            'delete',
            'get' => [
            'normalization_context' => ['groups' => ['read:collection', 'read:item', 'read:post']]
            ],
            'publish' => [
                'method' => 'POST',
                'path' => '/posts/{id}/publish',
                'controller' => PostPublishController::class,
                'openapi_context' => [
                    'summary' => "Permet de public un article",
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object'
                                ]
                            ]
                        ]
                    ]
                ] 
            ],
            'patch'

        ]
    ),
    ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial'])
]
class Post implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:collection'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(['read:collection', 'write:Post']),
        Length(min: 5, minMessage: 'Le titre doit faire minimum {{ limit }} caractères', groups:['create:Post'])
    ]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:collection', 'write:Post'])]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:item', 'write:Post'])]
    private $content;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:item'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:item'])]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts', cascade:['persist'])]
    #[
        Groups(['read:item', 'write:Post']),
        Valid()
    ]
    private $category;

    #[ORM\Column(type: 'boolean', options:['default' => 0])]
    #[Groups(['read:collection'])]
    #[ApiProperty(openapiContext: ['type' => 'boolean', 'description' => 'Article publié et accessible par tout'])]
    private $online = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    private $user;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public static function validationGroups(self $post){
        return ['create:Post'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
