<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\AltusService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, AltusService $altus): Response
    {
        $query = $request->query('q');

        if (empty($query)) {
            return Inertia::render('Welcome', [
                'products' => Product::all(),
            ]);
        }

        $databaseId = config('services.altus.database_id', env('ALTUS_DATABASE_ID'));

        if (empty($databaseId)) {
            logger()->warning('AltusService: ALTUS_DATABASE_ID not configured, falling back to regular products');
            return Inertia::render('Welcome', [
                'products' => Product::all(),
            ]);
        }

        $response = $altus->search([
            'query' => $query,
            'databases' => [$databaseId],
            'tables' => ['products'],
            'columns' => ['name', 'description', 'sku'],
            'searchMode' => 'natural',
            'limit' => 50,
        ]);

        $results = data_get($response, 'data.results', []);
        $skus = $this->extractSkus($results);

        if (empty($skus)) {
            return Inertia::render('Welcome', [
                'products' => Product::all(),
            ]);
        }

        $products = $this->fetchProductsBySkus($skus);
        $this->attachAltusMeta($products, $results);

        return Inertia::render('Welcome', [
            'products' => $products->values(),
        ]);
    }

    /**
     * Extract SKUs from Altus result set preserving order and uniqueness.
     */
    private function extractSkus(array $results): array
    {
        return collect($results)
            ->map(fn ($r) => data_get($r, 'data.sku'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Fetch products by SKUs and return a collection ordered by the provided SKU list.
     *
     * @return \Illuminate\Support\Collection
     */
    private function fetchProductsBySkus(array $skus)
    {
        $fetched = Product::whereIn('sku', $skus)->get()->keyBy('sku');

        return collect($skus)->map(fn ($sku) => $fetched->get($sku))->filter();
    }

    /**
     * Attach Altus metadata onto product models when available.
     *
     * @param  \Illuminate\Support\Collection  $products
     */
    private function attachAltusMeta($products, array $results): void
    {
        if (empty($results) || $products->isEmpty()) {
            return;
        }

        $metaBySku = collect($results)->mapWithKeys(fn ($r) => [data_get($r, 'data.sku') => $r]);

        $products->transform(function ($product) use ($metaBySku) {
            $meta = $metaBySku->get($product->sku);
            if ($meta) {
                $product->altus = [
                    'relevanceScore' => data_get($meta, 'relevanceScore'),
                    'snippet' => data_get($meta, 'snippet'),
                    'categories' => data_get($meta, 'categories', []),
                ];
            }

            return $product;
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): Response
    {
        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }
}
