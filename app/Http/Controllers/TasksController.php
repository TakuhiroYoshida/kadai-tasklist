<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::check()){
            // 認証済みユーザーを取得
            $user = \Auth::user();
            
            // ユーザーの投稿の一覧を取得
            $tasks = $user->tasks()->get();
        }
        
        
        // タスク一覧ビューでそれを表示
        return view('tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $task = new Task;
        // タスク作成ビューを表示
        return view('tasks.create', [
                'task' => $task,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
        //タスクを作成
        $task = new Task;
        $user = \Auth::user();
        $task->user_id = $user->id;
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();
        
        // トップページへリダイレクトさせる
        return redirect('/tasks');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザー（閲覧者）がその投稿の所有者である場合はタスク詳細ビューでそれを表示
        if (\Auth::id() === $task->user_id){
        return view('tasks.show', [
            'task' => $task, 
        ]);
        }
        else {
            // トップページへリダイレクトさせる
            return redirect('/tasks');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザー（閲覧者）がその投稿の所有者である場合はタスク編集ビューでそれを表示
        if (\Auth::id() === $task->user_id){
        return view('tasks.edit', [
            'task' => $task,
        ]);
        }
        
        else {
            // トップページへリダイレクトさせる
            return redirect('/tasks');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        // 認証済みユーザー（閲覧者）がその投稿の所有者である場合はタスクを更新
        if (\Auth::id() === $task->user_id){
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();
        // トップページへリダイレクトさせる
        return redirect('/tasks');
        }

        // トップページへリダイレクトさせる
        return redirect('/tasks');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザー（閲覧者）がその投稿の所有者である場合は投稿を削除
        if (\Auth::id() === $task->user_id){
            $task->delete();
            // トップページへリダイレクトさせる
            return redirect('/tasks');
        }
        

        // トップページへリダイレクトさせる
        return redirect('/tasks');
    }
}
