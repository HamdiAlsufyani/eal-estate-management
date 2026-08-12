<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Services\Analytics\ReportsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportsController extends Controller
{
    private const TYPES = ['properties', 'users', 'inquiries', 'views', 'favorites'];

    public function __construct(private readonly ReportsService $reports)
    {
    }

    public function index(Request $request): View
    {
        $type = $request->string('type')->value();
        $type = in_array($type, self::TYPES, true) ? $type : 'properties';

        $filters = $request->only([
            'search', 'date_from', 'date_to', 'owner', 'city', 'property_type',
            'purpose', 'status', 'availability', 'role', 'property',
        ]);

        $results = match ($type) {
            'users' => $this->reports->users($filters),
            'inquiries' => $this->reports->inquiries($filters),
            'views' => $this->reports->views($filters),
            'favorites' => $this->reports->favorites($filters),
            default => $this->reports->properties($filters),
        };

        return view('admin.reports.index', [
            'type' => $type,
            'results' => $results,
            'filters' => array_merge($filters, ['type' => $type]),
            'owners' => User::query()->role('Owner')->orderBy('name')->pluck('name', 'id'),
            'cities' => City::query()->orderBy('name_en')->get()->pluck('name', 'id'),
            'propertyTypes' => PropertyType::query()->orderBy('name_en')->get()->pluck('name', 'id'),
            'properties' => Property::query()->orderBy('title_en')->get()->pluck('title', 'id'),
        ]);
    }
}
