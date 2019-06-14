<?php

namespace Encore\Admin\LogViewer;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $dir = $request->query->get('dir');
        $file = $request->query->get('file');

        if (!$dir && !$file) {
            $file = (new LogViewer())->getLastModifiedLog();
        }

        return Admin::content(function (Content $content) use ($file, $request, $dir) {
            $offset = $request->get('offset');

            $viewer = new LogViewer($file, $dir);
            $test = $viewer->dirToArray(storage_path('logs/'));
            $content->body(view('laravel-admin-logs::logs', [
                'logs'      => $viewer->fetch($offset),
                'logFiles'  => $viewer->getLogFiles(),
                'fileName'  => $viewer->file,
                'end'       => $viewer->getFilesize(),
                'tailPath'  => route('log-viewer-tail', ['file' => $viewer->file]),
                'prevUrl'   => $viewer->getPrevPageUrl(),
                'nextUrl'   => $viewer->getNextPageUrl(),
                'filePath'  => $viewer->getFilePath(),
                'size'      => static::bytesToHuman($viewer->getFilesize()),
                'currentDir' => $dir,
                'dirTree' => $viewer->dirToArray(storage_path('logs' . DIRECTORY_SEPARATOR)),
            ]));

            $content->header(ltrim($dir . DIRECTORY_SEPARATOR . $file, '/'));
        });
    }

    private function checkFile($file) {
        $file_path = glob('.' . DIRECTORY_SEPARATOR . $file);
        return reset($file_path);
    }

    public function tail($file, Request $request)
    {
        $offset = $request->get('offset');

        $viewer = new LogViewer($file);

        list($pos, $logs) = $viewer->tail($offset);

        return compact('pos', 'logs');
    }

    protected static function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
