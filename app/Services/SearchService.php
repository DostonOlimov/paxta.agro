<?php
namespace App\Services;

use App\Models\OrganizationCompanies;
use Illuminate\Http\Request;

class SearchService
{
    public function search(
        Request $request,
        $filter,
        $modelClass,
        array $withRelations,
        array $viewData = [],
        $viewName = '',
        $status = [],
        $hasTestPrograms = false, // Add flag for whereHas condition
        $sumRelation = null,      // Related model for withSum
        $sumColumn = null         // Column to sum
    ) {
        // Default sorting by 'id' and order by 'desc'
        $sort_by = $request->get('sort_by', 'id');
        $sort_order = $request->get('sort_order', 'desc');

        // Extract filters from request
        $filters = $filter->getFilters($request);

        // Initialize filter values for use in the view
        $filterValues = array_map(fn($conditions) => reset($conditions), $filters);

        // Get the table name from the model class
        $modelInstance = new $modelClass;
        $table = $modelInstance->getTable();

        // Start building the query
        $query = $modelClass::query()->select("{$table}.id as model_id", "{$table}.*");

        // Apply whereIn condition for dalolatnoma status if provided
        if (!empty($status)) {
            $query->whereIn('status', $status);
        }

        // Apply whereHas condition if requested
        if ($hasTestPrograms) {
            $query->whereHas('tests');
        }

        // Apply withSum if a relation and column are provided
        if ($sumRelation && $sumColumn) {
            $query->withSum($sumRelation, $sumColumn);
        }

        // Apply filters and sorting to the query
        $filteredQuery = $filter->apply($query, $filters);
        $sortedQuery = $filter->applySorting($filteredQuery, $sort_by, $sort_order);

        // Fetch paginated results with relationships
        $results = $sortedQuery->with($withRelations)
            ->paginate(50)
            ->appends($request->except('page'));

        // Fetch organization if companyId filter is applied
        $organization = $filterValues['companyId'] ?? null
                ? OrganizationCompanies::find($filterValues['companyId'])
                : null;

        return view($viewName, array_merge($viewData, [
            'apps' => $results,
            'organization' => $organization,
            'filterValues' => $filterValues,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order,
        ]));
    }

}

