<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnnonceRepository")
 */
class Annonce
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $entete;

    /**
     * @ORM\Column(type="text", length=255)
     * @Assert\NotBlank()
     */
    private $corps;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiredAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Rubrique", inversedBy="annonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rubrique;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="annonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="annonce", orphanRemoval=true, cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visites;

    /**
     * Annonce constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEntete(): ?string
    {
        return $this->entete;
    }

    /**
     * @param string $entete
     * @return $this
     */
    public function setEntete(string $entete): self
    {
        $this->entete = $entete;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCorps(): ?string
    {
        return $this->corps;
    }

    /**
     * @param string $corps
     * @return $this
     */
    public function setCorps(string $corps): self
    {
        $this->corps = $corps;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpiredAt(): ?DateTimeInterface
    {
        return $this->expiredAt;
    }

    /**
     * @param DateTimeInterface $expiredAt
     * @return $this
     */
    public function setExpiredAt(DateTimeInterface $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * @return Rubrique|null
     */
    public function getRubrique(): ?Rubrique
    {
        return $this->rubrique;
    }

    /**
     * @param Rubrique|null $rubrique
     * @return $this
     */
    public function setRubrique(?Rubrique $rubrique): self
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     * @return $this
     */
    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @param Image $image
     * @return $this
     */
    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAnnonce($this);
        }

        return $this;
    }

    /**
     * @param Image $image
     * @return $this
     */
    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAnnonce() === $this) {
                $image->setAnnonce(null);
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getVisites(): ?int
    {
        return $this->visites;
    }

    /**
     * @param int|null $visites
     * @return $this
     */
    public function setVisites(?int $visites): self
    {
        $this->visites = $visites;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->entete;
    }
}
