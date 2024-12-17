<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\TypeRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\CategoryRepository;
use Carbon\Carbon;

class TransactionController extends BaseController
{
    /** @var TransactionRepository */
    private $transactionRepository;
    private $typeRepository;
    private $categoryRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        TypeRepository $typeRepository,
        CategoryRepository $categoryRepository
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->typeRepository = $typeRepository;
        $this->categoryRepository = $categoryRepository;
    }

     /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $data = $this->transactionRepository->list($request->user()->id, $request->start, $request->end, $request->type, $request->date);

        $data['categoryExpenses'] = $this->categoryRepository->all([
            'user_id' => $request->user()->id,
            'type_id' => 1
        ]);

        $data['categoryIncomes'] = $this->categoryRepository->all([
            'user_id' => $request->user()->id,
            'type_id' => 2
        ]);

        return $this->sendResponse($data, 'List');
    }

     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date|before_or_equal:'. now()->format('Y-m-d'),
            'name' => 'required|min:3',
            'amount' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'description' => 'sometimes|required',
        ]);

        $input = $request->all();
        $input['user_id'] = $request->user()->id;

        $item = $this->transactionRepository->all([
            'name' => $input['name'],
            'date' => $input['date'],
            'category_id' => $input['category_id'],
            'amount' => $input['amount'],
        ])->first();

        if($item) return $this->sendError('Cette transaction existe deja', []);

        $data = $this->transactionRepository->create($input);

        return $this->sendResponse($data, 'Opération reussie');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = $this->transactionRepository->find($id);

        if (!$data) return $this->sendError('Aucune transaction trouvée', []);

        return $this->sendResponse($data, 'Détails');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $data = $this->transactionRepository->find($id);

        if (!$data) return $this->sendError('Aucune transaction trouvée', []);

        $this->transactionRepository->delete($id);

        return $this->sendResponse([], 'Suppression');
    }

    public function statistics(Request $request) {

        return $data = $this->transactionRepository->statistics($request->type);

    }
}
