<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Repositories\SavingRepository;
use App\Repositories\GoalRepository;
use Illuminate\Http\JsonResponse;

class SavingController extends BaseController
{
    /** @var SavingRepository */
    private $savingRepository;
    private $goalRepository;

    public function __construct(SavingRepository $savingRepository, GoalRepository $goalRepository)
    {
        $this->savingRepository = $savingRepository;
        $this->goalRepository = $goalRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'goal_id' => 'required|exists:goals,id',
            'amount' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'day' => 'required|date',
        ]);

        $input = $request->all();

        $goal = $this->goalRepository->find($input['goal_id']);

        if (!$goal) return $this->sendError('Cet objectif n\'existe pas', []);

        $amount = $this->savingRepository->all(['goal_id' => $input['goal_id']])->sum('amount') + $input['amount'];

        if($amount > $goal->amount) {
            return $this->sendError('Avec ce montant l\'objectif sera depassé');
        }

        $saving = $this->savingRepository->create($input);

        return $this->sendResponse($saving, 'Opération reussie');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }
}
