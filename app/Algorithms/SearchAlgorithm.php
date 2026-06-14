<?php

namespace App\Algorithms;

class SearchAlgorithm
{
    public static function linearSearch(array $data, string $keyword, string $field = 'nama'): array
    {
        $results = [];
        $keyword = strtolower($keyword);

        foreach ($data as $item) {
            $value = strtolower((string) ($item[$field] ?? ''));
            if (str_contains($value, $keyword)) {
                $results[] = $item;
            }
        }

        return [
            'algorithm'       => 'Linear Search',
            'time_complexity' => 'O(n)',
            'keyword'         => $keyword,
            'field'           => $field,
            'total_data'      => count($data),
            'found'           => count($results),
            'data'            => $results,
        ];
    }

    public static function binarySearch(array $data, string $keyword, string $field = 'nama'): array
    {
        $startTime = microtime(true);
        $keyword   = strtolower($keyword);

        usort($data, function ($a, $b) use ($field) {
            return strcmp(
                strtolower((string) ($a[$field] ?? '')),
                strtolower((string) ($b[$field] ?? ''))
            );
        });

        $low    = 0;
        $high   = count($data) - 1;
        $found  = false;
        $result = null;
        $steps  = 0;

        while ($low <= $high) {
            $steps++;
            $mid  = (int) floor(($low + $high) / 2);
            $value = strtolower((string) ($data[$mid][$field] ?? ''));

            if ($value === $keyword) {
                $found  = true;
                $result = $data[$mid];
                break;
            }

            if ($value < $keyword) {
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 4);

        return [
            'algorithm'       => 'Binary Search',
            'time_complexity' => 'O(log n)',
            'keyword'         => $keyword,
            'field'           => $field,
            'total_data'      => count($data),
            'steps'           => $steps,
            'found'           => $found,
            'data'            => $found ? $result : null,
            'execution_time'  => "{$executionTime} ms",
        ];
    }

    public static function sequentialSearch(array $data, string $keyword, string $field = 'nama'): array
    {
        $results = [];
        $keyword = strtolower($keyword);
        $steps   = 0;

        foreach ($data as $item) {
            $steps++;
            $value = strtolower((string) ($item[$field] ?? ''));
            if (str_contains($value, $keyword)) {
                $results[] = $item;
            }
        }

        return [
            'algorithm'       => 'Sequential Search',
            'time_complexity' => 'O(n)',
            'keyword'         => $keyword,
            'field'           => $field,
            'total_data'      => count($data),
            'steps'           => $steps,
            'found'           => count($results),
            'data'            => $results,
        ];
    }
}
