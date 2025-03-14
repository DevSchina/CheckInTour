<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Planilla #{{ $planilla->numero }} - Fecha: {{ $planilla->fecha }}
        </h2>
    </x-slot>

    <!-- Mensajes de éxito o error -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <!-- Encabezado con botones de acción -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                <h3 class="text-2xl font-bold text-gray-800">Lista de Turistas</h3>
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                    <!-- Paso 1: Descargar Template -->
                    <div class="flex flex-col items-center space-y-2">
                        <a href="{{ asset('excel/turista-template.xlsx') }}" download class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300 text-center">
                            Paso 1: Descargar Template
                        </a>
                        <p class="text-sm text-gray-600 text-center">Descarga el archivo de plantilla para cargar turistas.</p>
                    </div>
                
                    <!-- Paso 2: Elegir Archivo -->
                    <div class="flex flex-col items-center space-y-2">
                        <form action="{{ route('planillas.cargarExcel', $planilla->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4">
                            @csrf
                            <!-- Input de archivo (oculto) -->
                            <input type="file" name="archivo_excel" required class="hidden" id="archivo_excel">
                
                            <!-- Botón para elegir archivo -->
                            <label for="archivo_excel" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-300 cursor-pointer text-center">
                                Paso 2: Elegir Archivo
                            </label>
                
                            <!-- Botón para importar datos -->
                            <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition duration-300 text-center">
                                Paso 3: Importar Datos
                            </button>
                
                            <!-- Mostrar el nombre del archivo seleccionado -->
                            <span id="nombre-archivo" class="text-gray-700 ml-2"></span>
                        </form>
                        <p class="text-sm text-gray-600 text-center">Selecciona el archivo y luego impórtalo.</p>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                    <!-- Botón para finalizar la planilla -->
                    @if(!$planilla->estado && (auth()->user()->rol === 'guia' || auth()->user()->rol === 'encargado_turismo' || auth()->user()->rol === 'admin'))
                        <form action="{{ route('planillas.finalizar', $planilla->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-300 text-center">
                                Finalizar Planilla
                            </button>
                        </form>
                    @endif

                    <!-- Botón para regresar atrás si la planilla está finalizada -->
                    @if($planilla->estado)
                        <a href="{{ route('planillas.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-300 text-center">
                            Atrás
                        </a>
                    @endif
                </div>
            </div>

            <!-- Tabla de turistas -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-700 uppercase">Tipo Documento</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-700 uppercase">Número Documento</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-700 uppercase">Nombre</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-700 uppercase">Correo</th>
                            <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-700 uppercase">Teléfono</th>
                            @if(auth()->user()->rol === 'encargado_turismo' || auth()->user()->rol === 'admin')
                                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-700 uppercase">Asistencia</th>
                            @endif
                            @if(auth()->user()->rol === 'encargado_turismo' || auth()->user()->rol === 'admin')
                                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold text-gray-700 uppercase">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($planilla->turistas as $turista)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <td class="px-6 py-4 border-b border-gray-200">{{ $turista->tipo_documento }}</td>
                                <td class="px-6 py-4 border-b border-gray-200">{{ $turista->numero_documento }}</td>
                                <td class="px-6 py-4 border-b border-gray-200">{{ $turista->nombre }}</td>
                                <td class="px-6 py-4 border-b border-gray-200">{{ $turista->correo }}</td>
                                <td class="px-6 py-4 border-b border-gray-200">{{ $turista->telefono }}</td>
                                @if(auth()->user()->rol === 'encargado_turismo' || auth()->user()->rol === 'admin')
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        <form action="{{ route('turistas.actualizarAsistencia', $turista->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="asistencia" value="0">
                                            <input 
                                                type="checkbox" 
                                                name="asistencia" 
                                                value="1" 
                                                {{ $turista->asistencia ? 'checked' : '' }}
                                                class="form-checkbox h-5 w-5 text-blue-600 rounded transition duration-200"
                                            >
                                        </form>
                                    </td>
                                @endif
                                @if(auth()->user()->rol === 'encargado_turismo' || auth()->user()->rol === 'admin')
                                    <td class="px-6 py-4 border-b border-gray-200">
                                        <a href="{{ route('turistas.edit', ['planilla' => $planilla->id, 'turista' => $turista->id]) }}" class="text-blue-500 hover:text-blue-700 mr-2">Editar</a>
                                        <form action="{{ route('turistas.destroy', $turista) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">Eliminar</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Botón para mostrar el formulario de agregar turista -->
            @if(!$planilla->estado && (auth()->user()->rol === 'guia' || auth()->user()->rol === 'encargado_turismo' || auth()->user()->rol === 'admin'))
                <div class="mt-6">
                    <button id="showFormButton" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300 w-full md:w-auto">
                        Agregar Nuevo Turista
                    </button>
                </div>
            @endif

            <!-- Formulario para agregar turistas (oculto inicialmente) -->
            @if(!$planilla->estado)
                <div id="turistaForm" class="mt-6 hidden bg-gray-50 p-6 rounded-lg shadow-inner">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Nuevo Turista</h3>
                    <form action="{{ route('turistas.store', $planilla->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                                <select name="tipo_documento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="RG">RG</option>
                                    <option value="CPF">CPF</option>
                                    <option value="DNI">DNI</option>
                                </select>
                            </div>
                            <div>
                                <label for="numero_documento" class="block text-sm font-medium text-gray-700">Número de Documento</label>
                                <input type="text" name="numero_documento" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input type="text" name="nombre" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="correo" class="block text-sm font-medium text-gray-700">Correo</label>
                                <input type="email" name="correo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="text" name="telefono" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-300 w-full md:w-auto">
                                Agregar Turista
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('showFormButton')?.addEventListener('click', function() {
            document.getElementById('turistaForm').classList.toggle('hidden');
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[name="asistencia"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const form = this.closest('form');
                    const url = form.action;
                    const token = form.querySelector('input[name="_token"]').value;
                    const data = { asistencia: this.checked ? 1 : 0 };

                    fetch(url, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Asistencia actualizada:', this.checked ? 'Presente' : 'Ausente');
                            location.reload();
                        } else {
                            console.error(data.error);
                            this.checked = !this.checked; // Revertir cambio si hay error
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.checked = !this.checked; // Revertir cambio si hay error
                    });
                });
            });
        });

        document.getElementById('archivo_excel').addEventListener('change', function() {
            // Obtener el nombre del archivo seleccionado
            const nombreArchivo = this.files[0] ? this.files[0].name : 'Ningún archivo seleccionado';

            // Mostrar el nombre del archivo en el span
            document.getElementById('nombre-archivo').textContent = nombreArchivo;
        });
    </script>
</x-app-layout>