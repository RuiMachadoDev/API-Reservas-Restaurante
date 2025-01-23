<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Notifications\ReservationConfirmed;

class ReservationController extends Controller
{
    // Listar todas as reservas de um utilizador autenticado
    public function index()
    {
        return auth()->user()->reservations;
    }

    // Criar uma nova reserva
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'number_of_people' => 'required|integer|min:1',
            'reservation_time' => 'required|date|after:now',
        ]);

        $table = Table::find($validated['table_id']);

        if ($validated['number_of_people'] > $table->capacity) {
            return response()->json(['message' => 'Table capacity exceeded'], 400);
        }

        // Verificar se há overbooking no horário
        $existingReservation = Reservation::where('table_id', $table->id)
            ->where('reservation_time', $validated['reservation_time'])
            ->first();

        if ($existingReservation) {
            return response()->json(['message' => 'Table already reserved at this time'], 400);
        }

        $reservation = Reservation::create([
            'table_id' => $validated['table_id'],
            'user_id' => auth()->id(),
            'number_of_people' => $validated['number_of_people'],
            'reservation_time' => $validated['reservation_time'],
        ]);

        // Enviar notificação de confirmação ao utilizador
        $reservation->user->notify(new ReservationConfirmed($reservation));

        return response()->json($reservation, 201);
    }

    // Exibir detalhes de uma reserva específica
    public function show($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        return $reservation;
    }

    // Cancelar uma reserva
    public function destroy($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservation->delete();

        return response()->json(['message' => 'Reservation canceled']);
    }
}
