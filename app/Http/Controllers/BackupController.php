<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use ZipArchive;
class BackupController extends Controller
{
    public function index()
    {
        // $breadcrumbs = [
        //     ['name' => 'Home', 'url' => '/dashboard'],
        //     ['name' => 'Backup', 'url' => '/backup']
        // ];
        return Inertia::render("Backup", [
            // 'breadcrumbs' => $breadcrumbs,

        ]);
    }
    public function backupDatabase(Request $request)
    {
        $backupPath = storage_path("app/backup");
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        // ✅ Always overwrite the database backup
        $databaseBackupFile = "$backupPath/superstore_db.sql";

        if (function_exists('shell_exec')) {
            $mysqldumpCommand = "C:/xampp/mysql/bin/mysqldump -h localhost -u root superstore > \"$databaseBackupFile\"";
            shell_exec($mysqldumpCommand);
        }

        return response()->json(['success' => 'Database backup completed successfully!']);
    }

    public function backupProjectFiles(Request $request)
    {
        $backupPath = storage_path("app/backup");
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        // ✅ Always overwrite the project backup
        $projectBackupFile = "$backupPath/laravel_backup.zip";

        $zip = new ZipArchive();
        if ($zip->open($projectBackupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $rootPath = base_path();
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($rootPath), \RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $relativePath = substr($file->getPathname(), strlen($rootPath) + 1);
                    $zip->addFile($file->getPathname(), $relativePath);
                }
            }
            $zip->close();
        }

        return response()->json(['success' => 'Project files backup completed successfully!']);
    }
}
