<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AreaRepository;

#[ORM\Entity(repositoryClass: AreaRepository::class)]
#[ORM\Table(name: "area", options: ["comment" => "省市区表"])]
class Area
{
    public static function getLevels()
    {
        return [
            1 => '一级',
            2 => '二级',
            3 => '三级'
        ];
    }

    #[ORM\Column(name: "id", type: "string", length: 10, options: ["comment" => "省市区编号"])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private string $id;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Area")]
    #[ORM\JoinColumn(name: "parent_id", referencedColumnName: "id")]
    private ?self $parent = null;

    #[ORM\Column(name: "name", type: "string", length: 45, options: ["comment" => "区域名称"])]
    private ?string $name = null;

    #[ORM\Column(name: "first_letter", type: "string", nullable: true, length: 1, options: ["fixed" => true, "comment" => "区域名称首字母"])]
    private ?string $firstLetter = null;

    #[ORM\Column(name: "level", type: "smallint", options: ["default" => 0, "comment" => "数据层级"])]
    private int $level = 0;


    public function getId(): ?string
    {
        return $this->id;
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

    public function getFirstLetter(): ?string
    {
        return $this->firstLetter;
    }

    public function setFirstLetter(?string $firstLetter): self
    {
        $this->firstLetter = $firstLetter;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
