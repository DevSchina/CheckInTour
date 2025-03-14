<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-r from-blue-50 to-indigo-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensaje de Bienvenida -->
            <div class="mb-8 p-6 bg-white rounded-lg shadow-md">
                <h1 class="text-2xl font-bold text-gray-800">
                    Bienvenido, {{ Auth::user()->name }}!
                </h1>
                <p class="text-gray-600 mt-2">
                    Tu rol es: <span class="font-semibold text-blue-600">{{ Auth::user()->rol }}</span>
                </p>
            </div>

            <!-- Tarjetas de Acción -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                <a href="{{ route('planillas.index') }}" class="block bg-blue-500 text-white px-6 py-6 rounded-lg shadow-lg hover:bg-blue-600 transition-all transform hover:scale-105">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-users text-3xl mr-3"></i>
                        <span class="text-xl font-semibold">Cargar Planilla</span>
                    </div>
                </a>
            </div>

            <!-- Resumen de Turistas -->
            @if(in_array(Auth::user()->rol, ['admin', 'encargado_turismo']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Resumen</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-gray-700">Turistas Confirmados:</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $turistasConfirmados }}</p>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-gray-700">Turistas Sin Confirmar:</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $turistasSinConfirmar }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tabla de Turistas Confirmados con Compra -->
            @if(in_array(Auth::user()->rol, ['admin', 'encargado_turismo']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Turistas Confirmados con Compra</h3>
                        @if ($detallesTuristasOracle->isEmpty())
                            <p class="text-gray-600">No hay turistas confirmados con datos en Oracle.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 border-b">ID</th>
                                            <th class="px-4 py-2 border-b">Fecha</th>
                                            <th class="px-4 py-2 border-b">Tipo Mov.</th>
                                            <th class="px-4 py-2 border-b">Estado</th>
                                            <th class="px-4 py-2 border-b">Nro. Documento</th>
                                            <th class="px-4 py-2 border-b">Nombre Cliente</th>
                                            <th class="px-4 py-2 border-b">Guía</th> <!-- Nueva columna para el guía -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detallesTuristasOracle as $turista)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 border-b">{{ ($detallesTuristasOracle->currentPage() - 1) * $detallesTuristasOracle->perPage() + $loop->iteration }}</td>
                                                <td class="px-4 py-2 border-b">
                                                    {{ \Carbon\Carbon::parse($turista['fecha'])->format('Y-m-d') }} <!-- Formatear la fecha -->
                                                </td>
                                                <td class="px-4 py-2 border-b">{{ $turista['tipo_mov'] }}</td>
                                                <td class="px-4 py-2 border-b">{{ $turista['estado'] }}</td>
                                                <td class="px-4 py-2 border-b">{{ $turista['num_doc'] }}</td>
                                                <td class="px-4 py-2 border-b">{{ $turista['nombre_cliente'] }}</td>
                                                <td class="px-4 py-2 border-b">{{ $turista['guia_nombre'] }}</td> <!-- Mostrar el nombre del guía -->
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            <div class="mt-4">
                                {{ $detallesTuristasOracle->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>