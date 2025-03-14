<!-- resources/views/planillas/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Turista</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('turistas.update', $turista->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                
                    <div class="mb-4">
                        <label class="block">Tipo de Documento</label>
                        <select name="tipo_documento" class="w-full border rounded p-2">
                            <option value="RG" {{ $turista->tipo_documento == 'RG' ? 'selected' : '' }}>RG</option>
                            <option value="CPF" {{ $turista->tipo_documento == 'CPF' ? 'selected' : '' }}>CPF</option>
                            <option value="DNI" {{ $turista->tipo_documento == 'DNI' ? 'selected' : '' }}>DNI</option>
                        </select>
                    </div>
                
                    <div class="mb-4">
                        <label class="block">Número de Documento</label>
                        <input type="text" name="numero_documento" value="{{ $turista->numero_documento }}" class="w-full border rounded p-2" required>
                    </div>
                
                    <div class="mb-4">
                        <label class="block">Nombre</label>
                        <input type="text" name="nombre" value="{{ $turista->nombre }}" class="w-full border rounded p-2" required>
                    </div>
                
                    <div class="mb-4">
                        <label class="block">Correo</label>
                        <input type="email" name="correo" value="{{ $turista->correo }}" class="w-full border rounded p-2">
                    </div>
                
                    <div class="mb-4">
                        <label class="block">Teléfono</label>
                        <input type="text" name="telefono" value="{{ $turista->telefono }}" class="w-full border rounded p-2">
                    </div>
                
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
