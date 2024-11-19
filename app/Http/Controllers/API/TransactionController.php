<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\TypeRepository;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;

class TransactionController extends BaseController
{
    /** @var TransactionRepository */
    private $transactionRepository;
    private $typeRepository;

    public function __construct(TransactionRepository $transactionRepository, TypeRepository $typeRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->typeRepository = $typeRepository;
    }

     /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->transactionRepository->list($request->user()->id, $request->start = null, $request->end = null);

        return $this->sendResponse($data, 'List');
    }

     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type_id' => 'required|exists:types,id',
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
            'type_id' => $input['type_id'],
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
}
