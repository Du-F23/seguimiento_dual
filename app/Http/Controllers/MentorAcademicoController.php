<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Vinkla\Hashids\Facades\Hashids;

class MentorAcademicoController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except('showJson');
    }

    public function index(): View
    {
        $mentores = User::where('rol_id', 2)->get();
        $mentoresDeleted = User::onlyTrashed()->where('rol_id', 2)->get();

        return view('mentoresacademicos.index', compact('mentores', 'mentoresDeleted'));
    }

    public function create(): View
    {
        return view('mentoresacademicos.create');
    }

    public function store(Request $request)
    {
        $request->validate(['titulo' => ['required', 'string', 'max:255'], 'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],]);

        $user = User::create(['titulo' => $request->titulo, 'name' => $request->name, 'email' => $request->email, 'password' => Hash::make('12345678'), 'rol_id' => 2, 'carrera_id' => 2]);

        return redirect()->route('academicos.index')->with('message', 'Mentor Academico creado Correctamente');
    }

    public function show($id): View
    {
        $id = Hashids::decode($id);
        $mentor = User::find($id);
        $mentor = $mentor[0];

        return view('mentoresacademicos.show', compact('mentor'));
    }

    public function edit($id): View
    {
        $id = Hashids::decode($id);
        $mentor = User::find($id);
        $mentor = $mentor[0];

        return view('mentoresacademicos.edit', compact('mentor'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate(['titulo' => ['string', 'max:255'], 'name' => ['min:3', 'string', 'max:255'], 'email' => ['required', 'string', 'email', 'max:255'],]);

//        dd($request->all());
        $mentor = User::find($id);
        if ($request->email !== $mentor->email) {
            $request->validate(['email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class]]);
            $mentor->update(['email' => $request->email], $request->all());
        }
        $mentor->update($request->all());

        return redirect()->route('academicos.index');
    }

    public function destroy($id)
    {
        try {
            $mentor = User::find($id);
            $mentor->delete();

            return redirect()->route('academicos.index')->with('messageDelete', 'Mentor Academico Eliminado Correctamente');

        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];

            if ($errorCode == 1451) {
                // Error de integridad referencial (clave foránea)
                return redirect()->route('academicos.index')->with('statusError', 'No se puede eliminar el Mentor Academico. Primero elimina los estudiantes asociados');
            }

            // Otro tipo de error, puedes manejarlo según tus necesidades
            return redirect()->route('academicos.index')->with('statusError', 'Error al eliminar el Mentor Academico: ' . $e->getMessage());
        }
    }

    public function showJson($id): JsonResponse
    {
        $mentores = User::withTrashed()->find($id);
        return response()->json($mentores);
    }

    public function restoreMentor($id): RedirectResponse
    {
        $mentor = User::onlyTrashed()->find($id);
        $mentor->restore();

        return redirect()->route('academicos.index')->with('success', 'Mentor Academico Restaurado.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $mentor = User::onlyTrashed()->find($id);
        $mentor->forceDelete();

        return redirect()->route('academicos.index')->with('success', 'Mentor Academico Eliminado Correctamente.');
    }
}
