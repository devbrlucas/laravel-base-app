<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Enums\PDF;

enum Disposition: string
{
    case INLINE = 'inline';
    case ATTACHMENT = 'attachment';
}