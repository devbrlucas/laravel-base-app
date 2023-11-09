<?php

declare(strict_types=1);

namespace DevBRLucas\Enums\PDF;

enum Orientation: string
{
    case PORTRAIT = 'portrait';
    case LANDSCAPE = 'landscape';
}