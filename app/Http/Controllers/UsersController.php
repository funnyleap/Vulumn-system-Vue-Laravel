<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use App\Exports\ExportFromArray;
use Illuminate\Http\Request;
use App\Repositories\UsersRepository;
use App\Repositories\MenusRepository;
use App\Repositories\UserNotificationsRepository;
use App\Providers\HelperProvider;
use App\Providers\AuthProvider;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    protected $repository;
    protected $request;
    protected $menusRepository;
    protected $userNotificationsRepository;

    public function __construct(
        Request $request,
        MenusRepository $menusRepository,
        UserNotificationsRepository $userNotificationsRepository,
        UsersRepository $repository
    ){
        $this->request = $request;
        $this->repository = $repository;
        $this->menusRepository = $menusRepository;
        $this->userNotificationsRepository = $userNotificationsRepository;
    }
    
    public function index(Request $request) {
        try {
            $payload = $request->all();
            $data = $this->repository->getList($request, [
                'Role',
                'Menu',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ]);
            if (isset($payload['csv'])) return $this->exportCSV($data);
            else return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function findById(Request $request, $id) {
        AuthProvider::has($request, 'users-read');
        try {
            $data = $this->repository->findAll($request, true, [
                'Role',
                'Menu',
                'createdByUser',
                'updatedByUser',
                'deletedByUser',
            ])->find($id);
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function store(Request $request, $id = null) {
        if ($id) AuthProvider::has($request, 'users-update');
        else AuthProvider::has($request, 'users-create');
        try {
            $validate = $this->validateStore($request, $id);
            if($validate['result']) {
                $data = $this->repository->store($request, $id);
                $msg = 'succes saving data';
                if ($id) $msg = 'success update data';
                return H_apiResponse($data, $msg);
            } else {
                return H_apiResponse(null, $validate['message'], 400);
            }
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function restore(Request $request, $id = null) {
        AuthProvider::has($request, 'users-restore');
        try {
            $data = $this->repository->restore($request, $id);
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function remove(Request $request, $id) {
        AuthProvider::has($request, 'users-delete');
        try {
            $payload = $request->all();
            $data = $this->repository->remove($request, $id);
            $msg = 'success deleted data';
            if(isset($payload['permanent'])) $msg = $msg . ' permanently';
            return H_apiResponse($data, $msg);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function exportCSV($data) {
        $data = H_toArrayObject($data);
        $export = new ExportFromArray($data);

        $fileName = 'Users-'.H_getCurrentDate();
        return Excel::download($export, ''.$fileName.'.csv');
    }

    public function roleMenu(Request $request, $id) {
        try {
            $data = $this->repository->getRoleMenu($request, $id);
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function info(Request $request) {
        try {
            $id = H_JWT_getUserId($request);
            $data = $this->repository->info($request, $id);
            return H_apiResponse($data);
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function permissions(Request $request, $id = null) {
        try {
            if ($id == null) $id = H_JWT_getUserId($request);
            $user = $this->repository->findById($request, $id);
            if ($user) {
                $data = $this->repository->rolePermissions($request, $id);
                return H_apiResponse($data);
            } else {
                return H_apiResponse(null, 'User not found', 200);
            }
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function menus(Request $request, $id = null) {
        try {
            if ($id == null) $id = H_JWT_getUserId($request);
            $user = $this->repository->findById($request, $id);
            if ($user) {
                $data = $this->menusRepository->getMenu($request, $user->menu_id);
                return H_apiResponse($data);
            } else {
                return H_apiResponse(null, 'User not found', 200);
            }
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function notifications(Request $request, $id = null) {
        try {
            if ($id == null) $id = H_JWT_getUserId($request);
            $user = $this->repository->findById($request, $id);
            if ($user) {
                $data = $this->userNotificationsRepository->getByUser($request, $user->id);
                return H_apiResponse($data);
            } else {
                return H_apiResponse(null, 'User not found', 200);
            }
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function allNotifications(Request $request, $id = null) {
        try {
            if ($id == null) $id = H_JWT_getUserId($request);
            $user = $this->repository->findById($request, $id);
            if ($user) {
                $data = $this->userNotificationsRepository->getAllByUser($request, $user->id);
                return H_apiResponse($data);
            } else {
                return H_apiResponse(null, 'User not found', 200);
            }
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function changePassword(Request $request, $id = null) {
        try {
            $validate = $this->validateChangePassword($request, $id);
            if($validate['result']) {
                $data = $this->repository->changePassword($request, $id);
                $msg = 'success update data';
                return H_apiResponse($data, $msg);
            } else {
                return H_apiResponse(null, $validate['message'], 400);
            }
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }

    public function updateProfile(Request $request, $id = null) {
        try {
            $validate = $this->validateUpdateProfile($request, $id);
            if($validate['result']) {
                $data = $this->repository->updateProfile($request, $id);
                $msg = 'success update data';
                return H_apiResponse($data, $msg);
            } else {
                return H_apiResponse(null, $validate['message'], 400);
            }
        } catch (Exception $e){
            return H_apiResError($e);
        }
    }
    

    // Validator
    public function validateStore($request, $id = null) {
        try {
            $result = true;
            $message = '';
            $payload = $request->all();

            $validator = Validator::make( $request->all(),
                [
                    'name' => 'required',  
                    'username' => 'required',  
                    'menu_id' => 'required',  
                    'email' => 'required',  
                    'role_id' => 'required' 

                ],
                [
                    'name.required' => 'name is required',  
                    'username.required' => 'username is required',  
                    'menu_id.required' => 'menu is required',  
                    'email.required' => 'email is required',  
                    'role_id.required' => 'role is required' 

                ]
            );
            if ($validator->fails()) {
                $message = $validator->messages()->first();
                $result = false;
            }

            if ($id != null && !$this->repository->findById($request, $id)) {
                $message = 'Data not found';
                $result = false;
            }

            return [
                'result' => $result,
                'message' => $message,
            ];
        } catch (Exception $e){
            if(env('APP_DEBUG')) return H_apiResError($e);
            else {
                $msg = $e->getMessage();
                return H_apiResponse(null, $msg, 400);
            }
        }
    }

    public function validateChangePassword($request, $id = null) {
        try {
            $result = true;
            $message = '';
            $payload = $request->all();

            $validator = Validator::make( $request->all(),
                [
                    'current_password' => 'required',  
                    'password' => 'required'
                ],
                [
                    'current_password.required' => 'current password is required',  
                    'password.required' => 'new password is required'
                ]
            );

            if ($validator->fails()) {
                $message = $validator->messages()->first();
                $result = false;
            }

            if ($id != null && !$this->repository->findById($request, $id)) {
                $message = 'Data not found';
                $result = false;
            }

            if(H_passwordChecker($request['current_password'], $this->repository->findById($request, $id)->password) == false){
                $message = 'Current password is wrong';
                $result = false;
            }

            return [
                'result' => $result,
                'message' => $message,
            ];
        } catch (Exception $e){
            if(env('APP_DEBUG')) return H_apiResError($e);
            else {
                $msg = $e->getMessage();
                return H_apiResponse(null, $msg, 400);
            }
        }
    }

    public function validateUpdateProfile($request, $id = null) {
        try {
            $result = true;
            $message = '';
            $payload = $request->all();

            $validator = Validator::make( $request->all(),
                [
                    'name' => 'required',  
                    'email' => 'required'
                ],
                [
                    'name.required' => 'name is required',  
                    'email.required' => 'email is required'
                ]
            );

            if ($validator->fails()) {
                $message = $validator->messages()->first();
                $result = false;
            }

            $data = $this->repository->findById($request, $id);
            if ($id != null && !$data) {
                $message = 'Data not found';
                $result = false;
            }

            // if ($data->email != $request['email'] && $this->repository->model->where('email', $request['email'])->first()) {
            //     $message = 'Email already used';
            //     $result = false;
            // }

            // if ($data->username != $request['username'] && $this->repository->model->where('username', $request['username'])->first()) {
            //     $message = 'Username already used';
            //     $result = false;
            // }

            return [
                'result' => $result,
                'message' => $message,
            ];
        } catch (Exception $e){
            if(env('APP_DEBUG')) return H_apiResError($e);
            else {
                $msg = $e->getMessage();
                return H_apiResponse(null, $msg, 400);
            }
        }
    }
}
  
        