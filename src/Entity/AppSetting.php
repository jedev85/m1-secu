<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AppSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 100)]
    public string $name = '';

    #[ORM\Column(type: 'text')]
    public string $value = '';

    #[ORM\Column(name: 'is_sensitive')]
    public bool $sensitive = false;
}
