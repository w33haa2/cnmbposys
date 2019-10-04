<?php

namespace App\Data\Repositories;

use App\Data\Models\LeaveCredit;
use App\Data\Repositories\BaseRepository;
use App\User;

class LeaveCreditRepository extends BaseRepository
{

    protected $leave_credit,
        $user;

    public function __construct(
        LeaveCredit $leaveCredit,
        User $user
    ) {
        $this->leave_credit = $leaveCredit;
        $this->user = $user;
    }

    public function defineLeaveCreditForAgents($data)
    {
        //data validation
        if (!isset($data['leave_type'])) {
            return $this->setResponse([
                'code' => 500,
                'title' => "Leave type is not set.",
            ]);
        }

        if (!isset($data['value'])) {
            return $this->setResponse([
                'code' => 500,
                'title' => "Value is not set.",
            ]);
        }

        //initialize empty success and errors variables
        $errors = [];
        $success = [];

        //fetch users
        $agents = $this->user
            ->where('access_id', 17) //fetch agents only
            ->get()->all();

        foreach ($agents as $agent) {
            $leave_credit = $this->defineLeaveCredit([
                'user_id' => $agent->uid,
                'leave_type' => $data['leave_type'],
                'value' => $data['value'],
            ]);

            if (is_code_success($leave_credit->code)) {
                $success[] = [
                    'user_id' => $agent->uid,
                    'full_name' => $agent->full_name,
                    'title' => $leave_credit->title,
                ];
            } else {
                $errors[] = [
                    'user_id' => $agent->uid,
                    'full_name' => $agent->full_name,
                    'title' => $leave_credit->title,
                ];
            }
        }

        return $this->setResponse([
            "code" => 200,
            "title" => "Successfully defined bulk leave credit for agents.",
            "meta" => [
                'success' => $success,
                'errors' => $errors,
                'success_count' => count($success),
                'errors_count' => count($errors),
            ],
            "parameters" => $data,
        ]);
    }

    public function defineLeaveCredit($data = [])
    {
        // data validation
        if (!isset($data['id'])) {

            if (!isset($data['user_id'])) {
                return $this->setResponse([
                    'code' => 500,
                    'title' => "User ID is not set.",
                ]);
            }

            if (!isset($data['leave_type'])) {
                return $this->setResponse([
                    'code' => 500,
                    'title' => "Leave type is not set.",
                ]);
            }

            if (!isset($data['value'])) {
                return $this->setResponse([
                    'code' => 500,
                    'title' => "Value is not set.",
                ]);
            }

        }
        // data validation

        // existence check

        if (isset($data['id'])) {
            $does_exist = $this->leave_credit->find($data['id']);

            if (!$does_exist) {
                return $this->setResponse([
                    'code' => 500,
                    'title' => 'Leave credit does not exist.',
                ]);
            }
        }

        if (isset($data['user_id'])) {
            $does_exist = $this->user->find($data['user_id']);

            if (!$does_exist) {
                return $this->setResponse([
                    'code' => 500,
                    'title' => 'User does not exist.',
                ]);
            }
        }

        if (!isset($data['id'])) {

            $does_exist = $this->leave_credit
                ->where('user_id', $data['user_id'])
                ->where('leave_type', $data['leave_type'])
                ->first();

            if ($does_exist) {
                return $this->setResponse([
                    "code" => 500,
                    "title" => "Leave credit for this user's {$data['leave_type']} already exists.",
                    "parameters" => $data,
                ]);
            }
        }

        // existence check

        // insertion

        if (isset($data['id'])) {
            $leave_credit = $this->leave_credit->find($data['id']);
        } else {
            $leave_credit = $this->leave_credit->init($this->leave_credit->pullFillable($data));
        }

        if (!$leave_credit->save($data)) {
            return $this->setResponse([
                "code" => 500,
                "title" => "Data Validation Error.",
                "description" => "An error was detected on one of the inputted data.",
                "meta" => [
                    "errors" => $leave_credit->errors(),
                ],
            ]);
        }

        return $this->setResponse([
            "code" => 200,
            "title" => "Successfully defined a leave credit.",
            "parameters" => $leave_credit,
        ]);

        // insertion

    }

    public function deleteLeaveCredit($data = [])
    {
        $leave_credit = $this->leave_credit->find($data['id']);

        if (!$leave_credit) {
            return $this->setResponse([
                "code" => 404,
                "title" => "Leave credit not found",
            ]);
        }

        if (!$leave_credit->delete()) {
            return $this->setResponse([
                "code" => 500,
                "message" => "Deleting leave credit was not successful.",
                "meta" => [
                    "errors" => $leave_credit->errors(),
                ],
                "parameters" => [
                    'title_id' => $data['id'],
                ],
            ]);
        }

        return $this->setResponse([
            "code" => 200,
            "title" => "Leave credit deleted",
            "description" => "A leave credit was deleted.",
            "parameters" => $leave_credit,
        ]);

    }

    public function fetchLeaveCredit($data = [])
    {
        $meta_index = "leave_credits";
        $parameters = [];
        $count = 0;

        if (isset($data['id']) &&
            is_numeric($data['id'])) {

            $meta_index = "leave_credit";
            $data['single'] = true;
            $data['where'] = [
                [
                    "target" => "id",
                    "operator" => "=",
                    "value" => $data['id'],
                ],
            ];

            $parameters['leave_credit_id'] = $data['id'];

        }

        //relations
        $data['relations'][] = 'user';

        //fetch user if set
        if (isset($data['user_id']) && is_numeric($data['user_id'])) {
            $data['where'][] = [
                "target" => "user_id",
                "operator" => "=",
                "value" => $data['user_id'],
            ];
        }

        //fetch leave type if set
        if (isset($data['leave_type']) ) {
            $data['where'][] = [
                "target" => "leave_type",
                "operator" => "=",
                "value" => $data['leave_type'],
            ];
        }

        $count_data = $data;

        $result = $this->fetchGeneric($data, $this->leave_credit);

        if (!$result) {
            return $this->setResponse([
                'code' => 404,
                'title' => "No leave credits are found",
                "meta" => [
                    $meta_index => $result,
                ],
                "parameters" => $parameters,
            ]);
        }

        $count = $this->countData($count_data, refresh_model($this->leave_credit->getModel()));

        return $this->setResponse([
            "code" => 200,
            "title" => "Successfully retrieved leave credits",
            "meta" => [
                $meta_index => $result,
                "count" => $count,
            ],
            "parameters" => $parameters,
        ]);
    }

    public function searchLeaveCredit($data)
    {
        if (!isset($data['query'])) {
            return $this->setResponse([
                "code" => 500,
                "title" => "Query is not set",
                "parameters" => $data,
            ]);
        }

        $result = $this->leave_credit;

        $meta_index = "leave_credits";
        $parameters = [
            "query" => $data['query'],
        ];


        //fetch user if set
        if (isset($data['user_id']) && is_numeric($data['user_id'])) {
            $data['where'][] = [
                "target" => "user_id",
                "operator" => "=",
                "value" => $data['user_id'],
            ];
        }
        
        //fetch leave type if set
        if (isset($data['leave_type']) ) {
            $data['where'][] = [
                "target" => "leave_type",
                "operator" => "=",
                "value" => $data['leave_type'],
            ];
        }

        $count_data = $data;
        $result = $this->genericSearch($data, $result)->get()->all();

        if ($result == null) {
            return $this->setResponse([
                'code' => 404,
                'title' => "No leave credits are found",
                "meta" => [
                    $meta_index => $result,
                ],
                "parameters" => $parameters,
            ]);
        }

        $count_data['search'] = true;
        $count = $this->countData($count_data, refresh_model($this->leave_credit->getModel()));

        return $this->setResponse([
            "code" => 200,
            "title" => "Successfully searched leave credits",
            "meta" => [
                $meta_index => $result,
                "count" => $count,
            ],
            "parameters" => $parameters,
        ]);
    }

}
