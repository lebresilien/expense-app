<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Repositories\CategoryRepository;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

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
            'type_id' => 2
        ]);

        $expenses  = $this->categoryRepository->all([
            'user_id' => $request->user()->id,
            'type_id' => 1
        ]);

        return $this->sendResponse([
            'expenses' => $expenses,
            'incomes' => $incomes
        ], 'List');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type_id' => 'required|exists:types,id',
            'name' => 'required|min:3'
        ]);

        $input = $request->all();
        $input['user_id'] = $request->user()->id;

        $category = $this->categoryRepository->all([
            'name' => $input['name'],
            'type_id' => $input['type_id'],
            'user_id' => $input['user_id']
        ]);

        if(count($category) > 0) return $this->sendError('Cette categorie existe deja');

        $data = $this->categoryRepository->create($input);

        return $this->sendResponse($data, 'Opération reussie');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, $start, $end)
    {
        $startDate = Carbon::parse($start)->format('Y-m-d');
        $endDate = Carbon::parse($end)->format('Y-m-d');

        $data = $this->categoryRepository->find($id);

        if (!$data) return $this->sendError('Aucune transaction trouvée', []);


        $array = [];
        foreach($data->transactions->whereBetween('date', [$startDate, $endDate]) as $item) {
            array_push($array, $item);
        }

        $data = [
            "type" => $data->type->id,
            "name" => $data->name,
            "data" => $array
        ];

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
        $category = $this->categoryRepository->find($id);

        if (!$category) return $this->sendError('Aucune transaction trouvée', []);

        if($category->transactions->count() > 0) return $this->sendError('Cette Categorie ne peut etre supprimée', []);

        $this->categoryRepository->delete($id);

        return $this->sendResponse([], 'Suppression');
    }
}
