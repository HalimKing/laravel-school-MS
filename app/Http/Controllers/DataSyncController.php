<?php

namespace App\Http\Controllers;

use App\Services\DataSynchronizer;
use Illuminate\Http\Request;

class DataSyncController extends Controller
{
    protected $synchronizer;

    public function __construct()
    {
        $this->synchronizer = new DataSynchronizer();
    }

    /**
     * Show sync status page
     */
    public function index()
    {
        return view('admin.data-sync.index');
    }

    /**
     * Sync teachers
     */
    public function syncTeachers(Request $request)
    {
        $overwrite = $request->boolean('overwrite', false);

        try {
            $result = $this->synchronizer->syncTeachers($overwrite);

            return redirect()->back()->with([
                'success' => "Teachers synchronized successfully!",
                'sync_result' => [
                    'type' => 'Teachers',
                    'synced' => $result['synced'],
                    'updated' => $result['updated'],
                    'skipped' => $result['skipped'],
                    'total' => $result['total'],
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error syncing teachers: ' . $e->getMessage());
        }
    }

    /**
     * Sync guardians
     */
    public function syncGuardians(Request $request)
    {
        $overwrite = $request->boolean('overwrite', false);

        try {
            $result = $this->synchronizer->syncGuardians($overwrite);

            return redirect()->back()->with([
                'success' => "Guardians synchronized successfully!",
                'sync_result' => [
                    'type' => 'Guardians/Parents',
                    'synced' => $result['synced'],
                    'updated' => $result['updated'],
                    'skipped' => $result['skipped'],
                    'total' => $result['total'],
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error syncing guardians: ' . $e->getMessage());
        }
    }

    /**
     * Sync students
     */
    public function syncStudents(Request $request)
    {
        $overwrite = $request->boolean('overwrite', false);

        try {
            $result = $this->synchronizer->syncStudents($overwrite);

            return redirect()->back()->with([
                'success' => "Students synchronized successfully!",
                'sync_result' => [
                    'type' => 'Students',
                    'synced' => $result['synced'],
                    'updated' => $result['updated'],
                    'skipped' => $result['skipped'],
                    'total' => $result['total'],
                ]
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error syncing students: ' . $e->getMessage());
        }
    }

    /**
     * Sync all data
     */
    public function syncAll(Request $request)
    {
        $overwrite = $request->boolean('overwrite', false);

        try {
            $results = $this->synchronizer->syncAll($overwrite);

            $message = "All data synchronized successfully!<br>";
            $message .= "Teachers: {$results['teachers']['synced']} created, {$results['teachers']['updated']} updated<br>";
            $message .= "Guardians: {$results['guardians']['synced']} created, {$results['guardians']['updated']} updated<br>";
            $message .= "Students: {$results['students']['synced']} created, {$results['students']['updated']} updated";

            return redirect()->back()->with([
                'success' => $message,
                'sync_results' => $results,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error syncing data: ' . $e->getMessage());
        }
    }

    /**
     * Check for duplicates
     */
    public function checkDuplicates()
    {
        try {
            $duplicates = $this->synchronizer->checkDuplicates();

            return redirect()->back()->with([
                'info' => 'Duplicate check completed',
                'duplicates' => $duplicates,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error checking duplicates: ' . $e->getMessage());
        }
    }
}
