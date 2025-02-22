<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAddress;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DeliveryAddressController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'mobile' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'order_notes' => 'nullable|string',
                'subtotal' => 'required|numeric',
                'shipping_fee' => 'required|numeric',
                'total' => 'required|numeric',
                'payment_method' => 'required|string|max:50',
                'product_snapshot' => 'required|array',
                'status' => 'required|string|in:Pending,Processing,Completed,Cancelled',
            ]);

            $deliveryAddress = DeliveryAddress::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery address created successfully',
                'data' => $deliveryAddress
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create delivery address',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $deliveryAddress = DeliveryAddress::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $deliveryAddress
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Delivery address not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve delivery address',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = DeliveryAddress::query();

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by payment method
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Filter by price range
            if ($request->filled('min_total')) {
                $query->where('total', '>=', $request->min_total);
            }
            if ($request->filled('max_total')) {
                $query->where('total', '<=', $request->max_total);
            }

            // Search by name, email, or mobile
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('mobile', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('address', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Sort results
            $sortField = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $allowedSortFields = ['created_at', 'total', 'status', 'name'];

            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $deliveryAddresses = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $deliveryAddresses,
                'meta' => [
                    'filters' => [
                        'status' => $request->status,
                        'payment_method' => $request->payment_method,
                        'date_from' => $request->date_from,
                        'date_to' => $request->date_to,
                        'min_total' => $request->min_total,
                        'max_total' => $request->max_total,
                        'search' => $request->search,
                        'sort_by' => $sortField,
                        'sort_direction' => $sortDirection,
                        'per_page' => $perPage
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve delivery addresses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
