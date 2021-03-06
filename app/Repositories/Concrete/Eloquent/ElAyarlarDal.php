<?php namespace App\Repositories\Concrete\Eloquent;

use App\Models\Ayar;
use App\Repositories\Concrete\ElBaseRepository;
use App\Repositories\Interfaces\AyarlarInterface;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Cache\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ElAyarlarDal implements AyarlarInterface
{
    public $model;

    public function __construct(Ayar $model)
    {
        $this->model = app()->makeWith(ElBaseRepository::class, ['model' => $model]);
    }

    public function getCachedConfig()
    {
        return \Cache::get('siteConfig');
    }

    public function update(array $data, $id)
    {
        $this->model->update($data, $id);
        $entry = $this->model->getById($id);
        $this->logoIconUploadedForUpdateCreateStatement($entry);
        Ayar::setCache($entry);
        return $entry;
    }

    public function create(array $data)
    {
        $entry = $this->model->create($data);
        $this->logoIconUploadedForUpdateCreateStatement($entry);
        Ayar::setCache($entry);
        return $entry;
    }

    private function logoIconUploadedForUpdateCreateStatement($entry)
    {
        if (\request()->hasFile('logo')) {
            $logo_file = \request()->file('logo');
            $logo_name = 'logo' . '.' . $logo_file->extension();
            $logo_file->move(config('constants.image_paths.config_image_folder_path'), $logo_name);
            $entry->update(['logo' => $logo_name]);
        }
        if (\request()->hasFile('icon')) {
            $icon_file = \request()->file('icon');
            $icon_name = 'icon' . '.' . $icon_file->extension();
            $icon_file->move(config('constants.image_paths.config_image_folder_path'), $icon_name);
            $entry->update(['icon' => $icon_name]);
        }
        if (\request()->hasFile('footer_logo')) {
            $footer_logo_file = \request()->file('footer_logo');
            $footer_logo_name = 'footer_logo' . '.' . $footer_logo_file->extension();
            $footer_logo_file->move(config('constants.image_paths.config_image_folder_path'), $footer_logo_name);
            $entry->update(['footer_logo' => $footer_logo_name]);
        }
    }

    public function all($filter = null, $columns = array("*"), $relations = null)
    {
        return $this->model->all()->get();
    }

    public function allWithPagination($filter = null, $columns = array("*"), $perPageItem = null, $relations = null)
    {
        // TODO: Implement allWithPagination() method.
    }

    public function getById($id, $columns = array('*'), $relations = null)
    {
        return $this->model->getById($id, $columns, $relations);
    }

    public function getByColumn(string $field, $value, $columns = array('*'), $relations = null)
    {
        // TODO: Implement getByColumn() method.
    }

    public function delete($id)
    {
        if (count($this->model->all()->get()) == 1) {
            session()->flash('message', "En az 1 adet site ayarı olması gerektiği için bu kaydı silemezsiniz");
            session()->flash('message_type', "danger");
            return false;
        }
        session('message', config('constants.messages.success_message'));
        $deletedRecord = $this->model->delete($id);
        $config = Ayar::orderByDesc('id')->first();
        Ayar::setCache($config);
    }

    public function with($relations, $filter = null, bool $paginate = null, int $perPageItem = null)
    {
        // TODO: Implement with() method.
    }
}
