<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CardActivation;
use App\Models\KycVerification;
use App\Models\Payment;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ApiAdminController extends Controller
{
    /**
     * Get dashboard statistics.
     *
     * GET /api/admin/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $totalUsers = User::count();
        $kycPending = KycVerification::where('status', 'Pending')->count();
        $cardPurchasesPending = Payment::where('type', 'card')->where('status', 'Pending')->count();
        $cardActivationsTotal = CardActivation::count();
        $cardLoadsPending = Payment::whereIn('type', ['load', 'USDT'])->where('status', 'Pending')->count();
        $supportTicketsOpen = SupportTicket::where('status', 'open')->count();

        // Today's volume
        $todayVolume = Payment::whereDate('created_at', today())
            ->where('type', 'card')
            ->sum('trans_amount');

        return response()->json([
            'total_users'             => $totalUsers,
            'kyc_pending'             => $kycPending,
            'card_purchases_pending'  => $cardPurchasesPending,
            'card_activations_total'  => $cardActivationsTotal,
            'card_loads_pending'      => $cardLoadsPending,
            'support_tickets_open'    => $supportTicketsOpen,
            'today_volume_usdt'       => (float) $todayVolume,
        ]);
    }

    /**
     * KYC report.
     *
     * GET /api/admin/reports/kyc
     */
    public function kycReport(Request $request): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $range = $request->get('range', 'all');
        $query = KycVerification::query();

        if ($range === 'weekly') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($range === 'monthly') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($range === 'daily') {
            $query->whereDate('created_at', today());
        }

        $total = (clone $query)->count();
        $approved = (clone $query)->where('status', 'Approved')->count();
        $rejected = (clone $query)->where('status', 'Rejected')->count();
        $pending = (clone $query)->where('status', 'Pending')->count();
        $inProcess = (clone $query)->where('status', 'In Process')->count();

        return response()->json([
            'total'     => $total,
            'approved'  => $approved,
            'rejected'  => $rejected,
            'pending'   => $pending,
            'in_process' => $inProcess,
        ]);
    }

    /**
     * Transaction report.
     *
     * GET /api/admin/reports/transactions
     */
    public function transactionReport(Request $request): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $range = $request->get('range', 'all');
        $query = Payment::query();

        if ($range === 'weekly') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($range === 'monthly') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($range === 'daily') {
            $query->whereDate('created_at', today());
        }

        $totalVolume = (clone $query)->sum('trans_amount');
        $totalFees = (clone $query)->sum('trans_fee');
        $transactionCount = (clone $query)->count();

        return response()->json([
            'total_volume'      => (float) $totalVolume,
            'total_fees'        => (float) $totalFees,
            'transaction_count' => $transactionCount,
        ]);
    }

    /**
     * System health check.
     *
     * GET /api/admin/health
     */
    public function health(Request $request): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $healthStatus = 'healthy';
        $checks = [];

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (\Exception $e) {
            $checks['database'] = 'error: ' . $e->getMessage();
            $healthStatus = 'degraded';
        }

        // NECard API checks (config-based)
        $checks['necard_api_visa'] = config('app.necard_api_url') ? 'ok' : 'not_configured';
        $checks['necard_api_mastercard'] = config('app.necard_api_url_mc') ? 'ok' : 'not_configured';

        // Heleket gateway check
        $checks['heleket_gateway'] = config('app.usdt-api-url') ? 'ok' : 'not_configured';

        // Email service check (just config)
        $checks['email_service'] = config('mail.default') !== 'log' ? 'ok' : 'using_log_driver';

        // Uptime
        $uptimeSeconds = -1;
        if (file_exists('/proc/uptime')) {
            $uptime = file_get_contents('/proc/uptime');
            $uptimeSeconds = (int) floor((float) $uptime);
        }

        return response()->json([
            'status'              => $healthStatus,
            'database'            => $checks['database'],
            'necard_api_visa'     => $checks['necard_api_visa'],
            'necard_api_mastercard' => $checks['necard_api_mastercard'],
            'heleket_gateway'     => $checks['heleket_gateway'],
            'email_service'       => $checks['email_service'],
            'uptime_seconds'      => $uptimeSeconds,
        ]);
    }

    /**
     * Trigger database backup.
     *
     * POST /api/admin/backup
     */
    public function backup(Request $request): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $backupFile = 'backup_' . now()->format('Y-m-d_His') . '.sql.gz';

        try {
            // Attempt to run the backup if a backup command is configured
            if (Artisan::has('backup:run')) {
                Artisan::call('backup:run', [
                    '--filename' => $backupFile,
                ]);
            } else {
                // Fallback: use mysqldump if available
                $dbName = config('database.connections.mysql.database');
                $dbUser = config('database.connections.mysql.username');
                $dbPass = config('database.connections.mysql.password');
                $dbHost = config('database.connections.mysql.host');
                $backupPath = storage_path('app/backups/' . $backupFile);

                if (! is_dir(dirname($backupPath))) {
                    mkdir(dirname($backupPath), 0755, true);
                }

                $command = sprintf(
                    'mysqldump -h %s -u %s %s %s 2>/dev/null | gzip > %s',
                    escapeshellarg($dbHost),
                    escapeshellarg($dbUser),
                    $dbPass ? '-p' . escapeshellarg($dbPass) : '',
                    escapeshellarg($dbName),
                    escapeshellarg($backupPath)
                );

                exec($command, $output, $exitCode);

                if ($exitCode !== 0) {
                    throw new \Exception('mysqldump failed with exit code ' . $exitCode);
                }
            }

            return response()->json([
                'message' => 'Backup initiated',
                'file'    => $backupFile,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Backup failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
