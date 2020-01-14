<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TreeRepository")
 */
class Tree
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\TreeInformation", inversedBy="trees")
     */
    private $informations;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TreePicture", mappedBy="tree")
     */
    private $pictures;

    public function __construct()
    {
        $this->informations = new ArrayCollection();
        $this->setCreatedAt(new DateTime());
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|TreeInformation[]
     */
    public function getInformations(): Collection
    {
        return $this->informations;
    }

    public function addInformation(TreeInformation $information): self
    {
        if (!$this->informations->contains($information)) {
            $this->informations[] = $information;
        }

        return $this;
    }

    public function removeInformation(TreeInformation $information): self
    {
        if ($this->informations->contains($information)) {
            $this->informations->removeElement($information);
        }

        return $this;
    }

    public function clearInformations(): self
    {
        $this->informations = new ArrayCollection();
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|TreePicture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(TreePicture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setTree($this);
        }

        return $this;
    }

    public function removePicture(TreePicture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getTree() === $this) {
                $picture->setTree(null);
            }
        }

        return $this;
    }

}
