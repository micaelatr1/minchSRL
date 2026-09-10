# SIC-MINCH — Resumen de Actividades (1–10)

Sistema de Información Contable para MINCH SRL. — Proyecto académico de software.

---

## Actividad 1 — Perfil del Proyecto

**Archivo:** `perfil-proyecto.tex`

- Solo "Introducción" como título, texto continuo sin secciones intermedias
- Stack corregido: PHP 8.3, Laravel 13, Livewire 4, TallStackUI 3.1, Fortify, Spatie Permission, Tailwind CSS v4, mPDF, Maatwebsite/Excel, **MySQL** (no PostgreSQL como decía el original)
- Objetivo general: implementar sistema web contable para retenciones y movimientos de caja
- 3 objetivos específicos: módulo retenciones, módulo caja, módulo reportes
- Problema: procesos manuales en Excel, 40% de jornada en tareas administrativas
- Justificación económica, social y técnica
- Métodos: análisis-síntesis, inducción-deducción, modelación, sistémico

---

## Actividad 2 — Análisis Documental

**Archivo:** `analisis-documental.tex`

- Matriz de 10 documentos analizados
- 3 cuestionarios (Auxiliar, Contador, Gerente)
- 3 guías de entrevista (por rol)
- 2 guías de observación (retenciones + caja)
- Plantillas de interpretación vacías
- Situación actual del proceso
- 9 puntos de dolor con soluciones propuestas

---

## Actividad 3 — Casos de Uso

**Archivo:** `casos-de-uso.tex`

- 6 perfiles de usuario (Admin, Contador, Auxiliar, Gerente, Proveedor)
- 7 módulos con actores, casos y relaciones (include/extend) descritos textualmente
- 12 casos de uso detallados:
  - CU-01: Inicio de sesión
  - CU-02: Gestionar usuarios
  - CU-03: Gestionar roles y permisos
  - CU-04: Gestionar proveedores
  - CU-05: Registrar retención
  - CU-06: Generar recibo PDF de retención
  - CU-08: Registrar movimiento de caja
  - CU-11: Registrar movimiento bancario
  - CU-13: Consultar estado de cuenta
  - CU-15: Generar reportes
- Cada CU: flujo básico, flujo alterno, precondiciones, postcondiciones
- Tabla de contraste con entrevistas/observación
- Tabla de cumplimiento de objetivos

---

## Actividad 4 — Scrum (Fase Inicial)

**Archivo:** `actividad-4-scrum.tex`

- Visión del producto (formato ágil)
- 3 personas: Carlos (Auxiliar 28a), Carmen (Contador 35a), Pedro (Gerente 45a)
- DoR (Definition of Ready) — 6 criterios
- DoD (Definition of Done) — 8 criterios
- 6 Épicas:
  - EP-01: Retenciones (26 SP)
  - EP-02: Libro de bancos (19 SP)
  - EP-03: Caja (14 SP)
  - EP-04: Estados de cuenta (11 SP)
  - EP-05: Reportes y dashboard (16 SP)
  - EP-06: Administración (22 SP)
- **27 user stories**, **108 SP total**
- Sprint 1: 9 historias, 40 SP (admin + retenciones básicas)
- Tareas desglosadas para HU-01 a HU-04 con responsables y entregables

---

## Actividad 5 — (Login mejorado)

Trabajo extra: mejora de la vista de login con animaciones CSS (fade-in escalonado, glow, grid pattern) en:
- `resources/css/app.css`
- `resources/views/components/guest-layout.blade.php`
- `resources/views/pages/auth/login.blade.php`

---

## Actividad 6 — Estudio de Factibilidad y Viabilidad (COCOMO)

**Archivo:** `actividad-6-factibilidad.tex`

- Plan de trabajo: 12 actividades (A1–A12), 80 días
- 6 metas SMART
- Equipo: Analista (85%), Técnico 1 (100%), Técnico 2 (100%), Gerente (8%), Contador (15%), Auxiliar (12%)
- **COCOMO semiacoplado**: 19 KDSI, E=84.9 pm, T=11.8 meses teóricos
- **Costo total: Bs. 424.545**
- 4 solicitudes de cambio registradas
- 8 riesgos identificados con mitigación
- VAN = -60.461 Bs (negativo a 5 años), Payback 6.05 años, B/C = 0.86
- Viabilidad: técnica/operativa/legal/cronológica/RRHH = Sí, económica = Parcial
- Infraestructura: VPS, NAS, 2 PCs desarrollo, 1 PC pruebas — total Bs. 36.607

---

## Actividad 7 — Fase Final de Implementación (Sprint Final)

**Archivo:** `actividad-7-implementacion.tex`

- Sprint 1 completado: 9 HU, 40 SP
- Sprint Final: 7 HU, 43 SP (libro de bancos, caja, estados cuenta, reportes)
- 22 tareas desglosadas con responsables
- 17 interfaces con estado completo (diseño ✓, frontend ✓, backend ✓, pruebas ✓)
- Preparación del release: CI/CD (Pint → Pest → Vite → deploy), pre-producción, pruebas PO, capacitación por rol

---

## Actividad 8 — Pruebas del Sistema

**Archivo:** `actividad-8-pruebas.tex`

| Bloque | Herramienta | Resultado |
|---|---|---|
| Unitarias / Caja Blanca | Pest PHP | 90.2% líneas, 84.4% ramas. 0 fallas |
| E2E Frontend | Cypress | 4 specs PASS (Auxiliar, Contador, Admin, Gerente) |
| E2E Backend | Postman + Newman | 6 endpoints OK con validación RBAC |
| Caja Negra | Manual | 31 casos, 100% OK (valores límite 10.00–999,999,999.99) |
| Carga y Estrés | JMeter | 100us OK / 200us OK / 350us FAIL (22% error) |
| Normativa | Checklist | 7/7 requisitos cumplen |
| **Total** | | **96 pruebas, 95 aprobadas, 98.9% éxito** |

---

## Actividad 9 — Calidad ISO/IEC 25010

**Archivo:** `actividad-9-calidad.tex`

| Característica | Puntuación | Estado |
|---|---|---|
| Adecuación Funcional | 93.3 | Aprobado (3 HU pendientes post-release) |
| Fiabilidad | 98.5 | Aprobado |
| Eficiencia de Rendimiento | 88.0 | Aprobado |
| Usabilidad | 90.0 | Aprobado (SUS 84) |
| Seguridad | 97.0 | Aprobado (Spatie + Fortify) |
| Compatibilidad | 96.0 | Aprobado |
| Mantenibilidad | 94.0 | Aprobado (90.2% cobertura) |
| Portabilidad | 92.0 | Aprobado |
| **PROMEDIO** | **93.6** | **Calidad alta** |

Adaptaciones por ser monolito Livewire: métricas de hidratación de componentes, RBAC con Spatie en vez de JWT, Sin FDA/pharma — solo normativa contable boliviana.

---

## Actividad 10 — Manuales Técnicos

**Archivo:** `actividad-10-manuales.tex`

4 manuales:

1. **Manual de Usuario** — Auxiliar, Contador, Gerente. SOP paso a paso para retenciones, caja, libro bancos, estados cuenta, reportes, FAQ
2. **Manual de Administrador** — RBAC Spatie, impuestos (RC-IVA/IUE/IT), cuentas bancarias, respaldos, logs
3. **Manual de Instalación y Despliegue** — `composer install`, `key:generate`, `migrate --seed`, `npm run build`, Nginx, SSL
4. **Manual de Base de Datos** — Diccionario de tablas (retentions, transactions, boxes, movements, taxes, persons), eventos lockForUpdate(), índices, script mysqldump

Incluye: portada, control de cambios, glosario de 14 términos contables bolivianos.

---

## Stack Tecnológico (verificado contra código fuente)

| Componente | Tecnología |
|---|---|
| Backend | PHP 8.3, Laravel 13 |
| Frontend | Livewire 4, TallStackUI 3.1, Tailwind CSS v4 |
| Base de datos | MySQL 8.0 (SQLite en test) |
| Autenticación | Laravel Fortify + Sanctum |
| RBAC | Spatie Permission (35 permisos, middleware `permission`) |
| PDF | mPDF con NumberHelper (montos en literal español) |
| Excel | Maatwebsite/Laravel-Excel |
| Testing | Pest PHP con cobertura |
| Assets | Vite |
| Cola/Queue | database driver |
| Caché | database driver |

## Reglas de Negocio Clave

- **Retenciones**: código correlativo por mes calendario con `lockForUpdate()`. Formato: `ABR-001`
- **Transacciones bancarias**: número por año fiscal (octubre–septiembre) con `lockForUpdate()`
- **Caja**: número por mes calendario con `lockForUpdate()`
- **Cálculo retenciones**: total = monto / (1 - sum(tasas)/100). Servicios: RC-IVA 13% + IT 3%. Bienes: IUE 5% + IT 3%
- **Movement types**: D = Débito, C = Crédito, B = Balance
- **Transaction payment_type**: T = Transferencia, CH = Cheque
- **Account**: HasManyThrough a Movement via Transaction
