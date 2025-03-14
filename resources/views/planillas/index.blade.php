<x-app-layout>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Lista de Planillas</h2>

        <!-- Mensajes de éxito o error -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline">{{ $errors->first('error') }}</span>
            </div>
        @endif

        <!-- Botón para crear nueva planilla -->
        @if (Auth::user()->rol == 'guia' || Auth::user()->rol == 'admin' || Auth::user()->rol == 'encargado_turismo')
            <a href="{{ route('planillas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out inline-block mb-6">
                Crear Nueva Planilla
            </a>
        @endif

        <!-- Formulario de Filtros -->
        <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
            <form action="{{ route('planillas.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Filtro por Fecha -->
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" name="fecha" id="fecha" value="{{ $fechaFiltro }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Filtro por Guía (solo para admin y encargado) -->
                @if (Auth::user()->rol === 'admin' || Auth::user()->rol === 'encargado_turismo')
                    <div>
                        <label for="guia_id" class="block text-sm font-medium text-gray-700">Guía</label>
                        <select name="guia_id" id="guia_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos los Guías</option>
                            @foreach ($guias as $guia)
                                <option value="{{ $guia->id }}" {{ $guiaFiltro == $guia->id ? 'selected' : '' }}>
                                    {{ $guia->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Botón de Aplicar Filtros -->
                <div class="self-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-300 ease-in-out">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de Planillas -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-4 text-left text-gray-700 font-semibold">
                            <a href="{{ route('planillas.index', ['sort_by' => 'numero', 'sort_order' => $sortBy === 'numero' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center sort-link">
                                #
                                @if ($sortBy === 'numero')
                                    <span class="sort-icon">
                                        @if ($sortOrder === 'asc')
                                            ▲
                                        @else
                                            ▼
                                        @endif
                                    </span>
                                @endif
                            </a>
                        </th>
                        <th class="p-4 text-left text-gray-700 font-semibold">
                            <a href="{{ route('planillas.index', ['sort_by' => 'fecha', 'sort_order' => $sortBy === 'fecha' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center sort-link">
                                Fecha
                                @if ($sortBy === 'fecha')
                                    <span class="sort-icon">
                                        @if ($sortOrder === 'asc')
                                            ▲
                                        @else
                                            ▼
                                        @endif
                                    </span>
                                @endif
                            </a>
                        </th>
                        <th class="p-4 text-left text-gray-700 font-semibold">Guía</th>
                        <th class="p-4 text-left text-gray-700 font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($planillas as $planilla)
                        <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                            <td class="p-4 text-gray-600">{{ $planilla->numero }}</td>
                            <td class="p-4 text-gray-600">{{ $planilla->fecha }}</td>
                            <td class="p-4 text-gray-600">{{ $planilla->guia->name }}</td>
                            <td class="p-4">
                                <a href="{{ route('planillas.show', $planilla->id) }}" class="text-blue-600 hover:text-blue-800 transition duration-300 ease-in-out">
                                    Ver
                                </a>
                                <a href="{{ route('planillas.imprimir', $planilla->id) }}" class="ml-4 text-green-600 hover:text-green-800 transition duration-300 ease-in-out">
                                    Imprimir <i class="fas fa-print"></i> <!-- Ícono de impresión de FontAwesome -->
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $planillas->appends(request()->query())->links() }}
        </div>
    </div>

    <style>
        .sort-link {
            color: #4a5568; /* Color gris */
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .sort-link:hover {
            color: #2d3748; /* Color gris más oscuro */
        }
        .sort-icon {
            font-size: 0.8em;
            margin-left: 0.5em;
        }
    </style>
</x-app-layout>