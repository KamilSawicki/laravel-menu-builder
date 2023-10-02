<?php

namespace KamilSawicki\LaravelMenuBuilder;

use Illuminate\Support\Collection;

class Item
{
    private array $options = [];
    private bool $toRender = true;
    private null|string $class = null;

    private Collection $children;

    private Collection $attr;

    protected function __construct(
        private string          $path,
        private readonly string $label,
        private readonly bool   $external
    ) {
        $this->children = new Collection();
        $this->attr = new Collection();
    }

    public static function new(string $label, string $path = '#', bool $external = false): self {
        return new self($external || $path == '#' ? $path : route($path), $label, $external);
    }

    public function if(bool $expression): self {
        return $this->setToRender($expression);
    }

    public function getViewData(): array {
        return array_merge([
            'url' => $this->path,
            'label' => $this->label,
            'external' => $this->external,
            'class' => $this->class,
        ],
            $this->options);
    }

    public function getOptions(): array {
        return $this->options;
    }

    public function isToRender(): bool {
        return $this->toRender;
    }

    public function getClass(): ?string {
        return $this->class;
    }

    public function getChildren(): Collection {
        return $this->children;
    }

    public function hasChildren(): bool {
        return !$this->children->isEmpty();
    }

    public function getAttr(): Collection {
        return $this->attr;
    }

    public function setOptions(array $options): self {
        $this->options = $options;

        return $this;
    }

    public function setToRender(bool $toRender): self {
        $this->toRender = $toRender;

        return $this;
    }

    public function setClass(?string $class): self {
        $this->class = $class;

        return $this;
    }

    public function setChildren(Collection $children): self {
        $this->children = $children;

        return $this;
    }

    public function addChildren(self $item): self {
        $this->path = '#';
        $this->children[] = $item;

        return $this;
    }

    public function setAttr(Collection|array $attr): self {
        $this->attr = $attr instanceof Collection ? $attr : new Collection($attr);

        return $this;
    }
}
