<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceSeason;
use App\Models\City;
use App\Models\User;
use App\Models\TourQuotation;
use App\Models\TourQuotationDay;
use App\Models\TourInclusion;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    /**
     * Search services by keyword/category.
     */
    public function searchServices(Request $request)
    {
        $services = Service::with(['serviceCategory', 'venderUser']);

        if ($request->filled('keyword')) {
            $services->where('description', 'like', "%{$request->keyword}%");
        }

        if ($request->filled('category_id')) {
            $catId = $request->category_id;
            // Recursively get ALL descendant category IDs
            $allCatIds = [$catId];
            $queue = [$catId];
            while (!empty($queue)) {
                $parentId = array_shift($queue);
                $childIds = ServiceCategory::where('parent_id', $parentId)->pluck('id')->toArray();
                foreach ($childIds as $cid) {
                    $allCatIds[] = $cid;
                    $queue[] = $cid;
                }
            }
            $services->whereIn('category', $allCatIds);
        }

        if ($request->filled('country_id')) {
            $services->where('country', $request->country_id);
        }

        return response()->json($services->limit(50)->get());
    }

    /**
     * Add expense to quotation day.
     */
    public function addExpense(Request $request)
    {
        $day = TourQuotationDay::findOrFail($request->day_id);
        $expenses = json_decode($day->expenses, true) ?: [];
        $expenses[] = [
            'service_id' => $request->service_id,
            'description' => $request->description,
            'cost' => $request->cost,
            'qty' => $request->qty ?? 1,
        ];
        $day->expenses = json_encode($expenses);

        // Recalculate total
        $total = 0;
        foreach ($expenses as $exp) {
            $total += ($exp['cost'] ?? 0) * ($exp['qty'] ?? 1);
        }
        $day->total_cost = $total;
        $day->save();

        return response()->json(['success' => true, 'total' => $total, 'expenses' => $expenses]);
    }

    /**
     * Calculate quotation totals.
     */
    public function calculateQuotation(Request $request)
    {
        $quotation = TourQuotation::with('quotationDays')->findOrFail($request->quotation_id);
        $totalCost = $quotation->quotationDays->sum('total_cost');
        $quotation->total_cost = $totalCost;
        $quotation->save();

        return response()->json(['total_cost' => $totalCost, 'total' => $quotation->total]);
    }

    /**
     * Delete expense from quotation day.
     */
    public function deleteExpense(Request $request)
    {
        $day = TourQuotationDay::findOrFail($request->day_id);
        $expenses = json_decode($day->expenses, true) ?: [];

        if (isset($expenses[$request->expense_index])) {
            array_splice($expenses, $request->expense_index, 1);
        }

        $day->expenses = json_encode($expenses);
        $total = 0;
        foreach ($expenses as $exp) {
            $total += ($exp['cost'] ?? 0) * ($exp['qty'] ?? 1);
        }
        $day->total_cost = $total;
        $day->save();

        return response()->json(['success' => true, 'total' => $total]);
    }

    /**
     * Get service details for expense modal.
     */
    public function getServiceDetails(Request $request)
    {
        $service = Service::with(['serviceCategory', 'venderUser', 'seasons'])->findOrFail($request->service_id);

        // Check seasonal pricing
        $cost = $service->cost;
        if ($request->filled('date')) {
            $season = $service->seasons()
                ->where('date_from', '<=', $request->date)
                ->where('date_to', '>=', $request->date)
                ->first();
            if ($season) {
                $cost = $season->cost;
            }
        }

        return response()->json([
            'id' => $service->id,
            'description' => $service->description,
            'cost' => $cost,
            'vender' => $service->venderUser ? $service->venderUser->full_name : 'N/A',
            'category' => $service->serviceCategory ? $service->serviceCategory->name : 'N/A',
        ]);
    }

    /**
     * Get cities for a country.
     */
    public function getCities(Request $request)
    {
        $cities = City::where('country', $request->country_id)
            ->where('lang', $request->lang ?? 'en')
            ->orderBy('name')
            ->get();

        return response()->json($cities);
    }

    /**
     * Get all inclusion items for the conditions library picker.
     */
    public function getInclusions()
    {
        $items = TourInclusion::where('lang', 'en')->orderBy('name')->get(['id', 'lang_id', 'name']);
        return response()->json($items);
    }

    /**
     * Save quotation day content.
     */
    public function saveQuotationDay(Request $request)
    {
        $day = TourQuotationDay::findOrFail($request->day_id);
        $day->update($request->only(['contents', 'included', 'excluded', 'images']));
        return response()->json(['success' => true]);
    }

    /**
     * Check if a user exists by email (AJAX for booking forms).
     */
    public function checkUser(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json([
                'found' => true,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'company' => $user->company ?? '',
            ]);
        }

        return response()->json(['found' => false]);
    }

    /**
     * Get top-level service categories for a country (AJAX for expense country dropdown).
     */
    public function getCountryCategories(Request $request)
    {
        $countryId = $request->country_id;

        // Get top-level categories (parent_id is null or 0) for this country
        $topCategories = ServiceCategory::where('country_id', $countryId)
            ->where(function($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->orderBy('name')
            ->get();

        $result = [];
        foreach ($topCategories as $cat) {
            $hasChildren = ServiceCategory::where('parent_id', $cat->id)->exists();
            $result[] = [
                'id' => $cat->id,
                'name' => html_entity_decode($cat->name),
                'has_children' => $hasChildren,
            ];
        }

        return response()->json($result);
    }

    /**
     * Browse file manager for image selection (AJAX).
     */
    public function fileManagerBrowse(Request $request)
    {
        $baseDir = public_path('uploads');
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }

        $currentDir = $request->query('dir', '');
        $currentDir = str_replace(['..', '\\'], '', $currentDir);
        $currentDir = trim($currentDir, '/');

        $fullPath = $baseDir . ($currentDir ? '/' . $currentDir : '');
        if (!is_dir($fullPath)) {
            $currentDir = '';
            $fullPath = $baseDir;
        }

        $folders = [];
        $files = [];
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

        foreach (new \DirectoryIterator($fullPath) as $item) {
            if ($item->isDot()) continue;
            if ($item->isDir()) {
                $folders[] = $item->getFilename();
            } else {
                $ext = strtolower($item->getExtension());
                if (in_array($ext, $imageExts)) {
                    $relativePath = '/uploads/' . ($currentDir ? $currentDir . '/' : '') . $item->getFilename();
                    $files[] = [
                        'name' => $item->getFilename(),
                        'url' => $relativePath,
                    ];
                }
            }
        }

        sort($folders);
        usort($files, function($a, $b) { return strcmp($a['name'], $b['name']); });

        return response()->json([
            'current_dir' => $currentDir,
            'folders' => $folders,
            'files' => $files,
        ]);
    }

    /**
     * Get subcategories for a given parent category (AJAX for expense modal left panel).
     */
    public function getSubcategories(Request $request)
    {
        $parentId = $request->query('parent_id', 0);

        $children = ServiceCategory::where('parent_id', $parentId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $result = [];
        foreach ($children as $child) {
            $hasChildren = ServiceCategory::where('parent_id', $child->id)->exists();
            $result[] = [
                'id' => $child->id,
                'name' => html_entity_decode($child->name),
                'has_children' => $hasChildren,
            ];
        }

        return response()->json($result);
    }
    /**
     * Upload file to file manager (AJAX).
     */
    public function fileManagerUpload(Request $request)
    {
        $baseDir = public_path('uploads');
        $currentDir = str_replace(['..', '\\'], '', $request->input('dir', ''));
        $currentDir = trim($currentDir, '/');
        $targetDir = $baseDir . ($currentDir ? '/' . $currentDir : '');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = $file->getClientOriginalName();
            $targetPath = $targetDir . '/' . $name;
            if (file_exists($targetPath)) {
                $name = time() . '_' . $name;
            }
            $file->move($targetDir, $name);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'No file selected']);
    }

    /**
     * Create new folder in file manager (AJAX).
     */
    public function fileManagerCreateFolder(Request $request)
    {
        $baseDir = public_path('uploads');
        $currentDir = str_replace(['..', '\\'], '', $request->input('dir', ''));
        $currentDir = trim($currentDir, '/');
        $folderName = preg_replace('/[^a-zA-Z0-9_ -]/', '', $request->input('folder_name', ''));
        $targetDir = $baseDir . ($currentDir ? '/' . $currentDir : '') . '/' . $folderName;

        if ($folderName && !is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Could not create folder']);
    }
}
