<?php

namespace App\Http\Controllers;

use Bo\Car\Models\Car;
use Bo\Medias\Models\Medias;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function syncData(Request $request)
    {
        if ($request->header("FROM_SYNC") != "scrape_data_car") {
            return [
                "message"     => "Nguồn api không đúng mục đích",
                "error"       => true,
                "status_code" => 400,
            ];
        }

        $validator = Validator::make($request->all(), [
            'license_plates' => 'required|string|min:5',
            'description'    => 'required|string|min:5',
            'source'         => 'nullable|string|min:5',
            'medias'         => 'required|array',
            'medias.*'       => 'required|string',
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 422);
        }

        try {
            DB::beginTransaction();

            if (Car::where('license_plates', $request->input('license_plates'))->exists()) {
                return [
                    "message"     => 'Đã đồng bộ data này',
                    "error"       => true,
                    "status_code" => 400,
                ];
            }

            $car = Car::create([
                'license_plates' => $request->input('license_plates'),
                'description'    => $request->input('description'),
                'source'         => $request->input('source'),
            ]);

            $medias_upload = [];
            foreach ($request->input('medias', []) as $media) {
                $media = json_decode($media, true);
                $medias_upload[] = Medias::create([
                    "source_id"   => $car->id,
                    "type"        => $media["type"],
                    "target"      => $media["target"],
                    "target_data" => json_encode($media)
                ]);
            }
            DB::commit();

            return [
                "message"     => "Đồng bộ data thành công",
                "error"       => false,
                "status_code" => 200,
                "data"        => [
                    "car"    => $car,
                    "medias" => $medias_upload,
                ]
            ];
        } catch (Exception $exception) {
            DB::rollBack();
            return [
                "message"     => $exception->getMessage(),
                "error"       => true,
                "status_code" => 500,
            ];
        }
    }
}
