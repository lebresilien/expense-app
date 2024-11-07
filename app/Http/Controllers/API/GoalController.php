<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Repository\GoalRepository;

class GoalController extends BaseController
{
    /** @var GoalRepository */
    private $goalRepository;

    public function __contruct(GoalRepository $goalRepository) {
        $this->goalRepository = $goalRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $goals = $this->goalRepository->all(['user_id' => $request->user()->id]);

        return $this->sendResponse($goals, 'List');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'unique:goals,name'],
            'expiredAt' => ['required', 'date', 'before_or_equal:'. now()->format('Y-m-d')],
            'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
        ]);

        $input = $request->all();

        $goal = $this->goalRepository->create($input);

        return $this->sendResponse($goal, 'Opération reussie');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $goal = $this->goalRepository->find($id);

        if (!$goal) return $this->sendError('Aucun Objectif trouvé', []);

        return $this->sendResponse($goal, 'Détails');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $goal = $this->goalRepository->find($id);

        if (!$goal) return $this->sendError('Aucun Objectif trouvé', []);

        $request->validate([
            'name' => ['required', 'unique:goals,name,' .$id],
            'expiredAt' => ['required', 'date', 'before_or_equal:'. now()->format('Y-m-d')],
            'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
        ]);

        $input = $request->all();

        $goal = $this->goalRepository->update($input, $id);

        return $this->sendResponse($goal, 'Edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $goal = $this->goalRepository->find($id);

        if (!$goal) return $this->sendError('Aucun Objectif trouvé', []);

        $this->absentRepository->delete($id);

        return $this->sendResponse([], 'Suppression');
    }
}
