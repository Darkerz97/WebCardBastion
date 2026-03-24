<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CsvReader
{
    /**
     * @return array<int, array<string, string|null>>
     */
    public static function read(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'rb');

        if ($handle === false) {
            throw new InvalidArgumentException('No se pudo leer el archivo cargado.');
        }

        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);

            return [];
        }

        $headers = array_map(
            fn ($header) => self::normalizeHeader((string) $header),
            $headers,
        );

        if (count(array_filter($headers)) !== count($headers)) {
            fclose($handle);

            throw new InvalidArgumentException('La plantilla contiene encabezados vacios o repetidos.');
        }

        $rows = [];
        $lineNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;

            $values = array_pad($row, count($headers), null);
            $values = array_map(
                fn ($value) => is_string($value) ? trim($value) : $value,
                $values,
            );

            if (count(array_filter($values, fn ($value) => $value !== null && $value !== '')) === 0) {
                continue;
            }

            $rows[] = [
                '_row' => $lineNumber,
                ...array_combine($headers, array_slice($values, 0, count($headers))),
            ];
        }

        fclose($handle);

        return $rows;
    }

    private static function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;

        return (string) Str::of($header)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_');
    }
}
