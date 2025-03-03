<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Support;

use Shuchkin\SimpleXLSXGen;

class XLSX extends SimpleXLSXGen
{
    public function __construct(private readonly string $filename)
    {
        parent::__construct();
        $this->setTitle($filename);
    }

    public static function generate(string $filename, array $rows, ?array $header = null): static
    {
        $sheetRows = [
            ...($header ? [$header] : []),
            ...$rows,
        ];
        $xlsx = new static($filename);
        $xlsx->addSheet($sheetRows);
        return $xlsx;
    }

    public static function generateTempFile(string $filename, array $rows, ?array $header = null): string
    {
        $xlsx = static::generate($filename, $rows, $header);
        $path = sys_get_temp_dir().'/'.uniqid().'.xlsx';
        file_put_contents($path, $xlsx->getContent());
        return $path;
    }

    public function generateTempFileFromSheets(): string
    {
        $path = sys_get_temp_dir().'/'.uniqid().'.xlsx';
        file_put_contents($path, $this->getContent());
        return $path;
    }

    public function getContent(): string
    {
        return (string) $this;
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "filename={$this->filename}.xlsx",
            'Content-Length' => strlen($this->getContent()),
        ];
    }
}
