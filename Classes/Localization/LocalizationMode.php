<?php

declare(strict_types=1);

namespace WebVision\Deepl\Base\Localization;

/**
 * Holds essential values for localization modal localization mode handling.
 */
final class LocalizationMode implements \JsonSerializable
{
    /**
     * @param string[] $before
     * @param string[] $after
     * @param int[]|null $restrictedSourceLanguageIds
     */
    public function __construct(
        public readonly string $identifier,
        public string $title,
        public string $description,
        public string $icon,
        public array $before = [],
        public array $after = [],
        public ?array $restrictedSourceLanguageIds = null,
    ) {
    }

    /**
     * @param array{
     *     identifier: string,
     *     title: string,
     *     description: string,
     *     icon: string,
     *     before: string[],
     *     after: string[],
     *     restrictedSourceLanguageIds: int[]|null
     * } $state
     *
     * @return self
     */
    public static function __set_state(array $state): self
    {
        return new self(...$state);
    }

    /**
     * @return array{
     *      identifier: string,
     *      title: string,
     *      description: string,
     *      icon: string,
     *      before: string[],
     *      after: string[],
     *      restrictedSourceLanguageIds: int[]|null,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'identifier' => $this->identifier,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'before' => $this->before,
            'after' => $this->after,
            'restrictedSourceLanguageIds' => $this->restrictedSourceLanguageIds,
        ];
    }
}
