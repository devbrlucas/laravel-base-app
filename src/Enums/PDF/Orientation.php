<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Enums\PDF;

enum Orientation: string
{
    case PORTRAIT = 'portrait';
    case LANDSCAPE = 'landscape';
}