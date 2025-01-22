<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // Listar itens do cardápio de um restaurante
    public function index(Request $request)
    {
        $restaurantId = $request->query('restaurant_id');

        if (!$restaurantId) {
            return response()->json(['message' => 'Restaurant ID is required'], 400);
        }

        $restaurant = Restaurant::find($restaurantId);

        if (!$restaurant) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }

        return $restaurant->menus;
    }

    // Criar um novo item no cardápio
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);

        $restaurant = Restaurant::find($validated['restaurant_id']);

        if ($restaurant->owner_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $menu = Menu::create($validated);

        return response()->json($menu, 201);
    }

    // Exibir um item específico do cardápio
    public function show($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu item not found'], 404);
        }

        return $menu;
    }

    // Atualizar um item do cardápio
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu item not found'], 404);
        }

        $restaurant = $menu->restaurant;

        if ($restaurant->owner_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        $menu->update($validated);

        return response()->json($menu);
    }

    // Excluir um item do cardápio
    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu item not found'], 404);
        }

        $restaurant = $menu->restaurant;

        if ($restaurant->owner_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $menu->delete();

        return response()->json(['message' => 'Menu item deleted']);
    }
}
