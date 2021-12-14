<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // User tasks
    public function tasks(Request $request) {
        if(!$request->completed){
            $tasks = Task::where('user_id', $request->user()->id)->get();
            return response()->json([
                $tasks
            ], 200);

        }
        else if($request->completed == 'false' || $request->completed == 'true'){

            $tasks = Task::where('user_id', $request->user()->id)->where('completed', $request->completed)->get();
            return response()->json([
                $tasks
            ], 200);
        }
        else{
            return response()->json([
                'page not found'
            ], 404);
        }

        /**
         * @OA\Get(path="/api/tasks",
         *   tags={"tasks"},
         *   summary="User tasks",
         *   description="User tasks",
         *   operationId="UserTasks",
         *   security={ {"bearerAuth": {}} },
         *  @OA\Response(
         *    response=200,
         *    description="Success",
         *    @OA\JsonContent(
         *       @OA\Property(property="name", type="string", example="Task 1"),
         *        )
         *     ),
         *    @OA\Response(
         *    response=401,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Unauthorized"),
         *        )
         *     ),
         * )
         */

        /**
         * @OA\Get(path="/api/tasks?completed={completed}",
         *   tags={"tasks"},
         *   summary="User tasks",
         *   description="User tasks",
         *   operationId="UserTasks",
         *   security={ {"bearerAuth": {}} },
         *   @OA\Parameter(
         *    description="True or false completed",
         *    in="path",
         *    name="completed",
         *    example="true",
         *    @OA\Schema(
         *       type="boolean"
         *    )
         * ),
         *  @OA\Response(
         *    response=200,
         *    description="Success",
         *    @OA\JsonContent(
         *       @OA\Property(property="name", type="string", example="Task 1"),
         *        )
         *     ),
         *    @OA\Response(
         *    response=401,
         *    description="error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Unauthorized"),
         *        )
         *     ),
         * )
         */
    }

    // Create a task
    public function createTask(Request $request) {
        $request->validate([
            'body' => 'required'
        ]);

        $task = Task::create([
            'body' => $request->body,
            'user_id'=>$request->user()->id,
        ]);

        if(!$request->body){
            return response()->json([
                "success"=> false,
                "msg"=> "This field is required"
            ], 400);
        }

        return response()->json([
            $task
        ], 201);

        /**
         * @OA\Post(path="/api/createTask",
         *   summary="Create a task",
         *   tags={"tasks"},
         *   description="Create a task",
         *   operationId="createTask",
         *   security={ {"bearerAuth": {}} },
         * @OA\RequestBody(
         *    required=true,
         *    description="Body",
         *    @OA\JsonContent(
         *       required={"body"},
         *       @OA\Property(property="body", type="string", example="Task 1"),
         *    ),
         * ),
         *  @OA\Response(
         *    response=201,
         *    description="Success",
         *    @OA\JsonContent(
         *       @OA\Property(property="name", type="string", example="Task 1"),
         *        )
         *     ),
         *   @OA\Response(
         *    response=400,
         *    description="error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="This field is required"),
         *        )
         *     ),
         *    @OA\Response(
         *    response=401,
         *    description="error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Unauthorized"),
         *        )
         *     ),
         * )
         */
    }

    // Update a task
    public function updateTask(Request $request, $id) {
        $task = Task::find($id);

        if(!$task){
            return response()->json([
                "message"=> "Undefined"
            ], 404);
        }

        if($request->user()->id != $task->user_id){
            return response()->json([
                "message"=> "Forbidden"
            ], 403);
        }

        $request->validate([
            'body' => 'required'
        ]);

        $task_updated = Task::where('id', $id)->update([
            'body'=> $request->body
        ]);

        return response()->json([
            "success"=>true
        ], 200);

        /**
         * @OA\Put(path="/api/updateTask/{id}",
         *   tags={"tasks"},
         *   summary="Update a task",
         *   description="Update a task",
         *   operationId="updateTask",
         *   security={ {"bearerAuth": {}} },
         *   @OA\RequestBody(
         *    required=true,
         *    description="Body",
         *    @OA\JsonContent(
         *       required={"body"},
         *       @OA\Property(property="body", type="string", example="Task 1"),
         *    ),
         * ),
         * @OA\Parameter(
         *    description="Task id",
         *    in="path",
         *    name="id",
         *    required=true,
         *    example="1",
         *    @OA\Schema(
         *       type="integer",
         *       format="int64",
         *      example="1"
         *    )
         * ),
         *  @OA\Response(
         *    response=200,
         *    description="Success",
         *    @OA\JsonContent(
         *       @OA\Property(property="name", type="string", example="Task 1"),
         *        )
         *     ),
         *   @OA\Response(
         *    response=400,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="This field is required"),
         *        )
         *     ),
         *    @OA\Response(
         *    response=401,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Unauthorized"),
         *        )
         *     ),
         *    @OA\Response(
         *    response=403,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Forbidden"),
         *        )
         *     ),
         * )
         */
    }

    // Complete a task
    public function completeTask(Request $request, $id) {

        $task = Task::find($id);
        if(!$task){
            return response()->json([
                "message"=> "Tache innexistante"
            ], 404);
        }
        if($request->user()->id != $task->user_id){
            return response()->json([
                "message"=> "Accès interdit!"
            ], 403);
        }

        $task_completed = Task::where('id', $id)->update([
            'completed'=> true
        ]);

        return response()->json([
            "success"=>true
        ], 200);

        /**
         * @OA\Get(path="/api/completeTask/{id}",
         *   tags={"tasks"},
         *   summary="Complete a task",
         *   description="Complete a task",
         *   operationId="completeTask",
         *   security={ {"bearerAuth": {}} },
         * @OA\Parameter(
         *    description="ID of task",
         *    in="path",
         *    name="id",
         *    required=true,
         *    example="1",
         *    @OA\Schema(
         *       type="integer",
         *       format="int64",
         *      example="1"
         *    )
         * ),
         *  @OA\Response(
         *    response=200,
         *    description="Success",
         *    @OA\JsonContent(
         *       @OA\Property(property="success", type="string", example="true"),
         *        )
         *     ),
         *    @OA\Response(
         *    response=401,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Unauthorized"),
         *        )
         *     ),
         *    @OA\Response(
         *    response=403,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Forbidden"),
         *        )
         *     ),
         * )
         */
    }

    // Delete a task
    public function deleteTask(Request $request, $id) {
        $task = Task::find($id);

        if(!$task){
            return response()->json([
                "message"=> "Tache innexistante"
            ], 404);
        }

        if($request->user()->id != $task->user_id){
            return response()->json([
                "message"=> "Forbidden"
            ], 403);
        }

        $task_deleted = Task::where('id', $id)->delete();

        return response()->json([
            "success"=>true
        ], 200);

        /**
         * @OA\Delete(path="/api/deleteTask/{id}",
         *   tags={"tasks"},
         *   summary="Delete a task",
         *   description="Delete a task",
         *   operationId="deleteTask",
         *   security={ {"bearerAuth": {}} },
         * @OA\Parameter(
         *    description="ID of task",
         *    in="path",
         *    name="id",
         *    required=true,
         *    example="1",
         *    @OA\Schema(
         *       type="integer",
         *       format="int64",
         *      example="1"
         *    )
         * ),
         *  @OA\Response(
         *    response=200,
         *    description="Success",
         *    @OA\JsonContent(
         *       @OA\Property(
         *     property="success", type="string", example="true"),
         *        )
         *     ),
         *    @OA\Response(
         *    response=401,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Unauthorized"),
         *        )
         *     ),
         *    @OA\Response(
         *    response=403,
         *    description="Error",
         *    @OA\JsonContent(
         *       @OA\Property(property="msg", type="string", example="Forbidden"),
         *        )
         *     ),
         * )
         */
    }
}
