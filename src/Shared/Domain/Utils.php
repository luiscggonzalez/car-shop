<?php

declare(strict_types=1);

namespace CarShop\Shared\Domain;

use DateTimeImmutable;
use DateTimeInterface;
use ReflectionClass;
use function Lambdish\Phunctional\filter;

final readonly class Utils
{
    public static function endsWith(string $needle, string $haystack): bool
    {
        $length = strlen($needle);
        if ($length === 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    public static function dateToString(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');//php7.2DateTimeInterface::ATOM
    }

    public static function stringToDate(string $date): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($date);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(sprintf('The date string "%s" is not valid', $date), 0, $e);
        }
    }

    /**
     * @param array<string, mixed> $values
     * @return string
     * @throws \RuntimeException
     */
    public static function jsonEncode(array $values): string
    {
        try {
            return json_encode($values, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Unable to encode data to JSON: ' . json_last_error_msg());

        }
    }

    /**
     * @param string $json
     * @return array<string, mixed>
     * @throws \RuntimeException
     */
    public static function jsonDecode(string $json): array
    {
        try {
            /** @var array<string, mixed> json_decode() */
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Unable to parse response body into JSON: ' . $e->getMessage());
        }
    }

    public static function toSnakeCase(string $text): string
    {
        return ctype_lower($text) ? $text : strtolower((string) preg_replace('/([^A-Z\s])([A-Z])/', "$1_$2", $text));
    }

    public static function toCamelCase(string $text): string
    {
        return lcfirst(str_replace('_', '', ucwords($text, '_')));
    }

    /**
     * @param array<string, mixed> $array
     * @param string $prepend
     * @return array<string, mixed>
     */
    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                /** @var array<string, mixed> $value */
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * @param string $path
     * @param string $fileType
     * @return array<int, string>
     * @throws \RuntimeException
     */
    public static function filesIn(string $path, string $fileType): array
    {
        $files = scandir($path);

        if (false === $files) {
            throw new \RuntimeException("Failed to scan directory: $path");
        }

        /** @var array<int, string> $filteredFiles */
        $filteredFiles = array_values(
            filter(
                static fn(string $possibleModule): bool => str_contains($possibleModule, $fileType),
                $files
            )
        );

        return $filteredFiles;
    }

    public static function extractClassName(object|string $object): string
    {
        if (is_string($object) && !class_exists($object)) {
            throw new \InvalidArgumentException(sprintf('The string "%s" is not a valid class name', $object));
        }

        $reflect = new ReflectionClass($object);

        return $reflect->getShortName();
    }
}
