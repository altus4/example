<script setup lang="ts">
import { index } from "@/actions/App/Http/Controllers/ProductController";
import { Head, Link, Form } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';

interface Product {
    id: number;
    name: string;
    sku: string;
    description: string;
    price: number;
    stock: number;
    weight?: number;
    altus?: {
        relevanceScore?: number;
        snippet?: string;
        categories?: string[];
    };
}

defineProps<{
    products: Product[]
}>();

const searchQuery = ref('');

// Format price for display
const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price);
};

// Format relevance score for display
const formatRelevance = (score?: number): string => {
    if (!score) return '';
    return `${Math.round(score * 100)}% match`;
};
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center">
        <!-- Search -->
         <div class="w-full bg-gray-100 py-4">
            <div class="container mx-auto my-8 w-full flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold">Products</h1>
                <div class="flex justify-center">
                    <Form
                        :action="index.url"
                        method="get"
                        :reset-on-success="['password']"
                        class="flex flex-col gap-6"
                    >
                        <Input
                            type="text"
                            placeholder="Search products with AI-enhanced search..."
                            name="q"
                            v-model="searchQuery"
                            class="w-full max-w-xl rounded border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none"
                        />
                    </Form>
                </div>
            </div>
         </div>

        <!-- Products List -->
        <div class="container mx-auto my-8 w-full px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <Card v-for="product in products" :key="product.id" class="hover:shadow-lg transition-shadow">
                    <CardHeader>
                        <CardTitle class="flex items-center justify-between">
                            {{ product.name }}
                            <span v-if="product.altus?.relevanceScore" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                {{ formatRelevance(product.altus.relevanceScore) }}
                            </span>
                        </CardTitle>
                        <CardDescription class="text-sm text-gray-500">
                            SKU: {{ product.sku }}
                            <span v-if="product.stock > 0" class="ml-2 text-green-600">In Stock ({{ product.stock }})</span>
                            <span v-else class="ml-2 text-red-600">Out of Stock</span>
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-gray-700">
                            {{ product.altus?.snippet || product.description }}
                        </p>
                        <div v-if="product.altus?.categories?.length" class="mt-2">
                            <span
                                v-for="category in product.altus.categories"
                                :key="category"
                                class="inline-block bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded mr-1"
                            >
                                {{ category }}
                            </span>
                        </div>
                    </CardContent>
                    <CardFooter class="flex items-center justify-between">
                        <span class="text-lg font-semibold text-green-600">{{ formatPrice(product.price) }}</span>
                        <Link
                            :href="`/products/${product.id}`"
                            class="text-blue-600 hover:underline"
                        >
                            View Details
                        </Link>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </div>
</template>
