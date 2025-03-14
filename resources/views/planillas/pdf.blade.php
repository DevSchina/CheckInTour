<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planilla {{ $planilla->numero }}</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 60px; /* Ajusta el tamaño según necesites */
            margin-right: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <!-- ENCABEZADO CON LOGO Y TÍTULO -->
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        <h2>Shopping China Importados</h2>
    </div>

    <h1>Planilla #{{ $planilla->numero }}</h1>
    <p><strong>Fecha:</strong> {{ $planilla->fecha }}</p>
    <p><strong>Guía:</strong> {{ $planilla->guia->name }}</p>

    <h2>Turistas</h2>
    <table>
        <thead>
            <tr>
                <th>Tipo de Documento</th>
                <th>Número de Documento</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @foreach($planilla->turistas as $turista)
                <tr>
                    <td>{{ $turista->tipo_documento }}</td>
                    <td>{{ $turista->numero_documento }}</td>
                    <td>{{ $turista->nombre }}</td>
                    <td>{{ $turista->correo }}</td>
                    <td>{{ $turista->telefono }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>