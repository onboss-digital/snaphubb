<?php

namespace Modules\Subscriptions\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Subscriptions\Models\Plan;
use Modules\Subscriptions\Models\PlanLimitation;
use Modules\Subscriptions\Models\PlanLimitationMapping;
use Yajra\DataTables\DataTables;

class PlanLimitationMappingController extends Controller
{
    use Authorizable;

    /**
     * Display plan limitations
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        $module_action = 'List';
        $module_title = 'plan_limitation.title';
        $module_name = 'planlimitation';
        
        $filter = [
            'status' => $request->status,
            'plan_id' => $request->plan_id,
        ];

        return view('subscriptions::backend.planlimitation.limitations_index', compact(
            'module_action',
            'module_title',
            'module_name',
            'filter'
        ));
    }

    /**
     * Get limitations data for datatable
     */
    public function index_data(Request $request)
    {
        $query = PlanLimitationMapping::with(['limitation_data', 'plan'])
            ->whereHas('plan', function ($q) {
                if (auth()->user()->id != 1) {
                    $q->where('created_by', auth()->id());
                }
            });

        if ($request->has('plan_id') && !empty($request->plan_id)) {
            $query->where('plan_id', $request->plan_id);
        }

        $mappings = $query->get();

        return DataTables::of($mappings)
            ->addIndexColumn()
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-item" value="' . $row->id . '">';
            })
            ->addColumn('plan_name', function ($row) {
                return optional($row->plan)->name ?? 'N/A';
            })
            ->addColumn('limitation_title', function ($row) {
                return optional($row->limitation_data)->title ?? $row->limitation_slug;
            })
            ->addColumn('limitation_value', function ($row) {
                if ($row->limitation_data && in_array($row->limitation_data->slug, ['video-cast', 'ads', 'download-status'])) {
                    return $row->limit == 1 ? '✓ Active' : '✗ Inactive';
                }
                return $row->limit ?? 'N/A';
            })
            ->addColumn('status', function ($row) {
                $checked = $row->limit == 1 ? 'checked' : '';
                return '<div class="form-check form-switch">
                    <input class="form-check-input status-toggle" type="checkbox" data-id="' . $row->id . '" ' . $checked . '>
                </div>';
            })
            ->addColumn('action', function ($row) {
                $editUrl = route('backend.planlimitation_mapping.edit', $row->id);
                $deleteUrl = route('backend.planlimitation_mapping.destroy', $row->id);
                return '<div class="d-flex gap-2">
                    <a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Edit">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form method="POST" action="' . $deleteUrl . '" class="d-inline" onsubmit="return confirm(\'Are you sure?\')">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>';
            })
            ->rawColumns(['check', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show limitations for a specific plan
     */
    public function planLimitations(Plan $plan)
    {
        $module_action = 'View';
        $module_title = 'Plan Limitations';
        $module_name = 'planlimitation';

        $limitations = PlanLimitationMapping::where('plan_id', $plan->id)
            ->with('limitation_data')
            ->get();

        $allLimitations = PlanLimitation::where('status', 1)->get();

        return view('subscriptions::backend.planlimitation.plan_limitations', compact(
            'plan',
            'limitations',
            'allLimitations',
            'module_action',
            'module_title',
            'module_name'
        ));
    }

    /**
     * Store a new limitation for a plan
     */
    public function storeLimitation(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'planlimitation_id' => 'required|exists:plan_limitation,id',
            'limit' => 'nullable',
        ]);

        PlanLimitationMapping::updateOrCreate(
            [
                'plan_id' => $plan->id,
                'planlimitation_id' => $validated['planlimitation_id'],
            ],
            [
                'limitation_slug' => PlanLimitation::find($validated['planlimitation_id'])->slug,
                'limit' => $validated['limit'] ?? 0,
            ]
        );

        return back()->with('success', 'Limitation added to plan successfully.');
    }

    /**
     * Update limitation status
     */
    public function toggleStatus(Request $request)
    {
        $mapping = PlanLimitationMapping::find($request->id);
        if ($mapping) {
            $mapping->limit = $mapping->limit == 1 ? 0 : 1;
            $mapping->save();
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error'], 404);
    }

    /**
     * Remove limitation from plan
     */
    public function destroy(PlanLimitationMapping $mapping)
    {
        $mapping->delete();
        return back()->with('success', 'Limitation removed successfully.');
    }
}
