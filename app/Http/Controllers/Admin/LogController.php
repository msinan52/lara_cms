<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\DeleteAllLogsJobs;
use App\Jobs\SendUserVerificationMail;
use App\Models\Log;
use App\Repositories\Concrete\BaseRepository;
use App\Repositories\Interfaces\LogInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    protected $model;

    public function __construct(LogInterface $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        $searchText = \request('q');
        $list = $this->model->allWithPagination($searchText);
        return view('admin.log.list_logs', compact('list'));
    }

    public function show($id)
    {
        $log = $this->model->getById($id);
        return view('admin.log.show_log', compact('log'));
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect(route('admin.logs'));
    }

    public function deleteAll()
    {
        $this->dispatch(new DeleteAllLogsJobs());
        return redirect(route('admin.logs'))->with('message', 'Loglar silinmek için sıraya(queues & jobs) alındı');
    }
}
