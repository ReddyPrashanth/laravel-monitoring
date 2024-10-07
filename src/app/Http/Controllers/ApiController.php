<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponses;

class ApiController extends Controller
{
    use ApiResponses;

    protected $policyClass;

    public function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (!isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $includeValues);
    }
}
