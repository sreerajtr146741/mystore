<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
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

            $q         = trim($request->get('q', ''));
            $status    = $request->get('status', '');
            $category  = $request->get('category', '');
            $added     = $request->get('added', '');
            $addedFrom = $request->get('added_from', '');
            $addedTo   = $request->get('added_to', '');

            $tz = 'Asia/Kolkata';
            [$fromDt, $toDt] = $this->computeDateRange($added, $addedFrom, $addedTo, $tz);

            $hasStatus = Schema::hasColumn('products', 'status');

            $products = Product::query()
                ->with('user:id,name')
                ->when($q, function ($qr) use ($q) {
                    $qr->where(function ($w) use ($q) {
                        $w->where('id', $q)
                          ->orWhere('name', 'like', "%{$q}%")
                          ->orWhere('category', 'like', "%{$q}%");
                    });
                })
                ->when($status !== '' && $hasStatus, fn($qr) => $qr->where('status', $status))
                ->when($category !== '', fn($qr) => $qr->where('category', $category))
                ->when($fromDt, fn($qr) => $qr->where('created_at', '>=', $fromDt->copy()->timezone('UTC')))
                ->when($toDt,   fn($qr) => $qr->where('created_at', '<',  $toDt->copy()->addDay()->startOfDay()->timezone('UTC')))
                ->orderByDesc('id')
                ->paginate(12)
                ->withQueryString();

            $categories = Product::query()
                ->select('category')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->all();

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

            return view('admin.products.manage', compact(
                'products', 'q', 'status', 'category', 'categories',
                'added', 'addedFrom', 'addedTo', 'counts'
            ));

        } catch (\Exception $e) {

            \Log::error('ProductManage index error: '.$e->getMessage());
            return back()->with('error', 'Unable to load products.');
        }
    }

    public function create()
    {
        try {

            $product = new Product();
            return view('admin.products.create', compact('product'));

        } catch (\Exception $e) {

            \Log::error('Product create error: '.$e->getMessage());
            return back()->with('error', 'Unable to open create page.');
        }
    }

    public function store(Request $request)
    {
        try {

            $rules = [
                'name'        => ['required','string','max:255', \Illuminate\Validation\Rule::unique('products','name')],
                'price'       => ['required','numeric','min:0'],
                'stock'       => ['required','integer','min:0'],
                'description' => ['nullable','string'],
                'category'    => ['nullable','string','max:100'],
                'image'       => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            ];

            if (Schema::hasColumn('products', 'status')) {
                $rules['status'] = ['required', \Illuminate\Validation\Rule::in(['active','draft'])];
            }

            $data = $request->validate($rules);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $data['user_id'] = auth()->id();

            if (Schema::hasColumn('products', 'slug')) {
                $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
            }

            Product::create($data);

            return redirect()->route('admin.products.manage')
                             ->with('success', 'Product created.');

        } catch (\Exception $e) {

            \Log::error('Product store error: '.$e->getMessage());
            return back()->with('error', 'Unable to create product.');
        }
    }

    public function edit(Product $product)
    {
        try {

            return view('admin.products.edit', compact('product'));

        } catch (\Exception $e) {

            \Log::error('Product edit error: '.$e->getMessage());
            return back()->with('error', 'Unable to open edit page.');
        }
    }

    public function update(Request $request, Product $product)
    {
        try {

            $data = $request->validate([
                'name'        => ['required','string','max:255', Rule::unique('products','name')->ignore($product->id)],
                'price'       => ['required','numeric','min:0'],
                'stock'       => ['required','integer','min:0'],
                'description' => ['nullable','string'],
                'status'      => ['required', Rule::in(['active','draft'])],
                'image'       => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            ]);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            if ($product->name !== $data['name'] && Schema::hasColumn('products', 'slug')) {
                $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
            }

            $product->update($data);

            return redirect()->route('admin.products.manage')
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

            return redirect()->route('admin.products.manage')
                             ->with('success', 'Product deleted.');

        } catch (\Exception $e) {

            \Log::error('Product delete error: '.$e->getMessage());
            return back()->with('error', 'Unable to delete product.');
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
