<?php

namespace App\Algorithms;

interface SearchInterface
{
    public function search(
        array $data,
        string $keyword,
        string $field = 'nama'
    ): array;
}