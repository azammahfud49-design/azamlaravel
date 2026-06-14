<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MahasiswaService;

class DashboardController extends Controller
{
    public function __construct(
        protected MahasiswaService $mahasiswaService
    ) {}

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->mahasiswaService->getStatistik(),
        ]);
    }
}
