<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Product;
use App\Models\Category;
use App\Models\Favorites;
use App\Models\PartnerProduct;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductActivationRequest;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $query = Product::query()->with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Category filter (including subcategories recursively)
        if ($request->filled('category_id')) {
            $category = Category::with('children')->find($request->category_id);
            if ($category) {
                $categoryIds = $this->getAllCategoryIds($category);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('partner_id')) {
            $partnerId = $request->partner_id;

            $query->whereHas('partners', function ($q) use ($partnerId) {
                $q->where('partners.id', $partnerId);
            });
        }

        $sort = $request->get('sort', 'id');

        switch ($sort) {
            case 'sum_max':
                $query->orderByDesc('price');
                break;
            case 'sum_min':
                $query->orderBy('price');
                break;
            default:
                $query->orderBy('id');
                break;
        }

        $products = $query->paginate($request->get('per_page', 12));

        foreach ($products as $product) {

            $partnersIds = PartnerProduct::where('product_id', $product->id)->pluck('partner_id')->toArray();
            $product->partners = Partner::whereIn('id', $partnersIds)->get();

            // Images
            $product->images = ProductImage::where('product_id', $product->id)
                ->select('id', 'path')
                ->get();

            // Favorite
            $product->is_favorite = $user
                ? Favorites::where('product_id', $product->id)
                    ->where('user_id', $user->id)
                    ->exists()
                : false;
        }

        return response()->json($products);
    }

    // Recursive function to get all child category IDs
    private function getAllCategoryIds(Category $category)
    {
        $ids = [$category->id];

        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getAllCategoryIds($child));
        }

        return $ids;
    }

    public function show(Product $product)
    {
        $partners = PartnerProduct::join('partners', 'partners.id', '=', 'partner_product.partner_id')
            ->where('partner_product.product_id', $product->id)
            ->select('partners.*')
            ->get();

        $images = ProductImage::join('products', 'products.id', '=', 'product_images.product_id')
            ->where('product_images.product_id', $product->id)
            ->select('product_images.id', 'product_images.path')
            ->get();

        $product->partners = $partners;
        $product->imeges = $images;

        return response()->json(['data' => $product]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:products,name',
            'price'       => 'required|numeric',
            'quantity'    => 'required|integer|min:0',
            'description' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'string',
        ]);

        $product = Product::create([
            'name'        => $validated['name'],
            'price'       => $validated['price'],
            'quantity'    => $validated['quantity'],
            'description' => $validated['description'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                ]);
            }
        }

        PartnerProduct::create([
            'product_id' => $product->id,
            'partner_id' => auth()->id(),
        ]);

        return response()->json($product->load('images'), 201);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'price'       => 'sometimes|numeric',
            'quantity'    => 'sometimes|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive,out_of_stock'
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully ✅',
            'product' => $product->fresh('images'),
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        $images = ProductImage::where('product_id', $product->id);

        foreach ($images as $image) {
            if (
                $image->path && Storage::disk('public')
                    ->exists(str_replace('storage/', '', $image->path))
            ) {
                Storage::disk('public')
                    ->delete(str_replace('storage/', '', $image->path));
            }
        }

        return response()->json(['message' => 'Product deleted']);
    }

    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('products', 'public');

        $image = ProductImage::create([
            'product_id' => $product->id,
            'path' => $path,
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'image'   => $image,
        ]);
    }

    public function deleteImage(ProductImage $image)
    {
        if (
            $image->path && Storage::disk('public')
                ->exists(str_replace('storage/', '', $image->path))
        ) {
            Storage::disk('public')
                ->delete(str_replace('storage/', '', $image->path));
        }

        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully ✅',
            'images' => $image,
        ]);
    }


    public function attachPartner(Request $request, $productId)
    {
        $request->validate([
            'partner_id' => 'required|array',
            'partner_id.*' => 'exists:partners,id',
        ]);

        $product = Product::find($productId);

        PartnerProduct::where('product_id', $product->id)->delete();

        foreach ($request->partner_id as $partnerId) {
            PartnerProduct::firstOrCreate([
                'product_id' => $product->id,
                'partner_id' => $partnerId,
            ]);
        }

        return response()->json(['message' => 'Partner attached to product successfully']);
    }

    public function requestActivation(Product $product)
    {
        // Егер продукт қазір active болса — запрос керегі жоқ
        if ($product->status === 'active') {
            return response()->json([
                'message' => 'Өнім қазірдің өзінде активте'
            ], 400);
        }

        // 2) Бұрын pending запрос жіберілмегенін тексеру
        $pending = ProductActivationRequest::where('product_id', $product->id)
            ->where('partner_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return response()->json([
                'message' => 'Сіз бұл өнімге активация запросын жібердіңіз'
            ], 400);
        }

        // 3) Запрос құру
        ProductActivationRequest::create([
            'product_id' => $product->id,
            'partner_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Активацияға запрос жіберілді'
        ]);
    }

    public function activationRequests()
    {
        $requests = ProductActivationRequest::with(['product', 'partner'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    public function approve(Product $product)
    {
        $product->status = 'active';
        $product->is_approved = true;
        $product->save();

        ProductActivationRequest::where('product_id', $product->id)
            ->where('status', 'pending')
            ->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Өнім сәтті активке өткізілді'
        ]);
    }

    public function reject(Product $product)
    {
        ProductActivationRequest::where('product_id', $product->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Запрос қабылданбады'
        ]);
    }

}
