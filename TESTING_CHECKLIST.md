# Checklist de Pruebas Manuales - Sistema de Parqueadero

## Flujo de Entrada

- [ ] Buscar vehículo existente por placa
- [ ] Registrar nuevo vehículo si no existe
- [ ] Seleccionar estacionamiento y espacio disponible
- [ ] Registrar entrada exitosamente
- [ ] Verificar que PDF se descarga automáticamente
- [ ] Verificar contenido del PDF (información correcta)
- [ ] Verificar que se muestra información del ticket creado
- [ ] Probar botón de re-descargar recibo
- [ ] Verificar que el espacio queda ocupado

## Flujo de Salida

- [ ] Buscar ticket activo por placa
- [ ] Verificar que se muestra información del ticket
- [ ] Verificar cálculo de precio en tiempo real
- [ ] Verificar tiempo transcurrido se actualiza
- [ ] Generar PDF antes de confirmar salida (opcional)
- [ ] Verificar contenido del PDF pre-salida
- [ ] Confirmar salida
- [ ] Verificar que se muestra resumen final
- [ ] Generar PDF de salida con precio final
- [ ] Verificar contenido del PDF de salida (precio calculado correctamente)
- [ ] Verificar que el espacio queda liberado
- [ ] Verificar que el ticket queda marcado como cerrado

## Casos Edge

- [ ] Intentar registrar entrada con espacio ocupado
- [ ] Intentar registrar salida con ticket inexistente
- [ ] Intentar generar PDF de salida sin haber registrado salida
- [ ] Probar con diferentes tipos de vehículos (car, motorcycle, truck)
- [ ] Probar con diferentes horarios (día/noche) para verificar cálculo de precio
- [ ] Probar con estacionamientos diferentes
- [ ] Verificar autenticación en todos los endpoints

## Validación de PDFs

- [ ] Verificar diseño profesional y legible
- [ ] Verificar que toda la información está presente
- [ ] Verificar formato de fechas y horas
- [ ] Verificar cálculo de precios correcto
- [ ] Verificar que PDF se abre correctamente en diferentes navegadores
- [ ] Verificar que PDF se puede imprimir correctamente

## Notas de Prueba

### Navegadores a Probar
- Chrome (última versión)
- Firefox (última versión)
- Edge (última versión)

### Escenarios de Precio
- Estacionamiento durante horas diurnas solamente
- Estacionamiento durante horas nocturnas solamente
- Estacionamiento que cruza de día a noche
- Estacionamiento que cruza medianoche

### Validación de Datos en PDFs
- Información del vehículo (placa, propietario, tipo)
- Información del estacionamiento (nombre, dirección)
- Información del espacio (número, tipo)
- Fechas y horas de entrada/salida
- Cálculo de horas y precio
- Método de pago (si aplica)


