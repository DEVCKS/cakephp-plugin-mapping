<?php

namespace Mapping\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $label
 * @property string $code
 */
class Cpv extends Entity
{
    protected $_accessible = [];

    protected $_hidden = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
