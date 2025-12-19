<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductBanner;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProductManageController extends Controller
{
    public function index(Request $request)
    {
        try {

            $this->authorizeView($request);

            $status    = $request->get('status', '');
            $category  = $request->get('category', '');
            $added     = $request->get('added', '');
            $addedFrom = $request->get('added_from', '');
            $addedTo   = $request->get('added_to', '');
            $q         = $request->get('search') ?? $request->get('q') ?? '';

            $tz = 'Asia/Kolkata';
            [$fromDt, $toDt] = $this->computeDateRange($added, $addedFrom, $addedTo, $tz);

            $hasStatus = Schema::hasColumn('products', 'status');

            $products = Product::query()
                ->with('user:id,name')
                ->when($status !== '' && $hasStatus, fn($qr) => $qr->where('status', $status))
                ->when($category !== '', function($qr) use ($category) {
                   $qr->where(function($sub) use ($category){
                       $sub->where('category', $category)
                           ->orWhereHas('linkedCategory', function($rel) use ($category){
                              $rel->where('name', $category);
                           });
                   });
                })
                ->when($fromDt, fn($qr) => $qr->where('created_at', '>=', $fromDt->copy()->timezone('UTC')))
                ->when($toDt,   fn($qr) => $qr->where('created_at', '<',  $toDt->copy()->addDay()->startOfDay()->timezone('UTC')))
                ->orderByDesc('id')
                ->paginate(12)
                ->withQueryString();

            if ($request->ajax()) {
                return view('admin.products.partials.row', compact('products'))->render();
            }

            // Legacy string categories
            $stringCats = Product::query()
                ->select('category')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->pluck('category')
                ->all();

            // New Hierarchical categories
            $modelCats = \App\Models\Category::pluck('name')->all();
            
            // Merge and Unique
            $categories = collect($stringCats)->merge($modelCats)->unique()->sort()->values()->all();

            $todayStart = Carbon::now($tz)->startOfDay();
            $weekStart  = Carbon::now($tz)->startOfWeek();
            $monthStart = Carbon::now($tz)->startOfMonth();

            $counts = [
                'total'         => Product::count(),
                'active'        => $hasStatus ? Product::where('status', 'active')->count() : null,
                'draft'         => $hasStatus ? Product::where('status', 'draft')->count()  : null,
                'added_today'   => Product::whereBetween('created_at', [
                                        $todayStart->copy()->timezone('UTC'),
                                        $todayStart->copy()->endOfDay()->timezone('UTC')
                                    ])->count(),
                'added_week'    => Product::whereBetween('created_at', [
                                        $weekStart->copy()->timezone('UTC'),
                                        Carbon::now($tz)->endOfDay()->timezone('UTC')
                                    ])->count(),
                'added_month'   => Product::whereBetween('created_at', [
                                        $monthStart->copy()->timezone('UTC'),
                                        Carbon::now($tz)->endOfDay()->timezone('UTC')
                                    ])->count(),
                'updated_today' => Product::whereBetween('updated_at', [
                                        $todayStart->copy()->timezone('UTC'),
                                        $todayStart->copy()->endOfDay()->timezone('UTC')
                                    ])->count(),
            ];

            $allCategories = \App\Models\Category::with('children')->whereNull('parent_id')->get();

            // DATA FOR JS BANNER MODAL (Client-side lookup)
            $simpleProducts = Product::with('banners')
                                ->select('id','name') // no select banner collumn
                                ->orderBy('name')
                                ->get()
                                ->map(function($p){
                                    return [
                                        'id' => $p->id,
                                        'name' => $p->name,
                                        'banners' => $p->banners->map(fn($b) => [
                                            'id' => $b->id,
                                            'url' => Storage::url($b->image),
                                            'start' => $b->start_at,
                                            'end' => $b->end_at,
                                        ])
                                    ];
                                });

            return view('admin.products.manage', compact(
                'products', 'q', 'status', 'category', 'categories', 'allCategories',
                'added', 'addedFrom', 'addedTo', 'counts', 'simpleProducts'
            ));

        } catch (\Exception $e) {

            \Log::error('ProductManage index error: '.$e->getMessage());
            return back()->with('error', 'Error loading products: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $product = new Product();
            $allCategories = \App\Models\Category::with('children')->whereNull('parent_id')->get();
            return view('admin.products.create', compact('product', 'allCategories'));

        } catch (\Exception $e) {
            \Log::error('Product create error: '.$e->getMessage());
            return back()->with('error', 'Unable to open create page.');
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'name'        => ['required','string','max:255'],
            'price'       => ['required','numeric','min:0'],
            'stock'       => ['required','integer','min:0'],
            'description' => ['nullable','string'],
            'category_id' => ['nullable','exists:categories,id'],
            'category'    => ['nullable','string','max:100'],
            'image'       => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'banner'      => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'], // Banner rule
            'status'      => ['required', \Illuminate\Validation\Rule::in(['active','draft'])],
        ];

        $data = $request->validate($rules);

        try {

            // Handle status -> is_active mapping
            $data['is_active'] = ($data['status'] === 'active');

            // If DB does not have 'status' column, remove it from data to avoid SQL error
            if (!Schema::hasColumn('products', 'status')) {
                unset($data['status']);
            }

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            if ($request->hasFile('banner')) {
                $data['banner'] = $request->file('banner')->store('products/banners', 'public');
            }

            $data['user_id'] = auth()->id();

            // Link to Category model
            // Prioritize ID if sent, otherwise find/create by name
            if (!empty($data['category_id'])) {
                // ID already valid via validation
                // $data['category_id'] = $data['category_id']; // Redundant
                // Optionally fill 'category' string for backward compat if column exists
                $cat = \App\Models\Category::find($data['category_id']);
                if ($cat) $data['category'] = $cat->name; 
            } elseif (!empty($data['category'])) {
                $cat = \App\Models\Category::firstOrCreate(['name' => $data['category']]);
                $data['category_id'] = $cat->id;
            }

            if (Schema::hasColumn('products', 'slug')) {
                // Ensure unique slug even if names are duplicate
                $data['slug'] = Str::slug($data['name']).'-'.Str::random(10);
            }

            Product::create($data);

            return redirect()->route('admin.products.list')
                             ->with('success', 'Product created.');

        } catch (\Exception $e) {
            \Log::error('Product store error: '.$e->getMessage());
            return back()->with('error', 'Unable to create product: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        try {
            $allCategories = \App\Models\Category::with('children')->whereNull('parent_id')->get();
            return view('admin.products.edit', compact('product', 'allCategories'));
        } catch (\Exception $e) {
            \Log::error('Product edit error: '.$e->getMessage());
            return back()->with('error', 'Unable to open edit page.');
        }
    }

    public function update(Request $request, Product $product)
    {
        try {

            $data = $request->validate([
                'name'        => ['required','string','max:255'],
                'price'       => ['required','numeric','min:0'],
                'stock'       => ['required','integer','min:0'],
                'description' => ['nullable','string'],
                'status'      => ['required', Rule::in(['active','draft'])],
                'image'       => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
                'banner'      => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            ]);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            if ($request->hasFile('banner')) {
                $data['banner'] = $request->file('banner')->store('products/banners', 'public');
            }

            if ($product->name !== $data['name'] && Schema::hasColumn('products', 'slug')) {
                 $data['slug'] = Str::slug($data['name']).'-'.Str::random(10);
            }

            // Link to Category model
            if (!empty($data['category'])) {
                $cat = \App\Models\Category::firstOrCreate(['name' => $data['category']]);
                $data['category_id'] = $cat->id;
            }

            $product->update($data);

            return redirect()->route('admin.products.list')
                             ->with('success', 'Product updated.');

        } catch (\Exception $e) {

            \Log::error('Product update error: '.$e->getMessage());
            return back()->with('error', 'Unable to update product.');
        }
    }

    public function destroy(Product $product)
    {
        try {

            $product->delete();

            return redirect()->route('admin.products.list')
                             ->with('success', 'Product deleted.');

        } catch (\Exception $e) {

            \Log::error('Product delete error: '.$e->getMessage());
            return back()->with('error', 'Unable to delete product.');
        }
    }

    public function updateBanner(Request $request, Product $product)
    {
        $request->validate([
            'banner'          => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            // 'remove_banner' is handled via delete route now
            'banner_start_at' => ['nullable','date'],
            'banner_end_at'   => ['nullable','date','after_or_equal:banner_start_at'],
        ]);

        try {
            // New Upload
            if ($request->hasFile('banner')) {
                $path = $request->file('banner')->store('products/banners', 'public');
                
                $product->banners()->create([
                    'image'      => $path,
                    'start_at'   => $request->banner_start_at ?: null,
                    'end_at'     => $request->banner_end_at ?: null,
                    'sort_order' => 0,
                ]);

                return redirect()->route('admin.products.list')
                                 ->with('success', 'New banner added.');
            }

            return back()->with('warning', 'No image uploaded.');

        } catch (\Exception $e) {
            \Log::error('Banner add error: '.$e->getMessage());
            return back()->with('error', 'Failed to add banner: '.$e->getMessage());
        }
    }

    public function destroyBanner(ProductBanner $productBanner)
    {
        try {
            if ($productBanner->image) {
                \Storage::disk('public')->delete($productBanner->image);
            }
            $productBanner->delete();
            return back()->with('success', 'Banner removed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error removing banner.');
        }
    }

    /* ---------- NEW: Toggle Status ---------- */
    public function toggleStatus(Product $product)
    {
        try {
            // Toggle boolean
            $newState = !$product->is_active;
            $product->is_active = $newState;

            // Sync string status if exists
            if (Schema::hasColumn('products', 'status')) {
                $product->status = $newState ? 'active' : 'draft';
            }

            $product->save();

            $msg = $newState ? 'Product Activated.' : 'Product Deactivated.';
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            \Log::error('Product toggle error: '.$e->getMessage());
            return back()->with('error', 'Unable to change status.');
        }
    }

    /* ---------- helpers ---------- */

    private function computeDateRange(?string $quick, ?string $from, ?string $to, string $tz): array
    {
        // unchanged helper
        $fromDt = null; $toDt = null;

        if ($quick === 'today') {
            $fromDt = Carbon::now($tz)->startOfDay();
            $toDt   = Carbon::now($tz)->endOfDay();
        } elseif ($quick === 'this_week') {
            $fromDt = Carbon::now($tz)->startOfWeek();
            $toDt   = Carbon::now($tz)->endOfDay();
        } elseif ($quick === 'this_month') {
            $fromDt = Carbon::now($tz)->startOfMonth();
            $toDt   = Carbon::now($tz)->endOfDay();
        }

        if ($from) {
            $fromDt = Carbon::createFromFormat('Y-m-d', $from, $tz)->startOfDay();
        }
        if ($to) {
            $toDt = Carbon::createFromFormat('Y-m-d', $to, $tz)->endOfDay();
        }

        return [$fromDt, $toDt];
    }

    private function authorizeView(Request $request): void
    {
        // unchanged helper
        $u = $request->user();
        if (!method_exists($u, 'isAdmin') || !method_exists($u, 'isSeller')) {
            return;
        }
    }

    private function authorizeModify(Request $request): void
    {
        return;
    }

    private function validateData(Request $request, bool $isUpdate = false): array
    {
        // unchanged helper
        $rules = [
            'name'        => ['required','string','max:255'],
            'category'    => ['nullable','string','max:120'],
            'description' => ['nullable','string','max:2000'],
            'price'       => ['required','numeric','min:0'],
            'stock'       => ['required','integer','min:0'],
            'sku'         => ['nullable','string','max:100'],

            'discount_type'      => ['nullable','in:percent,flat'],
            'discount_value'     => ['nullable','numeric','min:0'],
            'discount_starts_at' => ['nullable','date'],
            'discount_ends_at'   => ['nullable','date','after_or_equal:discount_starts_at'],
            'is_discount_active' => ['nullable','boolean'],

            'image'       => [$isUpdate ? 'nullable' : 'nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];

        if (Schema::hasColumn('products','status')) {
            $rules['status'] = ['required', Rule::in(['active','draft'])];
        }

        return $request->validate($rules);
    }
}
