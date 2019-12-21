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

    public function __construct()
    {
        $this->informations = new ArrayCollection();
        $this->setCreatedAt(new DateTime());
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
}
