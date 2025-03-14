<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Turista
        </h2>
    </x-slot>
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
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

            <!-- Formulario de edición -->
            <form action="{{ route('turistas.update', ['planilla' => $planilla->id, 'turista' => $turista->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                        <select name="tipo_documento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="RG" {{ $turista->tipo_documento === 'RG' ? 'selected' : '' }}>RG</option>
                            <option value="CPF" {{ $turista->tipo_documento === 'CPF' ? 'selected' : '' }}>CPF</option>
                            <option value="DNI" {{ $turista->tipo_documento === 'DNI' ? 'selected' : '' }}>DNI</option>
                        </select>
                    </div>
                    <div>
                        <label for="numero_documento" class="block text-sm font-medium text-gray-700">Número de Documento</label>
                        <input type="text" name="numero_documento" value="{{ $turista->numero_documento }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="nombre" value="{{ $turista->nombre }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700">Correo</label>
                        <input type="email" name="correo" value="{{ $turista->correo }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="telefono" value="{{ $turista->telefono }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Turismo</label>
                        <p class="mt-1 block w-full rounded-md bg-gray-100 p-2">
                            {{ $planilla->fecha }} <!-- Mostrar la fecha de la planilla -->
                        </p>
                        <input type="hidden" name="fecha" value="{{ $planilla->fecha }}"> <!-- Enviar la fecha como campo oculto -->
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300">
                        Actualizar Turista
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>