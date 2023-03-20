<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ArchiveUpload
{
    /**
     * Starts database transaction so that invalid archive imports are rolled back
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        DB::beginTransaction();
        $response = $next($request);

        if($response->status() == '200') {
            DB::commit();
        } else {
            DB::rollback();
        }

        return $response;
    }
}
