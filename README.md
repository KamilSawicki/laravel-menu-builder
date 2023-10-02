# Laravel Menu Builder
## About
Simple Laravel menu builder for Laravel 10 applications.
## Requirements
PHP >= 8.2, Tidy extension.
## How to use
### Installation
Add to your providers list in app.php `\KamilSawicki\LaravelMenuBuilder\Providers\LaravelMenuBuilderProvider::class,`.
### Usage
Just extend `AbstractMenuBuilder` and override `build` and `identifier` methods.
### AbstractMenuBuilder class
#### build(): void
Contains definition of menu items. You can add new object with Item::class using self::add() method.
#### identifier(): string
Should return string, that is used (by default) as id of root element and cache key.
#### string $itemTemplate
Path to blade item template used on first level of menu.
#### string $childTemplate
Path to blade item template used on another level of menu. If null, used `$itemTemplate`.
#### string $wrapperTemplate
Path to blade template, that wrapping whole items.
#### string $childWrapperTemplate
Path to blade template, that wrapping whole sub-levels items. If null, used `$wrapperTemplate`.
### Item class
#### new(string $label, string $path = '#', bool $external = false): self
Static constructor for class. Accepts arguments `label` (required), `path` (optional, default `#`) - route name or URL if `external` (optional, default `false`) is `true`.
#### if(bool $expression): self (shortcut) and setToRender(bool $toRender): self
Accept boolean expression define that item should not be filtered on rendering.
#### setClass(string $class): self
HTML class on `<a>` element.
#### addChildren(Item $child): self and setChildren(array<Item>|Collection<Item> $children): self
Add Item::class to sub-level of tree menu. `addChildren`
#### setAttr(array<string, string> $attr): self
List of custom attributes on `<a>` element. Rendered as `key="value"`
#### setOptions(array<string, string> $options): self
Custom variables passed to view, that can be used in custom templates.
## Example
```php
class ExampleMenuBuilder extends AbstractMenuBuilder
{
    protected function build(): void {
        $this->add(Item::new('Item 1', 'page_one'));
        $this->add(Item::new('Item 2', 'page_two'));
        $this->add(Item::new('Folder')
            ->addChildren(Item::new('SubItem 1', 'folder.sub_page_one'))
            ->addChildren(Item::new('SubItem 2', 'folder.sub_page_two'))
        );
        $this->add(Item::new('Hidden')->if(1 == 0));
        $this->add(Item::new('Red class')->setClass('red'));
        $this->add(Item::new('Custom view var')->setOptions([
            'custom-var' => 'test',
        ]));
        $this->add(Item::new('Custom attribute')
            ->setAttr(['custom-attr' => 'custom value'])
        );
    }

    protected function identifier(): string {
        return 'example_menu';
    }
}
```
will be rendered as 
```html
<ul id="example_menu">
    <li>
        <a href="http://localhost/page_1">Item 1</a>
    </li>
    <li>
        <a href="http://localhost/page_2">Item 2</a>
    </li>
    <li>
        <a href="#">Folder</a>
        <ul>
            <li>
                <a href="http://localhost/folder/sub_page_1">SubItem 1</a>
            </li>
            <li>
                <a href="http://localhost/folder/sub_page_2">SubItem 2</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="#" class="red">Red class</a>
    </li>
    <li>
        <a href="#">Custom view var</a>
    </li>
    <li>
        <a href="#" custom-attr="custom value">Custom attribute</a>
    </li>
</ul>
```
