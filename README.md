# Servicio SOAP de Billetera Virtual

Este servicio SOAP simula una billetera virtual, permitiendo registrar clientes, recargar saldo, realizar pagos con confirmación y consultar el saldo.

## Endpoints Disponibles

### 1. `registerClient`

Registra un nuevo cliente en el sistema.

**Parámetros:**

-   `documento` (string): Documento de identidad del cliente.
-   `nombres` (string): Nombres del cliente.
-   `email` (string): Correo electrónico del cliente.
-   `celular` (string): Número de celular del cliente.

**Respuesta:**

```xml
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAP-ENV:Body>
        <registerClientResponse>
            <return xsi:type="ns2:Map">
                <item>
                    <key xsi:type="xsd:string">success</key>
                    <value xsi:type="xsd:boolean">false</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">cod_error</key>
                    <value xsi:type="xsd:string">400</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">message_error</key>
                    <value xsi:type="xsd:string">Cliente con el documento 1234567890 ya está registrado.</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">data</key>
                    <value xsi:nil="true" />
                </item>
            </return>
        </registerClientResponse>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

### 2. `loadWallet`

Recarga saldo en la billetera de un cliente existente.

**Parámetros:**

-   `cliente_id ` (integer): ID del cliente.
-   `monto ` (decimal): Monto a recargar.

**Respuesta:**

```xml
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAP-ENV:Body>
        <loadWalletResponse>
            <return xsi:type="ns2:Map">
                <item>
                    <key xsi:type="xsd:string">success</key>
                    <value xsi:type="xsd:boolean">true</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">cod_error</key>
                    <value xsi:nil="true" />
                </item>
                <item>
                    <key xsi:type="xsd:string">message_error</key>
                    <value xsi:nil="true" />
                </item>
                <item>
                    <key xsi:type="xsd:string">data</key>
                    <value xsi:type="xsd:string">Recarga exitosa. Nuevo saldo: 600</value>
                </item>
            </return>
        </loadWalletResponse>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

### 3. `makePayment`

Genera un token de confirmación para realizar un pago.

**Parámetros:**

-   `cliente_id ` (integer): ID del cliente.
-   `monto ` (decimal): Monto a recargar.

**Respuesta:**

```xml
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAP-ENV:Body>
        <makePaymentResponse>
            <return xsi:type="ns2:Map">
                <item>
                    <key xsi:type="xsd:string">success</key>
                    <value xsi:type="xsd:boolean">true</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">cod_error</key>
                    <value xsi:nil="true" />
                </item>
                <item>
                    <key xsi:type="xsd:string">message_error</key>
                    <value xsi:nil="true" />
                </item>
                <item>
                    <key xsi:type="xsd:string">data</key>
                    <value xsi:type="xsd:string">Token de confirmación generado. Id de sesión: 9aa9ab58-9a10-47a1-8814-d2540e7018b9.</value>
                </item>
            </return>
        </makePaymentResponse>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

### 4. `confirmPayment`

Confirma el pago utilizando el `id_sesion` y el `token`.

**Parámetros:**

-   `id_sesion ` (string): ID de sesión del pago.
-   `token ` (string): Token de confirmación.

**Respuesta:**

```xml
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAP-ENV:Body>
        <confirmPaymentResponse>
            <return xsi:type="ns2:Map">
                <item>
                    <key xsi:type="xsd:string">success</key>
                    <value xsi:type="xsd:boolean">false</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">cod_error</key>
                    <value xsi:type="xsd:string">400</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">message_error</key>
                    <value xsi:type="xsd:string">La transacción ya ha sido confirmada o cancelada.</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">data</key>
                    <value xsi:nil="true" />
                </item>
            </return>
        </confirmPaymentResponse>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

### 5. `checkBalance`

Consulta el saldo de la billetera del cliente.

**Parámetros:**

-   `cliente_id ` (integer): ID del cliente.

**Respuesta:**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:ns1="http://127.0.0.1:8000/soap-server" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:ns2="http://xml.apache.org/xml-soap"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    <SOAP-ENV:Body>
        <ns1:checkBalanceResponse>
            <return xsi:type="ns2:Map">
                <item>
                    <key xsi:type="xsd:string">success</key>
                    <value xsi:type="xsd:boolean">true</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">cod_error</key>
                    <value xsi:type="xsd:string">00</value>
                </item>
                <item>
                    <key xsi:type="xsd:string">message_error</key>
                    <value xsi:nil="true" />
                </item>
                <item>
                    <key xsi:type="xsd:string">data</key>
                    <value xsi:type="xsd:string">Saldo actual de la billetera: 600.00</value>
                </item>
            </return>
        </ns1:checkBalanceResponse>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```
