<!-- resources/views/planillas/create.blade.php -->
<x-app-layout>
    <!-- Mensajes de éxito o error -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
     @endif
 
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <div class="p-6">
        <h2 class="text-xl font-bold">Crear Nueva Planilla</h2>
        <form action="{{ route('planillas.store') }}" method="POST" class="mt-4">
            @csrf
            <div class="mb-3">
                <label for="fecha" class="block font-medium">Fecha</label>
                <input type="date" name="fecha" class="w-full border rounded p-2" required>
            </div>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
        </form>
    </div>
</x-app-layout>
