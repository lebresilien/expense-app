<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\TypeRepository;

class TypeController extends BaseController
{
    /** @var TypeRepository */
    private $typeRepository;

    public function __construct(TypeRepository $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $data = $this->typeRepository->all();
        return $this->sendResponse($data, 'List');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:types,name',
        ]);

        $input = $request->all();

        $data = $this->typeRepository->create($input);

        return $this->sendResponse($data, 'Opération reussie');
    }
}
