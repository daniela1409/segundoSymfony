<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=PostsRepository::class)
 */
class Posts
{
    const REGISTRO_EXITOSO = 'success';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $likes;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $foto;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_publicacion;

    /**
     * @ORM\Column(type="string", length=80000)
     */
    private $contenido;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comentarios", mappedBy="post")
     */
    private $comentario;
    
    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="post")
    */
    private $user;

    public function __construct()
    {
        $this->likes = '';
        $this->fecha_publicacion = new \DateTime();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getLikes(): ?string
    {
        return $this->likes;
    }

    public function setLikes(string $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getFechaPublicacion(): ?\DateTimeInterface
    {
        return $this->fecha_publicacion;
    }

    public function setFechaPublicacion(\DateTimeInterface $fecha_publicacion): self
    {
        $this->fecha_publicacion = $fecha_publicacion;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): self
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * 
     * @return Collection|Comentarios[]
     */
    public function getComentario() : Collection
    {
        return $this->comentario;
    }

   /**
    * @param mixed $comentario
    */
    public function setComentario($comentario):void
    {
        $this->user = $comentario;
    }
    
   /**
     * 
     * @return mixed
     */
    public function getUser() : ?User
    {
        return $this->user;
    }

   /**
    * @param mixed $user
    */
    public function setUser($user):void
    {
        $this->user = $user;
    }
}
