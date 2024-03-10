<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * @author Yakubu Alhassan <yaqoubdramani@gmail.com>
 */
class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $response = Cache::remember('todos_data', 600, function () {
                return Http::get(config('json_faker.base_url') . 'todos')->json();
            });

            return response()->json([
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching todo data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'completed' => 'required|boolean'
            ]);
            $response = Http::post(config('json_faker.base_url') . 'todos', [
                'title' => $request->input('title'),
                'completed' => $request->input('completed'),
                'userId' => auth('api')->user()->id,
            ])->json();

            return response()->json([
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating a todo item: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $response = Cache::remember("todo_data_{$id}", 600, function () use ($id) {
                return Http::get(config('json_faker.base_url') . "todos/{$id}")->json();
            });

            return response()->json([
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching todo data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'filled|string',
                'completed' => 'filled|boolean',
            ]);

            $response = Http::put(config('json_faker.base_url') . "todos/{$id}", [
                'title' => $request->input('title'),
                'completed' => $request->input('completed'),
            ])->throw()->json();

            // Invalidate cache for this todo item
            Cache::forget("todo_data_{$id}");

            return response()->json([
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating todo data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            Http::delete(config('json_faker.base_url') . "todos/{$id}");

            Cache::forget("todo_data_{$id}");

            return response()->json(['message' => 'Data deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting todo data: ' . $e->getMessage()], 500);
        }
    }
}
