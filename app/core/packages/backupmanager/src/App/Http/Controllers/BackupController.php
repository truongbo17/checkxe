<?php

namespace Bo\BackupManager\App\Http\Controllers;

use Artisan;
use Carbon\Carbon;
use Exception;
use Illuminate\Routing\Controller;
use League\Flysystem\Adapter\Local;
use Log;
use Request;
use Storage;

class BackupController extends Controller
{
    public function index()
    {
        if (!count(config('backup.backup.destination.disks'))) {
            abort(500, trans('backupmanager::backup.no_disks_configured'));
        }

        $this->data['backups'] = [];

        foreach (config('backup.backup.destination.disks') as $diskName) {
            $disk = Storage::disk($diskName);
            $files = $disk->allFiles();

            // make an array of backup files, with their filesize and creation date
            foreach ($files as $file) {
                // remove diskname from filename
                $fileName = str_replace('backups/', '', $file);
                $downloadLink = route('backup.download', ['file_name' => $fileName, 'disk' => $diskName]);
                $deleteLink = route('backup.destroy', ['file_name' => $fileName, 'disk' => $diskName]);

                // only take the zip files into account
                if (substr($file, -4) == '.zip' && $disk->exists($file)) {
                    $this->data['backups'][] = (object) [
                        'filePath'     => $file,
                        'fileName'     => $fileName,
                        'fileSize'     => round((int) $disk->size($file) / 1048576, 2),
                        'lastModified' => Carbon::createFromTimeStamp($disk->lastModified($file))->formatLocalized('%d %B %Y, %H:%M'),
                        'diskName'     => $diskName,
                        'downloadLink' => is_a($disk->getAdapter(), Local::class, true) ? $downloadLink : null,
                        'deleteLink'   => $deleteLink,
                    ];
                }
            }
        }

        // reverse the backups, so the newest one would be on top
        $this->data['backups'] = array_reverse($this->data['backups']);
        $this->data['title'] = trans('backupmanager::backup.backups');

        return view('backupmanager::backup', $this->data);
    }

    public function create()
    {
        $command = config('bo.backupmanager.artisan_command_on_button_click') ?? 'backup:run';

        try {
            foreach (config('bo.backupmanager.ini_settings', []) as $setting => $value) {
                ini_set($setting, $value);
            }

            Log::info('Bo\BackupManager -- Called backup:run from admin interface');

            Artisan::call($command);

            $output = Artisan::output();
            if (strpos($output, 'Backup failed because')) {
                preg_match('/Backup failed because(.*?)$/ms', $output, $match);
                $message = "Bo\BackupManager -- backup process failed because ".($match[1] ?? '');
                Log::error($message.PHP_EOL.$output);

                return response($message, 500);
            }
        } catch (Exception $e) {
            Log::error($e);

            return response($e->getMessage(), 500);
        }

        return true;
    }

    /**
     * Downloads a backup zip file.
     */
    public function download()
    {
        $diskName = Request::input('disk');
        $fileName = Request::input('file_name');
        $disk = Storage::disk($diskName);

        if (!$this->isBackupDisk($diskName)) {
            abort(500, trans('backupmanager::backup.unknown_disk'));
        }

        if (!is_a($disk->getAdapter(), Local::class, true)) {
            abort(404, trans('backupmanager::backup.only_local_downloads_supported'));
        }

        if (!$disk->exists($fileName)) {
            abort(404, trans('backupmanager::backup.backup_doesnt_exist'));
        }

        return $disk->download($fileName);
    }

    /**
     * Deletes a backup file.
     */
    public function delete()
    {
        $diskName = Request::input('disk');
        $fileName = Request::input('file_name');

        if (!$this->isBackupDisk($diskName)) {
            return response(trans('backupmanager::backup.unknown_disk'), 500);
        }

        $disk = Storage::disk($diskName);

        if (!$disk->exists($fileName)) {
            return response(trans('backupmanager::backup.backup_doesnt_exist'), 404);
        }

        return $disk->delete($fileName);
    }

    /**
     * Check if disk is a backup disk.
     *
     * @param string $diskName
     *
     * @return bool
     */
    private function isBackupDisk(string $diskName)
    {
        return in_array($diskName, config('backup.backup.destination.disks'));
    }
}
