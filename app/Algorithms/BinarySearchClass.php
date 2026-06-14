<?php

namespace App\Algorithms;

class BinarySearch implements SearchInterface
{
    public function search(
        array $data,
        string $keyword,
        string $field = 'nama'
    ): array {

        return SearchAlgorithm::binarySearch(
            $data,
            $keyword,
            $field
        );
    }
}