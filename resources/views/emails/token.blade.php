<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Pago</title>
</head>
<body>
    <h1>Confirmación de Pago</h1>
    <p>Estimado {{ $cliente->nombres }},</p>
    <p>Gracias por utilizar nuestros servicios. Para completar su pago de {{ $monto }} COP, por favor use el siguiente token de confirmación:</p>
    <h2>{{ $token }}</h2>
    <p>Recuerde que este token es válido por una sola vez.</p>
</body>
</html>
