<?php

namespace App\Algorithms;

class LinearSearch implements SearchInterface
{
    public function search(
        array $data,
        string $keyword,
        string $field = 'nama'
    ): array {

        return SearchAlgorithm::linearSearch(
            $data,
            $keyword,
            $field
        );
    }
}