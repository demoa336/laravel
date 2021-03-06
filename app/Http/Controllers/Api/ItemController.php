<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Item;
use File;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Item::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->hasFile('file')) {
            $path = $request->file('file')->storeAs(
                'items', str_replace(' ', '_', $request->file('file')->getClientOriginalName())
            );

            $request->request->add(['image' => $path]);
        }

        Item::create($request->all());
        return response()->json(["message" => "Successfully Inserted!"]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Item::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $item = Item::find($request->id);
        if($request->hasFile('file')) {

            $file = storage_path('app/public')."/".$item->file;
            if(File::exists( $file )) {
                File::delete($file);
            }

            $path = $request->file('file')->storeAs(
                'items', $request->id.".".str_replace(' ', '_', $request->file('file')->getClientOriginalName())
            );

            $request->request->add(['image' => $path]);
        }

        Item::where('id', $request->id)->update($request->except('_method', 'file'));
        return response()->json(["message" => "Successfully Updated!"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $item = Item::find($request->id);
        $file = storage_path('app/public')."/".$item->file;
        if(File::exists( $file )) {
            File::delete($file);
        }
        $item->delete();

        return response()->json(["message" => "Successfully Deleted!"]);
    }
}
