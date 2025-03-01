<?php

namespace Modules\User\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Subscriptions\Models\Plan;
use Modules\User\Models\Ranking;
use Yajra\DataTables\DataTables;
use Modules\User\Http\Requests\UserRequest;
use Modules\User\Http\Requests\PasswordRequest;
use App\Trait\ModuleTrait;
use Hash;
use Modules\Subscriptions\Models\Subscription;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExpiringSubscriptionEmail;

class RankingController extends Controller
{


    public function index()
    {

        $module_name = 'user';
        $module_action = 'List';
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ],
            [
                'value' => 'description',
                'text' => __('messages.description'),
            ],
            [
                'value' => 'status',
                'text' => __('plan.lbl_status'),
            ],
        ];
        $export_url = route('backend.users.ranking.export');

        $module_action = 'List';
        $module_title = 'Rankings';


        return view('user::backend.rankings.index', compact('module_name', 'module_action', 'module_title', 'export_import', 'export_columns', 'export_url'));
    }

    public function restore($id)
    {
        Ranking::withTrashed()->find($id)->restore();
        $message = __('messages.restore_form', ['form' => 'Genres']);
        return response()->json(['message' => $message, 'status' => true], 200);

    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $data['contents'] = json_encode($data['contents'] ?? []);

        $ranking = Ranking::findOrFail($id);
        $ranking->update($data);
        $ranking->plans()->sync($request->plans);

        $message = __('messages.update_form', ['form' => 'Ranking']);

        return redirect()->route('backend.users.ranking.index_list')->with('success', $message);
    }

    public function destroy($id)
    {
        Ranking::findOrFail($id)->delete();
        $message = __('messages.delete_form', ['form' => 'Genres']);
        return response()->json(['message' => $message, 'status' => true], 200);
    }


    public function forceDelete($id)
    {

        Ranking::withTrashed()->find($id)->forceDelete();

        $message = __('messages.permanent_delete_form', ['form' => 'Genres']);
        return response()->json(['message' => $message, 'status' => true], 200);
    }



    public function index_data(Datatables $datatable, Request $request)
    {

        $filter = $request->filter;

        $query = Ranking::query();

        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        if (isset($filter['name'])) {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }


        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" data-type="genres" onclick="dataTableRowCheck(' . $row->id . ',this)">';
            })
            ->addColumn('name', function ($row) {
                return $row->name;
                // return view('users::backend.rankings.action', compact('data'));
            })
            ->addColumn('action', function ($data) {
                return view('user::backend.rankings.action', compact('data'));
            })
            ->editColumn('status', function ($row) {
                $checked = $row->status ? 'checked="checked"' : '';
                $disabled = $row->trashed() ? 'disabled' : '';
                return '
                    <div class="form-check form-switch">
                        <input type="checkbox" data-url="' . route('backend.users.ranking.update_status', $row->id) . '"
                            data-token="' . csrf_token() . '" class="switch-status-change form-check-input"
                            id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" ' . $checked . ' ' . $disabled . '>
                    </div>
                ';
            })
            ->editColumn('score', function ($data) {
                return 'score';
            })
            ->editColumn('updated_at', function ($data) {
                $diff = Carbon::now()->diffInHours($data->updated_at);
                return $diff < 25 ? $data->updated_at->diffForHumans() : $data->updated_at->isoFormat('llll');
            })
            ->orderColumns(['id'], '-:column $1')
            ->rawColumns(['action', 'status', 'check'])
            ->toJson();
    }

    public function edit($id)
    {

        $ranking = Ranking::with('plans')->findOrFail($id);
        $ranking->contents = json_decode($ranking->contents) ?? [];
        $plans = Plan::pluck('name', 'id');
        $mediaUrls = getMediaUrls();
        $module_title = 'Rankings';
        return view('user::backend.rankings.edit', compact('ranking', 'mediaUrls', 'module_title', 'plans'));
    }


    public function create()
    {
        $plans = Plan::pluck('name', 'id');
        return view('user::backend.rankings.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $ranking = Ranking::create($data);
        $ranking->plans()->sync($request->plans);
        return $this->edit($ranking->id);
    }

    public function resetResponses($id)
    {
        $ranking = Ranking::findOrFail($id);
        $ranking->resetResponses();

        return redirect()->route('backend.users.ranking.edit', $id)->with('success', __('Ranking responses have been reset.'));
    }

}