<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Event;
class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Page/Meta - Tasks';
        $tasks = Event::all();
        return view('admin.dashboard.tasks.index', compact('tasks'))->with('page_title', $title);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'start_date' => 'required',
        ]);
        if($request->id==0){
            Event::create([
                'name' => $request->name,
                'desc' => $request->desc,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'color' => $request->textcolor,
                'lable_bg' => $request->lablecolor
            ]);
        }else{
            $task = Event::find($request->id);
            $task->name = $request->name;
            $task->desc = $request->desc;
            $task->start_date = $request->start_date;
            $task->end_date = $request->end_date;
            $task->color = $request->textcolor;
            $task->lable_bg = $request->lablecolor;
            $task->save();
        }
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Event::where('id',$request->id)->delete();
        return redirect()->back();
    }
}
