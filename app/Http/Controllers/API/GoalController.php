<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Repositories\GoalRepository;
use Illuminate\Http\JsonResponse;

class GoalController extends BaseController
{
    /** @var GoalRepository */
    private $goalRepository;

    public function __construct(GoalRepository $goalRepository)
    {
        $this->goalRepository = $goalRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $collection = collect([]);

        $goals = $this->goalRepository->all(['user_id' => $request->user()->id]);

        foreach($goals as $goal) {
            $collection->push([
                'id' => $goal->id,
                'name' => $goal->name,
                'amount' => $goal->amount,
                'expiredAt' => $goal->expiredAt,
                'savingAmount' => $goal->savings->sum('amount'),
            ]);
        }

        return $this->sendResponse($collection, 'List');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:goals,name',
            'expiredAt' => 'required|date|after_or_equal:'. now()->format('Y-m-d'),
            'amount' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
        ]);

        $input = $request->all();
        $input['user_id'] = $request->user()->id;

        $goal = $this->goalRepository->all(['name' => $input['name']])->first();

        if($goal) return $this->sendError('Cet objectif existe deja', $goal);

        $goal = $this->goalRepository->create($input);

        return $this->sendResponse($goal, 'Opération reussie');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $goal = $this->goalRepository->find($id);

        if (!$goal) return $this->sendError('Aucun Objectif trouvé', []);

        $data = [
            'goal' => $goal,
            'saving' => $goal->savings
        ];

        return $this->sendResponse($data, 'Détails');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $goal = $this->goalRepository->find($id);

        if (!$goal) return $this->sendError('Aucun Objectif trouvé', []);

        $request->validate([
            'name' => ['required', 'unique:goals,name,' .$id],
            'expiredAt' => ['required', 'date', 'after_or_equal:'. now()->format('Y-m-d')],
            'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
        ]);

        $input = $request->all();

        $goal = $this->goalRepository->update($input, $id);

        return $this->sendResponse($goal, 'Edit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $goal = $this->goalRepository->find($id);

        if (!$goal) return $this->sendError('Aucun Objectif trouvé', []);

        if($goal->saving) $goal->saving->delete();
        $this->goalRepository->delete($id);

        return $this->sendResponse([], 'Suppression');
    }
}
