<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Support;

use DevBRLucas\LaravelBaseApp\Enums\PDF\Disposition;
use DevBRLucas\LaravelBaseApp\Enums\PDF\Orientation;
use Dompdf\Dompdf;

class PDF
{
    public function __construct(
        private readonly string $title,
        private readonly string $content,
    )
    {
        //
    }

    public function getContent(): string
    {
        return $this->content;    
    }

    public function geTitle(): string
    {
        return $this->title;
    }

    public static function generate(string $content, string $title, Orientation $orientation, string|array $size = 'A4', array $options = []): static
    {
        $dompdf = new Dompdf([
            'isRemoteEnabled' => true,
            'isJavascriptEnabled' => false,
            'dpi' => 300,
            ...$options,
        ]);
        $dompdf->addInfo('title', $title);
        $dompdf->setPaper($size, $orientation->value);
        $dompdf->loadHtml($content);
        $dompdf->render();
        return new static($title, $dompdf->output());
    }

    public static function generateTempFile(string $content, string $title, Orientation $orientation, bool $isHTML = true, string|array $size = 'A4', array $options = []): string
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
            'Content-Length' => strlen($this->content),
        ];
    }
}
