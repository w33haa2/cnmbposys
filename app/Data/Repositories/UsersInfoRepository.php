<?php
namespace App\Data\Repositories;

use App\Data\Models\UserInfo;
use App\User;
use App\Data\Models\UsersData;
use App\Data\Models\Users;
use App\Data\Models\UserCluster;
use App\Data\Models\UpdateStatus;
use App\Data\Models\AccessLevelHierarchy;
use App\Data\Models\UserBenefit;
use App\Data\Repositories\BaseRepository;

class UsersInfoRepository extends BaseRepository
{

    protected 
        $user_info,$user_datum,$user_status,$user_benefits,$user_infos,
        $user,$access_level_hierarchy;

    public function __construct(
        UsersData $user_info,
        UserInfo $user_infos,
        User $user,
        Users $user_datum,
        UpdateStatus $user_status,
        UserCluster $select_users,
        UserBenefit $user_benefits,
        AccessLevelHierarchy $access_level_hierarchy
    ) {
        $this->user_info = $user_info;
        $this->user_infos = $user_infos;
        $this->user = $user;
        $this->user_datum = $user_datum;
        $this->user_status = $user_status;
        $this->select_users = $select_users;
        $this->user_benefits = $user_benefits;
        $this->access_level_hierarchy = $access_level_hierarchy;
    } 

    public function usersInfo($data = [])
    {
        $meta_index = "metadata";
        $parameters = [];
        $count      = 0;

        if (isset($data['id']) &&
            is_numeric($data['id'])) {

            $meta_index     = "metadata";
            $data['single'] = true;
            $data['where']  = [
                [
                    "target"   => "id",
                    "operator" => "=",
                    "value"    => $data['id'],
                ],
            ];

            $parameters['id'] = $data['id'];

        }

        $count_data = $data;
        $data['relations'] = ["user_info", "accesslevel", "benefits"];        
        $count_data = $data;    
        $result = $this->fetchGeneric($data, $this->user_info);

        if (!$result) {
            return $this->setResponse([
                'code'       => 404,
                'title'      => "No Users are found",
                "meta"       => [
                    $meta_index => $result,
                ],
                "parameters" => $parameters,
            ]);
        }
       
        $count = $this->countData($count_data, refresh_model($this->user_info->getModel()));

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully retrieved users Information",
            "description"=>"UserInfo",
            "meta"       => [
                $meta_index => $result,
                "count"     => $count,
            ],
            "count"     => $count,
            "parameters" => $parameters,
            
        ]);
    }

    public function logsInputCheck($data = [])
    {
        // data validation
        

            if (!isset($data['user_id']) ||
                !is_numeric($data['user_id']) ||
                $data['user_id'] <= 0) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "User ID is not set.",
                ]);
            }

            if (!isset($data['action'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "action ID is not set.",
                ]);
            }

            if (!isset($data['affected_data'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "data affected is not set.",
                ]);
            }

       
            $action_logs = $this->action_logs->init($this->action_logs->pullFillable($data));
            $action_logs->save($data);

        if (!$action_logs->save($data)) {
            return $this->setResponse([
                "code"        => 500,
                "title"       => "Data Validation Error.",
                "description" => "An error was detected on one of the inputted data.",
                "meta"        => [
                    "errors" => $action_logs->errors(),
                ],
            ]);
        }

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully defined an agent schedule.",
            "parameters" => $action_logs,
        ]);
        
    }

    public function addUser($data = [])
    {
        // data validation
        $action=null;
        $user_datani=[];
        $user_information = [];
        $hierarchy = [];
        $cluster=[];
        $user_benefits=[];
        if (!isset($data['id'])) {
            if (!isset($data['firstname'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "First Name is not set.",
                ]);
            }else{
                $user_information['firstname']= $data['firstname'];
            }
            if (!isset($data['middlename'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "middlename is not set.",
                ]);
            }else{
                $user_information['middlename']= $data['middlename'];
            }
            if (!isset($data['lastname'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "lastname is not set.",
                ]);
            }else{
                $user_information['lastname']= $data['lastname'];
            }
            if (!isset($data['birthdate'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "birthdate is not set.",
                ]);
            }else{
                $user_information['birthdate']= $data['birthdate'];
            }
            if (!isset($data['gender'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "gender is not set.",
                ]);
            }else{
                $user_information['gender']= $data['gender'];
            }    
            if (!isset($data['email'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "Email is not set.",
                ]);
            }if (!isset($data['access_id'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "access_id is not set.",
                ]);
            }

            if (isset($data['contact_number'])) {
                $user_information['contact_number']= $data['contact_number'];
            }
            if (isset($data['address'])) {
                $user_information['address']= $data['address'];
            }
            if (isset($data['salary_rate'])) {
                $user_information['salary_rate']= $data['salary_rate'];
            }
            if (isset($data['status'])) {
                $user_information['status']= $data['status'];
            }
            if (isset($data['hired_date'])) {
                $user_information['hired_date']= $data['hired_date'];
            }
            if (isset($data['separation_date'])) {
                $user_information['separation_date']= $data['separation_date'];
            }
            if (isset($data['excel_hash'])) {
                $user_information['excel_hash']= $data['excel_hash'];
            }
            if (isset($data['p_email'])) {
                $user_information['p_email']= $data['p_email'];
            }
            if (isset($data['status_reason'])) {
                $user_information['status_reason']= $data['status_reason'];
            }
            if (isset($data['imageName'])) {
                define('UPLOAD_DIR', 'storage/images/');
                $file =  request()->image->move(UPLOAD_DIR,$data['imageName']);
                $url= asset($file);
                $user_information['image_url']= $url;
            }
            $user_informations =  $this->user_infos->init($this->user_infos->pullFillable($user_information));
            $user_informations->save();
            $user_id= $user_informations->id;
            $hierarchy['child_id']= $user_id;
            if (isset($data['parent_id'])) {
                $hierarchy['parent_id']= $data['parent_id'];
            }
           $user_hierarchy= $this->access_level_hierarchy->init($this->access_level_hierarchy->pullFillable($hierarchy));
           $user_hierarchy->save();
           $user_data['uid']= $user_id;
            if (isset($data['email'])) {
                $user_data['email']= $data['email'];
            }
            if (isset($data['access_id'])) {
                $user_data['access_id']= $data['access_id'];
            }
            if (isset($data['company_id'])) {
                $user_data['company_id']= $data['company_id'];
            }
            if (isset($data['contract'])) {
                $user_data['contract']= $data['contract'];
            }
            $user_data['password'] = bcrypt(strtolower($data['firstname'].$data['lastname']));
            $users_data = $this->user_datum->init($this->user_datum->pullFillable($user_data));
           // $users_data->save();
            $benefits=[];
            $ben=[];
            $array=json_decode($data['benefits'], true );
            foreach($array as $key => $value ){
                    $ben['benefit_id'] = $key+1;
                    $ben['id_number'] = $value;
                    $ben['user_info_id'] = $user_id;
                    $user_ben = $this->user_benefits->init($this->user_benefits->pullFillable($ben));   
                    array_push($benefits,$user_ben);
                    $user_ben->save();   
            }  
            if (isset($data['status'])) {
            $status_logs['user_id']=$user_id;
            $status_logs['status']=$data['status'];
            }
            if (isset($data['reason'])) {
            $status_logs['reason']=$data['status'];
            }
            if (isset($data['status_reason'])) {
            $status_logs['reason']=$data['status_reason'];
            }
            if (isset($data['separation_date'])) {
            $status_logs['hired_date']=$data['hired_date'];
            }
            if (isset($data['separation_date'])) {
            $status_logs['separation_date']=$data['separation_date'];
            }
            $status = $this->user_status->init($this->user_status->pullFillable($status_logs)); 
            $status->save();         
        }else{
            if (isset($data['id'])) {   
                $does_exist = $this->users->find($data['id']);
                if (!$does_exist) {
                    return $this->setResponse([
                        'code'  => 500,
                        'title' => 'User Not Found.',
                    ]);
                }

            }
        }
            // if (isset($data['id'])) {
            //     $Users = $this->users->find($data['id']);
            //     $action="Updated";
            // } else{
            //     $data['password'] = bcrypt('password');
            //     $Users = $this->users->init($this->users->pullFillable($data));
            //     $user_information =  $this->user_infos->init($this->user_infos->pullFillable($data));
            //     $action="Added";
            // }
            
           

        if (!$users_data->save($data)) {
            return $this->setResponse([
                "code"        => 500,
                "title"       => "Data Validation Error.",
                "description" => "An error was detected on one of the inputted data.",
                "meta"        => [
                    "errors" => $Users->errors(),
                ],
            ]);
        }
        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully ".$action." a User.",
            "meta"        => [
                "user_information" => $user_informations,
                "user" => $users_data,
                "benefits" => $benefits
            ]
        ]);
        
    }

    public function updateStatus($data = [])
    {
        // data validation
        $action=null;
       
            if (!isset($data['status'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "status is not set.",
                ]);
            }
            if (!isset($data['user_id'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "user id is not set.",
                ]);
            }
            if (!isset($data['reason'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "reason is not set.",
                ]);
            }   

                $status = $this->user_status->init($this->user_status->pullFillable($data));
                $Users = $this->user_infos->find($data['user_id']);
                $Users->status=$data['status'];
                $Users->status_reason=$data['reason'];
                if(isset($data['hired_date'])){
                    $Users->hired_date=$data['hired_date'];
                }
                if(isset($data['separation_date'])){
                    $Users->separation_date=$data['separation_date'];
                }
                $action="Updated";
                if (!$Users->save($data)) {
                    return $this->setResponse([
                        "code"        => 500,
                        "title"       => "Data Validation Error on User.",
                        "description" => "An error was detected on one of the inputted data.",
                        "meta"        => [
                            "errors" => $Users->errors(),
                        ],
                    ]);
                }
                if (!$status->save($data)) {
                    return $this->setResponse([
                        "code"        => 500,
                        "title"       => "Data Validation Error.",
                        "description" => "An error was detected on one of the inputted data.",
                        "meta"        => [
                            "errors" => $status->errors(),
                        ],
                    ]);
                }
                return $this->setResponse([
                    "code"       => 200,
                    "title"      => "Successfully ".$action." a User Status.",
                    "meta"        => [
                        "Users" => $Users,
                        "Status Log" => $status
                    ]
                ]);
                    
        
        
    }

    public function bulkUpdateStatus($data = [])
    {
        // data validation
        $action=null;
        $array=json_decode($data['user_id'], true );
       $all_users=[];
        foreach ($array as $key => $value) {
            $data['user_id']=$value;          
            if (!isset($data['status'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "status is not set.",
                ]);
            }
            if (!isset($data['user_id'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "user id is not set.",
                ]);
            }
            if (!isset($data['reason'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "reason is not set.",
                ]);
            }   

                $status = $this->user_status->init($this->user_status->pullFillable($data));
                $Users = $this->user_infos->find($value);
                $Users->status=$data['status'];
                $Users->status_reason=$data['reason'];
                if(isset($data['hired_date'])){
                    $Users->hired_date=$data['hired_date'];
                }
                if(isset($data['separation_date'])){
                    $Users->separation_date=$data['separation_date'];
                }
                $action="Updated";
                if (!$Users->save($data)) {
                    return $this->setResponse([
                        "code"        => 500,
                        "title"       => "Data Validation Error on User.",
                        "description" => "An error was detected on one of the inputted data.",
                        "meta"        => [
                            "errors" => $Users->errors(),
                        ],
                    ]);
                }
                if (!$status->save($data)) {
                    return $this->setResponse([
                        "code"        => 500,
                        "title"       => "Data Validation Error.",
                        "description" => "An error was detected on one of the inputted data.",
                        "meta"        => [
                            "errors" => $status->errors(),
                        ],
                    ]);
                }
                array_push($all_users,$Users);
            }
                return $this->setResponse([
                    "code"       => 200,
                    "title"      => "Successfully ".$action." a Users Status.",
                    "meta"        => [
                        "Users" => $all_users
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
    public function getCluster($data = [])
    {
        $meta_index = "options";
        $parameters = [];
        $count      = 0;
         

        $count_data = $data;
        $data['relations'] = ["accesslevel","accesslevelhierarchy"];   
        $result = $this->fetchGeneric($data, $this->select_users);
        $results=[];
        $keys=0;
        $parent=null;
        foreach ($result as $key => $value) {
              if($value->accesslevelhierarchy->child_id==$data['id']){
                  $parent=$value->accesslevelhierarchy->parent_id;
                  array_push($results,$value);     
                foreach ($result as $key => $val) {
                    $last_child2=null;
                    if($val->accesslevelhierarchy->child_id==$parent){
                        $keys++;
                        $count++;  
                        array_push($results,$val);
                       
                        foreach ($result as $key => $vals) {
                            if($vals->accesslevelhierarchy->parent_id==$parent&&$vals->accesslevelhierarchy->child_id!=$data['id']){
                                $keys++;
                                $count++;  
                                array_push($results,$vals);                         
                        } 
                            $last_child2=$val->accesslevelhierarchy->parent_id;
                            if($vals->accesslevelhierarchy->child_id==$last_child2){
                                $keys++;
                                $count++;  
                                array_push($results,$vals);
                                
                            }
                          
        
                        } 
                        
                        
                    }

                } 
                
                $keys++;
                $count++;  
            }
            
         } 

        if (!$results) {
            return $this->setResponse([
                'code'       => 404,
                'title'      => "No users found",
                "meta"       => [
                    $meta_index => $results,
                ],
                "parameters" => $parameters,
            ]);
        }
       
        // $count = $this->countData($count_data, refresh_model($this->users->getModel()));

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully retrieved Users Cluster",
            "description"=>"Cluster",
            "meta"       => [
                $meta_index => $results,
                "count"     => $count
            ],
            
            
        ]);
    }



}
