<?php

// app/Algorithms/SortAlgorithm.php
namespace App\Algorithms;

/**
 * SortAlgorithm - OOP Concept: Encapsulation & Polymorphism
 *
 * ╔═══════════════════════════════════════════════════════╗
 * ║          ESTIMASI TIME COMPLEXITY SORTING             ║
 * ╠═══════════════════════════════════════════════════════╣
 * ║ Bubble Sort → O(n²)   - Best: O(n) jika sudah sorted ║
 * ║ Merge Sort  → O(n log n) - Stabil, konsisten         ║
 * ╚═══════════════════════════════════════════════════════╝
 */
class SortAlgorithm
{
    /**
     * ┌──────────────────────────────────────────────────────┐
     * │                  BUBBLE SORT                         │
     * │  Time Complexity: O(n²) worst/average | O(n) best   │
     * │  Space Complexity: O(1) - in-place                   │
     * │                                                      │
     * │  Cara Kerja:                                         │
     * │  - Bandingkan dua elemen bersebelahan                │
     * │  - Jika tidak urut, tukar posisinya (swap)           │
     * │  - Elemen terbesar "menggelembung" ke akhir          │
     * │  - Ulangi sebanyak n-1 pass                          │
     * │  - Optimasi: berhenti jika tidak ada swap (sorted)   │
     * └──────────────────────────────────────────────────────┘
     *
     * @param array  $data       Array data mahasiswa
     * @param string $field      Field untuk sorting (nama/nim/tanggal)
     * @param string $direction  asc atau desc
     * @return array Hasil sorting + metadata
     */
    public static function bubbleSort(array $data, string $field = 'nama', string $direction = 'asc'): array
    {
        $n         = count($data);
        $swaps     = 0;
        $passes    = 0;
        $startTime = microtime(true);

        // Outer loop: n-1 pass
        for ($i = 0; $i < $n - 1; $i++) {
            $passes++;
            $swapped = false; // Flag optimasi early exit

            // Inner loop: bandingkan elemen bersebelahan
            // Setiap pass, elemen terbesar sudah di posisi akhir
            for ($j = 0; $j < $n - $i - 1; $j++) {

                // Ambil nilai field untuk dibandingkan
                $valueA = strtolower((string) ($data[$j][$field] ?? ''));
                $valueB = strtolower((string) ($data[$j + 1][$field] ?? ''));

                // Tentukan kondisi swap berdasarkan direction
                $shouldSwap = ($direction === 'asc')
                    ? ($valueA > $valueB)
                    : ($valueA < $valueB);

                if ($shouldSwap) {
                    // Swap: tukar posisi dua elemen
                    [$data[$j], $data[$j + 1]] = [$data[$j + 1], $data[$j]];
                    $swaps++;
                    $swapped = true;
                }
            }

            // Early exit: jika tidak ada swap, data sudah terurut
            if (!$swapped) {
                break;
            }
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 4);

        return [
            'algorithm'       => 'Bubble Sort',
            'time_complexity' => 'O(n²) worst case, O(n) best case',
            'space_complexity'=> 'O(1) - in-place',
            'field'           => $field,
            'direction'       => $direction,
            'total_data'      => $n,
            'passes'          => $passes,
            'swaps'           => $swaps,
            'execution_time'  => "{$executionTime} ms",
            'data'            => array_values($data),
        ];
    }

    /**
     * ┌──────────────────────────────────────────────────────┐
     * │                  MERGE SORT                          │
     * │  Time Complexity: O(n log n) - semua kasus          │
     * │  Space Complexity: O(n) - memerlukan array tambahan  │
     * │                                                      │
     * │  Cara Kerja (Divide and Conquer):                    │
     * │  1. DIVIDE: bagi array menjadi dua bagian sama      │
     * │  2. CONQUER: rekursif sort tiap bagian               │
     * │  3. COMBINE: gabungkan (merge) dua bagian terurut    │
     * │                                                      │
     * │  Keunggulan:                                         │
     * │  - Stabil (stable sort)                              │
     * │  - Konsisten O(n log n) di semua kasus               │
     * │  - Cocok untuk dataset besar                         │
     * └──────────────────────────────────────────────────────┘
     *
     * @param array  $data      Array data mahasiswa
     * @param string $field     Field untuk sorting
     * @param string $direction asc atau desc
     * @return array Hasil sorting + metadata
     */
    public static function mergeSort(array $data, string $field = 'nama', string $direction = 'asc'): array
    {
        $startTime  = microtime(true);
        $comparisons = 0;

        // Jalankan algoritma merge sort rekursif
        $sorted = self::mergeSortRecursive($data, $field, $direction, $comparisons);

        $executionTime = round((microtime(true) - $startTime) * 1000, 4);

        return [
            'algorithm'       => 'Merge Sort',
            'time_complexity' => 'O(n log n) - semua kasus',
            'space_complexity'=> 'O(n) - memerlukan array tambahan',
            'field'           => $field,
            'direction'       => $direction,
            'total_data'      => count($data),
            'comparisons'     => $comparisons,
            'execution_time'  => "{$executionTime} ms",
            'data'            => array_values($sorted),
        ];
    }

    /**
     * Fungsi rekursif Merge Sort (private helper)
     * @param array  $data        Array yang akan diurutkan
     * @param string $field       Field sorting
     * @param string $direction   Arah sorting
     * @param int    &$comparisons Counter perbandingan (pass by reference)
     * @return array Array yang sudah terurut
     */
    private static function mergeSortRecursive(
        array $data,
        string $field,
        string $direction,
        int &$comparisons
    ): array {
        $n = count($data);

        // Base case: array dengan 0 atau 1 elemen sudah terurut
        if ($n <= 1) {
            return $data;
        }

        // DIVIDE: cari titik tengah dan bagi menjadi dua sub-array
        $mid   = (int) floor($n / 2);
        $left  = array_slice($data, 0, $mid);   // Bagian kiri
        $right = array_slice($data, $mid);       // Bagian kanan

        // CONQUER: rekursif sort tiap bagian
        $left  = self::mergeSortRecursive($left,  $field, $direction, $comparisons);
        $right = self::mergeSortRecursive($right, $field, $direction, $comparisons);

        // COMBINE: gabungkan dua bagian yang sudah terurut
        return self::merge($left, $right, $field, $direction, $comparisons);
    }

    /**
     * Fungsi merge: gabungkan dua array terurut menjadi satu array terurut
     * Time Complexity: O(n) - linear untuk proses merge
     */
    private static function merge(
        array $left,
        array $right,
        string $field,
        string $direction,
        int &$comparisons
    ): array {
        $merged = [];
        $i      = 0; // Pointer untuk left
        $j      = 0; // Pointer untuk right

        // Bandingkan elemen left dan right, masukkan yang lebih kecil/besar
        while ($i < count($left) && $j < count($right)) {
            $comparisons++;
            $valueLeft  = strtolower((string) ($left[$i][$field]  ?? ''));
            $valueRight = strtolower((string) ($right[$j][$field] ?? ''));

            $takeLeft = ($direction === 'asc')
                ? ($valueLeft <= $valueRight)
                : ($valueLeft >= $valueRight);

            if ($takeLeft) {
                $merged[] = $left[$i++];
            } else {
                $merged[] = $right[$j++];
            }
        }

        // Tambahkan sisa elemen yang belum diproses
        while ($i < count($left))  $merged[] = $left[$i++];
        while ($j < count($right)) $merged[] = $right[$j++];

        return $merged;
    }
}