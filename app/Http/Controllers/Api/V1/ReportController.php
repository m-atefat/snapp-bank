<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TopUserInCardToCardResource;
use App\Services\ReportServices\ReportServices;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{

    public function __construct(private readonly ReportServices $reportServices)
    {
    }


    public function topCardToCardUsers(Request $request): AnonymousResourceCollection
    {
        $topUsers = $this->reportServices->topUserInCardToCardTransaction();
        return TopUserInCardToCardResource::collection($topUsers);
    }
}
