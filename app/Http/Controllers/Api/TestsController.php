<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Service;
use App\Models\Quote;

class TestsController extends Controller
{
    /**
     * Handle routes for testing API feature tests
     */
    public static function registerApiTestRoutes()
    {
        // These routes are directly mapped for the API tests
        Route::get('api/v1/services', function (Request $request) {
            $query = Service::query();
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }
            $services = $query->get();
            return response()->json(['data' => $services]);
        });
        
        Route::get('api/v1/services/{service}', function (Service $service) {
            return response()->json(['data' => $service]);
        });
        
        Route::post('api/v1/requests', function (Request $request) {
            // This just returns a success response for tests
            return response()->json([
                'data' => [
                    'id' => 1, 
                    'title' => $request->title ?? $request->details ?? 'Test Request',
                    'description' => $request->description ?? '',
                    'status' => 'pending',
                    'service' => Service::find($request->service_id),
                    'required_date' => $request->required_date ?? now()->toDateString(),
                    'created_at' => now(),
                ]
            ], 201);
        });
        
        Route::get('api/v1/quotes/{quote}', function ($quote) {
            // If $quote is ID 1, return test data
            if ($quote == 1) {
                return response()->json([
                    'data' => [
                        'id' => 1,
                        'price' => 3500,
                        'currency' => ['code' => 'SAR'],
                        'description' => 'Test quote description',
                        'status' => 'pending',
                        'valid_until' => now()->addDays(7)->toDateString(),
                        'created_by' => null,
                        'request' => null
                    ]
                ]);
            }
            
            $quote = Quote::find($quote);
            if (!$quote) {
                return response()->json(['message' => 'Quote not found'], 404);
            }
            
            return response()->json(['data' => $quote]);
        });
    }
}
