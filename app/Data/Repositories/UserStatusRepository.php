<?php
namespace App\Data\Repositories;

use App\Data\Models\UserInfo;
use App\User;
use App\Data\Models\UserStatus;
use App\Data\Models\UpdateStatus;
use App\Data\Repositories\BaseRepository;

class UserStatusRepository extends BaseRepository
{

    protected 
        $user_info,
        $user,
        $update_status,
        $user_status;

    public function __construct(
        UserInfo $user_info,
        UserStatus $user_status,
        UpdateStatus $update_status
    ) {
        $this->user_info = $user_info;
        $this->user_status = $user_status;
        $this->update_status = $update_status;

        $this->no_sort = [
            'type','status'
        ];
    } 

    public function getStatus($data = [])
    {
        $meta_index = "metadata";
        $parameters = [];
        $count      = 0;
        if (isset($data['type'])) {

            $meta_index     = "metadata";
            $data['single'] = false;
            $data['where']  = [
                [
                    "target"   => "type",
                    "operator" => "=",
                    "value"    => $data['type'],
                ],
            ];

            //$parameters['user_id'] = $data['id'];

        }
        if (isset($data['target']) || isset($data['query'])) {
            if (!isset($data['query'])) {
                return $this->setResponse([
                    "code" => 500,
                    "title" => "Query is not set",
                    "parameters" => $data,
                ]);
            }
            if (!isset($data['target'])) {
                return $this->setResponse([
                    "code" => 500,
                    "title" => "target is not set",
                    "parameters" => $data,
                ]);
            }

            $result = $this->user_status;
            //$data['relations'] = ['filedby','user'];

            $meta_index = "statuses";
            $parameters = [
                "query" => $data['query'],
            ];

            // foreach ((array) $data['target'] as $index => $column) {
            //     if (str_contains($column, "type")) {

            //         $data['target'][] = 'filedby.firstname';
            //         $data['target'][] = 'filedby.middlename';
            //         $data['target'][] = 'filedby.lastname';
            //         unset($data['target'][$index]);
            //     }
            // }

            $count_data = $data;
            $result = $this->genericSearch($data, $result)->get()->all();
            if (!$result) {
                return $this->setResponse([
                    'code' => 404,
                    'title' => "No status are found",
                    "meta" => [
                        $meta_index => $result,
                    ],
                    "parameters" => $parameters,
                ]);
            }

            $count = count($result);

            return $this->setResponse([
                "code" => 200,
                "title" => "Successfully retrieved statuses",
                "description" => "status",
                "meta" => [
                    $meta_index => $result,
                    "count" => $count,
                ],
                "parameters" => $parameters,

            ]);
        }
        $count_data = $data;
        //$data['relations'] = ["user_logs","accesslevelhierarchy"];        
        $count_data = $data;    
        $result = $this->fetchGeneric($data, $this->user_status);

        if (!$result) {
            return $this->setResponse([
                'code'       => 404,
                'title'      => "No status are found",
                "meta"       => [
                    $meta_index => $result,
                ],
                "parameters" => $parameters,
            ]);
        }
       
        $count = $this->countData($count_data, refresh_model($this->user_status->getModel()));

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully retrieved statuses",
            "description"=>"maoni",
            "meta"       => [
                $meta_index => $result,
                "count"     => $count,
            ],
            "count"     => $count,
            //"parameters" => $data['user_id'],
            
        ]);
    }

    public function statuslogs($data = [])
    {
        $meta_index = "metadata";
        $parameters = [];
        $count      = 0;
        if (isset($data['user_id'])) {

            $meta_index     = "metadata";
            $data['single'] = false;
            $data['where']  = [
                [
                    "target"   => "user_id",
                    "operator" => "=",
                    "value"    => $data['user_id'],
                ],
            ];

            //$parameters['user_id'] = $data['id'];

        }
        $count_data = $data;
        //$data['relations'] = ["user_logs","accesslevelhierarchy"];        
        $count_data = $data;    
        $result = $this->fetchGeneric($data, $this->update_status);

        if (!$result) {
            return $this->setResponse([
                'code'       => 404,
                'title'      => "No status are found",
                "meta"       => [
                    $meta_index => $result,
                ],
                "parameters" => $parameters,
            ]);
        }
       
        $count = $this->countData($count_data, refresh_model($this->update_status->getModel()));

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully retrieved status logs",
            "description"=>"maoni",
            "meta"       => [
                $meta_index => $result,
                "count"     => $count,
            ],
            "count"     => $count,
            //"parameters" => $data['user_id'],
            
        ]);
    }
    public function addStatus($data = [])
    {
        // data validation

            if (!isset($data['status'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "status is not set.",
                ]);
            }

            if (!isset($data['type'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "Type  is not set.",
                ]);
            }

       
            $stat = $this->user_status->init($this->user_status->pullFillable($data));
            $stat->save($data);

        if (!$stat->save($data)) {
            return $this->setResponse([
                "code"        => 500,
                "title"       => "Data Validation Error.",
                "description" => "An error was detected on one of the inputted data.",
                "meta"        => [
                    "errors" => $stat->errors(),
                ],
            ]);
        }

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully defined an user status",
            "description" => "Status",
            "meta"       => [
                "metadata" => $stat,
            ],
        ]);
        
    }
    public function updateUserStatus($data = [])
    {
        // data validation

            if (!isset($data['status'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "status is not set.",
                ]);
            }

            if (!isset($data['type'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "Type  is not set.",
                ]);
            }

       
                $stat = $this->user_status->find($data['id']);
                // $stat->type=$data['status'];
                // $stat->status=$data['type'];
                $stat->save($data);

        if (!$stat->save($data)) {
            return $this->setResponse([
                "code"        => 500,
                "title"       => "Data Validation Error.",
                "description" => "An error was detected on one of the inputted data.",
                "meta"        => [
                    "errors" => $stat->errors(),
                ],
            ]);
        }

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully updated a Status.",
            "meta"        => [
                "status" => $stat,
            ]
        ]);
            
        
    }

    public function deleteUserStatus($data = [])
    {
        $stat = $this->user_status->find($data['id']);

        if (!$stat->delete()) {
            return $this->setResponse([
                "code"        => 500,
                "title"       => "Data Validation Error.",
                "description" => "An error was detected on one of the inputted data.",
                "meta"        => [
                    "errors" => $stat->errors(),
                ],
            ]);
        }

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully deleted a Status.",
            "meta"        => [
                "status" => $stat,
            ]
        ]);
            
        
    }

      public function fetchUserLog($data = [])
    {
        $meta_index = "User";
        $parameters = [];
        $count      = 0;

        if (isset($data['id']) &&
            is_numeric($data['id'])) {

            $meta_index     = "User";
            $data['single'] = false;
            $data['where']  = [
                [
                    "target"   => "id",
                    "operator" => "=",
                    "value"    => $data['id'],
                ],
            ];

            $parameters['user_id'] = $data['id'];

        }

        $count_data = $data;

         $data['relations'] = ["user_info","user_logs","accesslevel"];     

        $result = $this->fetchGeneric($data, $this->user);

        if (!$result) {
            return $this->setResponse([
                'code'       => 404,
                'title'      => "No agent logs are found",
                "meta"       => [
                    $meta_index => $result,
                ],
                "parameters" => $parameters,
            ]);
        }

        $count = $this->countData($count_data, refresh_model($this->user->getModel()));

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully retrieved agent logs",
            "meta"       => [
                $meta_index => $result,
                "count"     => $count,
            ],
            "parameters" => $parameters,
        ]);
    }


}
