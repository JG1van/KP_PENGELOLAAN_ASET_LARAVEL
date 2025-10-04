<?php
use Illuminate\Support\Facades\DB;

if (!function_exists('generateUniqueId')) {
    function generateUniqueId($table, $column, $start = 1)
    {
        $id = DB::table($table)->max($column);
        $id = $id ? $id + 1 : $start;

        while (DB::table($table)->where($column, $id)->exists()) {
            $id++;
        }

        return $id;
    }
}
