<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Support;

use Shuchkin\SimpleXLSXGen;

class XLSX
{
    private static string $filename;
    private static string $content;

    public static function generate(string $filename, array $header, array $data): string
    {
        $rows = [
            $header,
            ...$data,
        ];
        $xlsxContent = (string) SimpleXLSXGen::fromArray($rows)->setTitle($filename);
        static::$content = $xlsxContent;
        static::$filename = $filename;
        return static::$content;
    }

    public static function generateTempFile(string $filename, array $header, array $data): string
    {
        $content = static::generate($filename, $header, $data);
        static::$content = $content;
        static::$filename = $filename;
        $path = sys_get_temp_dir().'/'.uniqid().'.xlsx';
        file_put_contents($path, $content);
        return $path;
    }

    public static function headers(): array
    {
        $file = static::$filename;
        $content = static::$content;
        static::$filename = static::$content = '';
        return [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "filename=$file.pdf",
            'Content-Length' => strlen($content),
        ];
    }
}
