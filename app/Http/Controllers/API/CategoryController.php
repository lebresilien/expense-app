<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Repositories\CategoryRepository;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $incomes  = $this->categoryRepository->all([
            'user_id' => $request->user()->id,
            'type_id' => 1
        ]);

        $expenses  = $this->categoryRepository->all([
            'user_id' => $request->user()->id,
            'type_id' => 2
        ]);

        return $this->sendResponse([
            'expenses' => $expenses,
            'incomes' => $incomes
        ], 'List');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type_id' => 'required|exists:types,id',
            'name' => 'required|min:3|unique:categories,name,'. $request->user()->id
        ]);

        $input = $request->all();
        $input['user_id'] = $request->user()->id;

        $data = $this->categoryRepository->create($input);

        return $this->sendResponse($data, 'Opération reussie');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = $this->categoryRepository->find($id);

        if (!$data) return $this->sendError('Aucune transaction trouvée', []);

        return $this->sendResponse($data, 'Détails');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->categoryRepository->find($id);

        if (!$data) return $this->sendError('Aucune transaction trouvée', []);

        $this->categoryRepository->delete($id);

        return $this->sendResponse([], 'Suppression');
    }
}
