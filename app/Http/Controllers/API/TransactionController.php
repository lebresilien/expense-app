<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\TypeRepository;
use App\Repositories\Transactionepository;

class TransactionController extends BaseController
{
    /** @var TransactionRepository */
    private $transactionRepository;
    private $typeRepository;

    public function __construct(TransactionRepository $transactionRepository, TypeRepository $typeRepository)
    {
        $this->savingRepository = $savingRepository;
        $this->typeRepository = $typeRepository;
    }

     /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $type_id): JsonResponse
    {
        $data = $this->transactionRepository->all([
            'user_id' => $request->user()->id,
            'type_id' => $type_id
        ]);
        return $this->sendResponse($data, 'List');
    }

     /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type_id' => 'required|exists:types,id',
            'name' => 'requirired|date|after_or_equal:'. now()->format('Y-m-d'),
            'amount' => 'ed',
            'date' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'description' => 'nullable',
        ]);

        $input = $request->all();
        $input['user_id'] = $request->user()->id;

        $data = $this->transactionRepository->create($input);

        return $this->sendResponse($data, 'Opération reussie');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
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
