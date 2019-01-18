<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 */
class Item implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CheckList", inversedBy="items")
     */
    private $checkList;

    /**
     * @ORM\Column(type="boolean")
     */
    private $checked;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Attachment", cascade={"persist", "remove"})
     */
    private $attachment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckList(): ?CheckList
    {
        return $this->checkList;
    }

    public function setCheckList(?CheckList $checkList): self
    {
        $this->checkList = $checkList;

        return $this;
    }

    public function getChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): self
    {
        $this->checked = $checked;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'checked' => $this->getChecked(),
            'attachment' => $this->getAttachment()
        ];
    }

    public function getAttachment(): ?Attachment
    {
        return $this->attachment;
    }

    public function setAttachment(?Attachment $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }
}
