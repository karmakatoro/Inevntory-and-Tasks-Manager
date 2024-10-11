<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

class FunctionsController extends Controller
{
    public function store_file($file, $path)
    {
        $fileName = time().'.'.$file->getClientOriginalExtension();
        $file->storeAs($path, $fileName, 'public');

        $filePath = 'storage/'.$path.'/'.$fileName;

        return $filePath;
    }

    public function store_multiples_file($files, $path)
    {
        $filesTab = [];
        foreach ($files as $file) {
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $file->storeAs($path, $fileName, 'public');

            $filePath = 'storage/'.$path.'/'.$fileName;
            $filesTab[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $filePath,
            ];
        }

        return json_encode($filesTab);
    }

    public function delete_row(Model $model)
    {
        $deleted = $model->delete();
        if ($deleted) {
            return response()->json([
                'status' => true,
                'message' => 'Successful supression',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ]);
        }

    }
}
