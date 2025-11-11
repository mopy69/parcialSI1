# Sistema Automático de Gestión de Términos Académicos

## 📋 Descripción

El sistema ahora activa y desactiva automáticamente las gestiones académicas según sus fechas de inicio y fin.

## ✅ Características Implementadas

### 1. **Activación/Desactivación Automática**
- Las gestiones se activan automáticamente cuando la fecha actual está dentro del rango (start_date - end_date)
- Se desactivan automáticamente cuando la fecha actual está fuera del rango
- Ejecuta diariamente a la medianoche

### 2. **Comando Manual**
Puedes ejecutar manualmente el comando para actualizar el estado:

```bash
php artisan terms:update-status
```

**Salida del comando:**
- ✓ Activada: Nombre de la gestión (fecha inicio - fecha fin)
- ✗ Desactivada: Nombre de la gestión (fecha inicio - fecha fin)
- Total de gestiones activadas/desactivadas

### 3. **Ejecución Automática Programada**
El comando se ejecuta automáticamente cada día a la medianoche.

**Para que funcione en producción, configura el scheduler:**

#### En Windows:
1. Abre "Programador de tareas" (Task Scheduler)
2. Crea una nueva tarea básica
3. Nombre: "Laravel Scheduler"
4. Desencadenador: Diariamente, cada 1 día, a las 00:00
5. Acción: Iniciar programa
   - Programa: `php`
   - Argumentos: `artisan schedule:run`
   - Iniciar en: `C:\Users\Leonardo\Documents\U\si1\parcialSI1`

#### En Linux/Mac:
Agrega al crontab:
```bash
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### 4. **Scopes del Modelo Term**

Ahora puedes usar estos métodos en el modelo:

```php
// Obtener solo gestiones activas
$activeTerms = Term::active()->get();

// Obtener la gestión actual (activa y dentro del rango)
$currentTerm = Term::current()->first();

// Verificar si una gestión debería estar activa
if ($term->shouldBeActive()) {
    // La gestión está dentro del rango de fechas
}
```

### 5. **Integración con el Sistema**

El sistema ahora:
- ✅ Solo muestra gestiones activas en selectores
- ✅ Solo usa gestiones activas para asignaciones
- ✅ Actualiza automáticamente la gestión actual en la sesión
- ✅ Previene el uso de gestiones inactivas

## 🔧 Comandos Disponibles

```bash
# Actualizar estado de gestiones manualmente
php artisan terms:update-status

# Ver todas las tareas programadas
php artisan schedule:list

# Ejecutar todas las tareas programadas ahora (para testing)
php artisan schedule:run
```

## 📊 Lógica de Activación

```
Fecha Actual: 2025-11-11

Gestión A: 2025-01-01 - 2025-06-30 → INACTIVA (ya pasó)
Gestión B: 2025-09-01 - 2025-12-31 → ACTIVA (estamos dentro)
Gestión C: 2026-01-01 - 2026-06-30 → INACTIVA (aún no empieza)
```

## ⚠️ Importante

1. **Primera Ejecución**: Ejecuta manualmente el comando para actualizar el estado inicial:
   ```bash
   php artisan terms:update-status
   ```

2. **Scheduler**: Asegúrate de configurar el Laravel Scheduler para que las tareas programadas funcionen automáticamente.

3. **Base de Datos**: El campo `asset` (activo) se actualiza automáticamente. No necesitas modificarlo manualmente.

## 🎯 Beneficios

- ✅ No necesitas activar/desactivar gestiones manualmente
- ✅ Las gestiones se activan el día que inician
- ✅ Se desactivan automáticamente cuando terminan
- ✅ Previene errores de usar gestiones incorrectas
- ✅ Sistema completamente automatizado

## 📝 Notas

- El sistema verifica fechas una vez al día (medianoche)
- Puedes ejecutar el comando manualmente cuando necesites
- Las gestiones activas son las únicas que se pueden seleccionar en el sistema
- La gestión actual en sesión se actualiza automáticamente
