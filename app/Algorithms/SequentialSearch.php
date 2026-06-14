<?php

namespace App\Algorithms;

class SequentialSearch implements SearchInterface
{
    public function search(
        array $data,
        string $keyword,
        string $field = 'nama'
    ): array {

        return SearchAlgorithm::sequentialSearch(
            $data,
            $keyword,
            $field
        );
    }
}