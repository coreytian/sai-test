<?php

namespace App\Http\Controllers;

use App\File;
use App\Mail\UploadNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|mimes:pdf,txt,docx,xml,csv|max:10000'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->messages()->get('document')], 400);
        }

        if($request->hasFile('document')) {
            $fileName = $request->file('document')->getClientOriginalName();
            $fileSize = $request->file('document')->getSize();
            $uploadResult = $request->file('document')->storeAs('', uniqid().'_'.$fileName, 's3');
        } else {
            return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }

        if($uploadResult === false){
            return response()->json(['success' => false, 'message' => 'Upload to S3 failed, please try again later.'], 400);
        }

        $file = new File();
        $file->size = $fileSize;
        $file->name = $fileName;
        $file->s3_id = $uploadResult;
        $file->save();

        $data = ['file_name'=>$fileName, 'file_size'=>$fileSize, 's3_key'=>$uploadResult];
        Mail::to(config('mail.uploadNotificationTo'))->queue(new UploadNotification($data));

        return response()->json(['success' => true, 'message' => 'File successfully uploaded'], 200);
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
    public function destroy($id)
    {
        //
    }
}
