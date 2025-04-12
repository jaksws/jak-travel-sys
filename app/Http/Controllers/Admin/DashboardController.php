<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        // The middleware is already applied in the route definitions
        // $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        // Get basic system statistics
        $stats = [
            'users_count' => DB::table('users')->count(),
            'agencies_count' => $this->getAgenciesCount(),
            'services_count' => $this->getServicesCount(),
            'requests_count' => $this->getRequestsCount(),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }
    
    /**
     * Show users management page
     */
    public function users()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }
    
    /**
     * Show system logs
     */
    public function logs()
    {
        return view('admin.system.logs');
    }
    
    /**
     * Show requests management page
     */
    public function requests()
    {
        // Obtener todas las solicitudes con sus relaciones
        $requests = \App\Models\Request::select(['id', 'title', 'status'])
            ->with(['service', 'user'])
            ->latest()
            ->paginate(15);
            
        // Forzar la asignación de la variable a la vista mediante array asociativo
        return view('admin.requests.index', ['requests' => $requests]);
    }
    
    /**
     * Get agencies count
     */
    private function getAgenciesCount()
    {
        try {
            return DB::table('agencies')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get services count
     */
    private function getServicesCount()
    {
        try {
            return DB::table('services')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get requests count
     */
    private function getRequestsCount()
    {
        try {
            return DB::table('requests')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
