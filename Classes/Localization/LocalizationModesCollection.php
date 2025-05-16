<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Localization;

use TYPO3\CMS\Core\Service\DependencyOrderingService;

/**
 * @implements \Iterator<string, LocalizationMode>
 */
final class LocalizationModesCollection implements \JsonSerializable, \Countable, \Iterator
{
    /**
     * @var array<string, LocalizationMode>
     */
    private array $modes = [];

    private int $position = 0;

    /**
     * @param array{modes: array<string, LocalizationMode>} $state
     * @return self
     */
    public static function __set_state(array $state): self
    {
        $instance = new self();
        $instance->set(...array_values($state['modes']));
        return $instance;
    }

    public function add(LocalizationMode ...$modes): void
    {
        foreach ($modes as $mode) {
            $this->modes[$mode->identifier] = $mode;
        }
        $this->sort();
    }

    public function count(): int
    {
        return count($this->modes);
    }

    public function hasIdentifier(string $identifier): bool
    {
        return ($this->modes[$identifier] ?? null) instanceof LocalizationMode;
    }

    public function getIdentifier(string $identifier): ?LocalizationMode
    {
        return $this->hasIdentifier($identifier)
            ? $this->modes[$identifier]
            : null;
    }

    public function set(LocalizationMode ...$modes): void
    {
        $this->modes = [];
        $this->add(...$modes);
        $this->sort();
    }

    public function remove(LocalizationMode $mode): void
    {
        unset($this->modes[$mode->identifier]);
        $this->sort();
    }

    /**
     * @return LocalizationMode[]
     */
    public function modes(): array
    {
        return array_values($this->modes);
    }

    /**
     * @return LocalizationMode[]
     */
    public function jsonSerialize(): array
    {
        return $this->modes();
    }

    private function sort(): void
    {
        $this->modes = array_map(
            static fn ($value) => $value['mode'],
            (new DependencyOrderingService())->orderByDependencies(
                array_map(
                    static fn (LocalizationMode $mode) => ['before' => $mode->before, 'after' => $mode->after, 'mode' => $mode],
                    $this->modes
                ),
            ),
        );
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function key(): string
    {
        return array_keys($this->modes)[$this->position];
    }

    public function current(): LocalizationMode
    {
        return $this->modes[$this->key()];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function valid(): bool
    {
        return isset(array_keys($this->modes)[$this->position])
            && ($this->modes[array_keys($this->modes)[$this->position]] ?? null) instanceof LocalizationMode;
    }
}
