<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Support;

use DevBRLucas\LaravelBaseApp\Enums\PDF\Orientation;
use Dompdf\Dompdf;

class PDF
{
    private static string $title;
    private static string $content;

    public static function generate(string $content, string $title, Orientation $orientation, array $options = []): string
    {
        $pdf = new Dompdf([
            'isRemoteEnabled' => true,
            'isJavascriptEnabled' => false,
            'dpi' => 300,
            ...$options,
        ]);
        $pdf->addInfo('title', $title);
        $pdf->setPaper('A4', $orientation->value);
        $pdf->loadHtml($content);
        $pdf->render();
        static::$content = $pdf->output();
        static::$title = $title;
        return static::$content;
    }

    public static function generateTempFile(string $content, string $title, Orientation $orientation, bool $isHTML = true, array $options = []): string
    {
        if ($isHTML) $content = static::generate($content, $title, $orientation, $options);
        static::$content = $content;
        static::$title = $title;
        $path = sys_get_temp_dir().'/'.uniqid().'.pdf';
        file_put_contents($path, $content);
        return $path;
    }

    public static function headers(): array
    {
        $file = static::$title;
        return [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "filename=$file.pdf",
            'Content-Length' => strlen(static::$content),
        ];
    }
}
