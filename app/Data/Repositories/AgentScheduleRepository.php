<?php
/**
 * Created by PhpStorm.
 * User: Janrey
 * Date: 30/10/2018
 * Time: 2:12 PM
 */

namespace App\Data\Repositories;

use App\Data\Models\AgentSchedule;
use App\Data\Models\UserInfo;
use App\User;
use App\Data\Repositories\BaseRepository;

class AgentScheduleRepository extends BaseRepository
{

    protected 
        $agent_schedule,
        $user;

    public function __construct(
        AgentSchedule $agentSchedule,
        User $user
    ) {
        $this->agent_schedule = $agentSchedule;
        $this->user = $user;
    }

    public function defineAgentSchedule($data = [])
    {
        // data validation
        if (!isset($data['id'])) {

            if (!isset($data['user_id']) ||
                !is_numeric($data['user_id']) ||
                $data['user_id'] <= 0) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "User ID is not set.",
                ]);
            }

            if (!isset($data['title'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "Title is not set.",
                ]);
            }

            if (!isset($data['start_event'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "Start date is not set.",
                ]);
            }

            if (!isset($data['end_event'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "End date is not set.",
                ]);
            }

        }
        // data validation

        // existence check

        if (isset($data['user_id'])) {
            if (!UserInfo::find($data['user_id'])) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => "User ID is not available.",
                ]);
            }
        }

        if (isset($data['id'])) {
            $does_exist = $this->agent_schedule->find($data['id']);

            if (!$does_exist) {
                return $this->setResponse([
                    'code'  => 500,
                    'title' => 'Agent Schedule ID does not exist.',
                ]);
            }
        }

        // existence check

        // insertion

        if (isset($data['id'])) {
            $agent_schedule = $this->agent_schedule->find($data['id']);
        } else {
            $agent_schedule = $this->agent_schedule->init($this->agent_schedule->pullFillable($data));
        }

        if (!$agent_schedule->save($data)) {
            return $this->setResponse([
                "code"        => 500,
                "title"       => "Data Validation Error.",
                "description" => "An error was detected on one of the inputted data.",
                "meta"        => [
                    "errors" => $agent_schedule->errors(),
                ],
            ]);
        }

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully defined an agent schedule.",
            "parameters" => $agent_schedule,
        ]);

        // insertion

    }

    public function deleteAgentSchedule($data = [])
    {
        $record = $this->agent_schedule->find($data['id']);

        if (!$record) {
            return $this->setResponse([
                "code"        => 404,
                "title"       => "Agent schedule not found"
            ]);
        }

        if (!$record->delete()) {
            return $this->setResponse([
                "code"    => 500,
                "message" => "Deleting agent schedule was not successful.",
                "meta"    => [
                    "errors" => $record->errors(),
                ],
                "parameters" => [
                    'schedule_id' => $data['id']
                ]
            ]);
        }

        return $this->setResponse([
            "code"        => 200,
            "title"       => "Agent schedule deleted",
            "description" => "An agent schedule was deleted.",
            "parameters"        => [
                "schedule_id" => $data['id']
            ]
        ]);

    }

    public function fetchAgentSchedule($data = [])
    {
        $meta_index = "agent_schedules";
        $parameters = [];
        $count      = 0;

        if (isset($data['id']) &&
            is_numeric($data['id'])) {

            $meta_index     = "agent_schedule";
            $data['single'] = true;
            $data['where']  = [
                [
                    "target"   => "id",
                    "operator" => "=",
                    "value"    => $data['id'],
                ],
            ];

            $parameters['agent_schedule_id'] = $data['id'];

        }

        $count_data = $data;

        $data['relations'] = "user_info";

        $result     = $this->fetchGeneric($data, $this->agent_schedule);

        if (!$result) {
            return $this->setResponse([
                'code'       => 404,
                'title'      => "No agent schedules are found",
                "meta"       => [
                    $meta_index => $result,
                ],
                "parameters" => $parameters,
            ]);
        }

        $count = $this->countData($count_data, refresh_model($this->agent_schedule->getModel()));

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully retrieved agent schedules",
            "meta"       => [
                $meta_index => $result,
                "count"     => $count,
            ],
            "parameters" => $parameters,
        ]);
    }

    public function fetchAllAgentsWithSchedule($data = [])
    {
        $meta_index = "agents";
        $parameters = [];
        $count      = 0;

        if (isset($data['id']) &&
            is_numeric($data['id'])) {

            $meta_index     = "agent";
            $data['single'] = true;
            $data['where']  = [
                [
                    "target"   => "id",
                    "operator" => "=",
                    "value"    => $data['id'],
                ],
            ];

            $parameters['agent_id'] = $data['id'];

        }

        $count_data = $data;

        $data['relations'] = "schedule";

        $result     = $this->fetchGeneric($data, $this->user);

        if (!$result) {
            return $this->setResponse([
                'code'       => 404,
                'title'      => "No agent schedules are found",
                "meta"       => [
                    $meta_index => $result,
                ],
                "parameters" => $parameters,
            ]);
        }

        $count = $this->countData($count_data, refresh_model($this->agent_schedule->getModel()));

        return $this->setResponse([
            "code"       => 200,
            "title"      => "Successfully retrieved agent schedules",
            "meta"       => [
                $meta_index => $result,
                "count"     => $count,
            ],
            "parameters" => $parameters,
        ]);
    }

}