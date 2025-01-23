<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    // Listar todos os restaurantes
    public function index()
    {
        return Restaurant::paginate(10);
    }

    // Criar um novo restaurante
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string',
        ]);

        $restaurant = Restaurant::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'owner_id' => auth()->id(),
        ]);

        return response()->json($restaurant, 201);
    }

    // Exibir um restaurante específico
    public function show($id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        return $restaurant;
    }

    // Atualizar um restaurante
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        if ($restaurant->owner_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $restaurant->update($validated);

        return response()->json($restaurant);
    }

    // Excluir um restaurante
    public function destroy($id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        if ($restaurant->owner_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted']);
    }

    public function report($id)
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        $reservations = $restaurant->reservations()->count();
        $averageRating = $restaurant->reviews()->avg('rating');

        return response()->json([
            'total_reservations' => $reservations,
            'average_rating' => $averageRating,
        ]);
    }
}
