<?php

namespace App\Http\Controllers;

use App\Services\InfinyService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class CloudLXController extends Controller
{
    private InfinyService $infinyService;

    public function __construct(InfinyService $infinyService)
    {
        $this->infinyService = $infinyService;
    }

    /**
     * Show list of CloudLX services
     *
     * @return View
     */
    public function index(): View
    {
        $data['services'] = $this->infinyService->getAllServices();

        return view('cloudlx.index', $data);

    }

    /**
     * @param int $serviceId
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $serviceId, Request $request): JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json($this->infinyService->getServiceDetails($serviceId));
        }

        throw new MethodNotAllowedHttpException(['json']);
    }
}
