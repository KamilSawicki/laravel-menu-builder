<?php

namespace KamilSawicki\LaravelMenuBuilder;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

abstract class AbstractMenuBuilder
{
    protected Collection $items;
    protected string $itemTemplate = 'lmb::item';
    protected string $wrapperTemplate = 'lmb::wrapper';
    protected null|string $childTemplate = null;
    protected null|string $childWrapperTemplate = null;
    protected int $cacheLifeTime = 0;

    public function __construct() {
        $this->items = new Collection();
    }

    abstract protected function build(): void;

    abstract protected function identifier(): string;

    protected function add(Item $item): void {
        $this->items->add($item);
    }

    public function render(): string {
        return Cache::remember(
            implode('_', [$this->identifier(), Auth::user() ? Auth::user()->id : 'unauthorized']),
            Carbon::now()->addSecond($this->cacheLifeTime),
            function () {
                $this->build();

                return $this->renderLevel($this->items);
            }
        );
    }

    private function renderItem(Item $item, int $level = 0): string {
        return view(
            $this->childTemplate && $level > 0 ? $this->childTemplate : $this->itemTemplate,
            array_merge(
                $item->getViewData(),
                [
                    'hasChild' => $item->hasChildren(),
                    'childInnerHtml' => $this->renderLevel($item->getChildren(), $level+1),
                    'attr' => $item->getAttr()
                        ->map(fn ($val, $key) => sprintf('%s="%s"', $key, $val))
                        ->implode(' '),
                ]
            )
        )->render();
    }

    private function renderLevel(Collection $items, $level = 0): string {
        $innerHtml = $items
            ->filter(fn(Item $item) => $item->isToRender())
            ->map(fn(Item $item) => $this->renderItem($item, $level))
            ->implode(PHP_EOL);

        $template = $level > 0 && $this->childWrapperTemplate
            ? $this->childWrapperTemplate
            : $this->wrapperTemplate;

        $viewData = ['innerHtml' => $innerHtml];

        if ($level === 0) {
            $viewData['identifier'] = $this->identifier();
        }

        $html = view($template, $viewData)->render();

        $tidy = new \tidy();
        $tidy->parseString($html, [
            'indent' => true,
            'output-xhtml' => true,
            'wrap' => 200,
            'show-body-only' => true
        ]);
        $tidy->cleanRepair();

        return (string) $tidy;
    }
}
