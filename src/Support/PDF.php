<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Support;

use DevBRLucas\LaravelBaseApp\Enums\PDF\Disposition;
use DevBRLucas\LaravelBaseApp\Enums\PDF\Orientation;
use Dompdf\Dompdf;

class PDF extends Dompdf
{
    private readonly string $title;

    public function getContent(): string
    {
        return $this->output();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public static function generate(string $content, string $title, Orientation $orientation = Orientation::PORTRAIT, string|array $size = 'A4', array $options = []): static
    {
        $pdf = new static([
            'isRemoteEnabled' => true,
            'isJavascriptEnabled' => false,
            'dpi' => 300,
            ...$options,
        ]);
        $pdf->addInfo('title', $title);
        $pdf->setPaper($size, $orientation->value);
        $pdf->loadHtml($content);
        $pdf->render();
        $pdf->title = $title;
        return $pdf;
    }

    public static function generateTempFile(string $content, string $title, Orientation $orientation = Orientation::PORTRAIT, bool $isHTML = true, string|array $size = 'A4', array $options = []): string
    {
        if ($isHTML) {
            $pdf = static::generate($content, $title, $orientation, $size, $options);
            $content = $pdf->getContent();
        }
        $path = sys_get_temp_dir().'/'.uniqid().'.pdf';
        file_put_contents($path, $content);
        return $path;
    }

    public function getHeaders(Disposition $disposition = Disposition::INLINE): array
    {
        return [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "{$disposition->value}; filename={$this->title}.pdf",
            'Content-Length' => strlen($this->getContent()),
        ];
    }
}
