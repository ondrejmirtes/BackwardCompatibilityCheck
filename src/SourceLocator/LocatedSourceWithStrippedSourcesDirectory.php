<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\SourceLocator;

use PHPStan\BetterReflection\SourceLocator\Located\LocatedSource;

use function str_starts_with;
use function strlen;
use function substr_replace;

/** @internal */
final class LocatedSourceWithStrippedSourcesDirectory extends LocatedSource
{
    public function __construct(
        private LocatedSource $next,
        private string $sourcesDirectory,
    ) {
    }

    public function getSource(): string
    {
        return $this->next->getSource();
    }

    public function getName(): string|null
    {
        return $this->next->getName();
    }

    public function getFileName(): string|null
    {
        $fileName = $this->next->getFileName();

        if ($fileName === null || ! str_starts_with($fileName, $this->sourcesDirectory)) {
            return $fileName;
        }

        return substr_replace($fileName, '', 0, strlen($this->sourcesDirectory));
    }

    public function isInternal(): bool
    {
        return $this->next->isInternal();
    }

    public function getExtensionName(): string|null
    {
        return $this->next->getExtensionName();
    }

    public function isEvaled(): bool
    {
        return $this->next->isEvaled();
    }

    public function getAliasName(): string|null
    {
        return $this->next->getAliasName();
    }
}
