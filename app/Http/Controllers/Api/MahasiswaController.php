<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MahasiswaService;
use App\Algorithms\SearchAlgorithm;
use App\Algorithms\SortAlgorithm;
use App\Http\Requests\MahasiswaRequest;
use App\Exceptions\MahasiswaException;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function __construct(
        protected MahasiswaService $mahasiswaService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'jurusan', 'sort_by', 'sort_dir', 'per_page']);
        $data = $this->mahasiswaService->getAll($filters);
        return response()->json([
            'success' => true,
            'data'    => $data->items(),
            'meta'    => [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
            ],
        ]);
    }

    public function store(MahasiswaRequest $request)
    {
        $mahasiswa = $this->mahasiswaService->create(
            $request->validated(),
            $request->file('foto')
        );
        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil ditambahkan.',
            'data'    => $mahasiswa,
        ], 201);
    }

    public function show(int $id)
    {
        $mahasiswa = $this->mahasiswaService->findById($id);
        return response()->json([
            'success' => true,
            'data'    => $mahasiswa,
        ]);
    }

    public function update(MahasiswaRequest $request, int $id)
    {
        $mahasiswa = $this->mahasiswaService->update(
            $id,
            $request->validated(),
            $request->file('foto')
        );
        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil diperbarui.',
            'data'    => $mahasiswa,
        ]);
    }

    public function destroy(int $id)
    {
        $this->mahasiswaService->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil dihapus.',
        ]);
    }

    public function linearSearch(Request $request)
    {
        $request->validate(['keyword' => 'required|string', 'field' => 'nullable|string']);
        $data    = $this->mahasiswaService->getAllForAlgorithm();
        $result  = SearchAlgorithm::linearSearch($data, $request->keyword, $request->field ?? 'nama');
        return response()->json($result);
    }

    public function binarySearch(Request $request)
    {
        $request->validate(['keyword' => 'required|string', 'field' => 'nullable|string']);
        $data   = $this->mahasiswaService->getAllForAlgorithm();
        $result = SearchAlgorithm::binarySearch($data, $request->keyword, $request->field ?? 'nama');
        return response()->json($result);
    }

    public function sequentialSearch(Request $request)
    {
        $request->validate(['keyword' => 'required|string', 'field' => 'nullable|string']);
        $data   = $this->mahasiswaService->getAllForAlgorithm();
        $result = SearchAlgorithm::sequentialSearch($data, $request->keyword, $request->field ?? 'nama');
        return response()->json($result);
    }

    public function bubbleSort(Request $request)
    {
        $request->validate(['field' => 'nullable|string', 'direction' => 'nullable|in:asc,desc']);
        $data   = $this->mahasiswaService->getAllForAlgorithm();
        $result = SortAlgorithm::bubbleSort($data, $request->field ?? 'nama', $request->direction ?? 'asc');
        return response()->json($result);
    }

    public function mergeSort(Request $request)
    {
        $request->validate(['field' => 'nullable|string', 'direction' => 'nullable|in:asc,desc']);
        $data   = $this->mahasiswaService->getAllForAlgorithm();
        $result = SortAlgorithm::mergeSort($data, $request->field ?? 'nama', $request->direction ?? 'asc');
        return response()->json($result);
    }
}
