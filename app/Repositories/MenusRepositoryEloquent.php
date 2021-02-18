<?php

namespace App\Repositories;

use Laravel\Lumen\Application;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\MenusRepository;
use App\Models\Menus;
use App\Validators\MenusValidator;
use Exception;

use App\Providers\HelperProvider;

class MenusRepositoryEloquent extends BaseRepository implements MenusRepository
{

    public function __construct(
        Application $app
	){
		parent::__construct($app);
    }

    /**
     * Specify Model class name
     * @return array
     */
    public function model() {
        return Menus::class;
    }

    /**
     * Specify Model class name
     * @return array
     */
    public function columns() {
        return $this->model->Columns();
    }

    public function validateColumns($name) {
        $valid = false;
        foreach ($this->columns() as $col) {
            $mixCol = $col.'!';
            if ($name == $col || $name == $mixCol) {
                $valid = true;
                break;
            }
            else $valid = false;
        }
        return $valid;
    }

    /**
     * Model initiate
     * @return object
     */
    public function initModel($id = null) {
        $model = new Menus;
        if (!empty($id)) $model = $this->model->where($this->model->getKeyName(), $id)->first();
        return $model;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot() {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @return array
     * 
     * @raw_request : raw data from controller
     */
    public function totalData($raw_request) {
        $payload = $raw_request->all();
        $data = $this->model;
        if (H_hasRequest($payload, 'trash')) $data = $data->onlyTrashed();
        return $data->count();
    }
 
    /**
     * @return array
     * 
     * @raw_request : raw data from controller
     * @$raw : true / false : raw query or executed query
     *  
     * Search Usage : 
     * #Related search
     * - single : search={columName}:{value}
     * - multiple : search={columName}:{value}|{columName2}:{value2}
     * - separator : |
     * 
     * #Exact search 
     * - add `!` before `:` , ex : search={columName}!:{value}
     * 
     */
    public function findAll($raw_request, $raw = false) {
        try {
            $payload = $raw_request->all();
            $data = $this->model;
            if (H_hasRequest($payload, 'trash')) $data = $data->onlyTrashed();
            
            $search = [];
            if (isset($payload['search'])) $search = H_extractParamsAttribute($payload['search']);
            
            $order = [];
            if (isset($payload['order'])) $order = H_extractParamsAttribute($payload['order']);

            if (count($search) != 0) { // filter search
                foreach ($search as $key => $params) {
                    if ($this->validateColumns($params['key'])) {
                        $key = H_escapeStringTable($params['key']);
                        $value = H_escapeString($params['value']);

                        $importantCheck = explode('!', $key);
                        $column = $importantCheck[0];

                        if (isset($importantCheck[1])) {
                            $data =  $data->whereRaw(''.$column.' = ? ', [$value]);
                        } else {
                            $data =  $data->whereRaw(''.$column.' = ? OR '.$column.' like ?', [$value,'%'.$value.'%']);
                        }
                    }
                }
            }

            if (count($order) != 0) { // order
                foreach ($order as $key => $params) {
                    if ($this->validateColumns($params['key'])) {
                        $key = H_escapeString($params['key']);
                        $value = H_escapeString($params['value']);
                        $value = strtoupper($value);
                        if ($value == 'ASC' || $value == 'DESC') $data = $data->orderBy($key, $value);
                    }
                }
            }

            if ($raw) return $data;
            else return $data->get();

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return object
     * 
     * @raw_request : raw data from controller
     * @id : integer
     */
    public function findById($raw_request, $id) {
        $payload = $raw_request->all();
        $data = $this->findAll($raw_request, true);
        $data = $data->where($this->model->getKeyName(), $id)->first();
        return !empty($data) ? $data : null;
    }

    public function getList($raw_request) {
		try {
            $payload = $raw_request->all();
            $data = $this->findAll($raw_request, true);
 
            $limit = env('PAGINATION_LIMIT', 5);
            if (H_hasRequest($payload, 'limit') && $payload['limit'] != '0') $limit = $payload['limit'];

            if (isset($payload['table'])) return $data->paginate($limit)->withQueryString();
            else {
                if (H_hasRequest($payload, 'limit') && $payload['limit'] == '0') return $data->get();
                else return $data->limit($limit)->get();
            }
        } catch (Exception $e){
			throw new Exception($e->getMessage());
        }
    }

    /**
     * @return object
     * 
     * params information
     * @raw_request : array (with Request model) 
     * @id : integer
     * @customRequest : array --assoc array type, to replace value default from request
     */
    public function store($raw_request, $id = null, $customRequest = null) {
        try {
 
            if ($customRequest === null) $request = $raw_request->all();
            else $request = $customRequest;

            $data = $this->initModel($id);

            //storing defined property    
            $data->menu_item_id = $request['menu_item_id']; 
            $data->master_menu_id = $request['master_menu_id']; 

            
            $data->save();
            return $data;

        } catch (Exception $e){ 
            throw new Exception($e->getMessage());
        } 
    }

    /**
     * @return object
     * 
     * params information
     * @raw_request : array (with Request model) 
     * @id : integer
     */
    public function remove($raw_request, $id) {
        try {
            $request = $raw_request->all();
            $data = $this->model->where($this->model->getKeyName(), $id);

            if(isset($request['permanent'])) {
                // if data null, check again from trashbin
                if ($data->first() == null) $data = $data->onlyTrashed();
            }
            $data = $data->first();

            if ($data) {
                if(isset($request['permanent'])) $data->forceDelete();
                else $data->delete();
                return $data;
            } else {
                return null;
            }

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return object
     * 
     * params information
     * @raw_request : array (with Request model) 
     * @id : integer
     */
    public function restore($raw_request, $id) {
        try {

            $data = $this->model->whereId($id)->onlyTrashed()->first();
            if ($data) return $data->restore();
            else return null;

        } catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

}
        