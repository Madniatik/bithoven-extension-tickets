<?php

namespace Bithoven\Tickets\Database\Seeders\Data;

/**
 * Tickets Extension Permissions Data
 * 
 * Estructura de cada permiso:
 * [
 *     'name' => 'extensions:tickets:scope:action',
 *     'alias' => 'Nombre Amigable',
 *     'description' => 'Descripción detallada del permiso'
 * ]
 */
class TicketsPermissions
{
    /**
     * Get all tickets extension permissions with alias and descriptions
     * 
     * @return array
     */
    public static function all(): array
    {
        return [
            // ===========================================
            // BASE (4 permisos)
            // ===========================================
            [
                'name' => 'extensions:tickets:base:view',
                'alias' => 'Ver Tickets',
                'description' => 'Permite ver la lista de tickets y sus detalles. Incluye acceso al módulo de soporte y visualización de tickets propios y asignados.'
            ],
            [
                'name' => 'extensions:tickets:base:create',
                'alias' => 'Crear Tickets',
                'description' => 'Permite crear nuevos tickets de soporte. Incluye selección de categoría, prioridad, asignación y carga de archivos adjuntos.'
            ],
            [
                'name' => 'extensions:tickets:base:edit',
                'alias' => 'Editar Tickets',
                'description' => 'Permite modificar tickets existentes, incluyendo título, descripción, prioridad, estado y agregar comentarios.'
            ],
            [
                'name' => 'extensions:tickets:base:assign',
                'alias' => 'Asignar Tickets',
                'description' => 'Permite asignar y reasignar tickets a agentes de soporte. Incluye transferencia entre equipos y cambio de responsable.'
            ],

            // ===========================================
            // CATEGORIES (1 permiso)
            // ===========================================
            [
                'name' => 'extensions:tickets:categories:manage',
                'alias' => 'Gestionar Categorías',
                'description' => 'Permite crear, editar y eliminar categorías de tickets. Incluye configuración de colores, iconos y descripción de cada categoría.'
            ],

            // ===========================================
            // TEMPLATES (1 permiso)
            // ===========================================
            [
                'name' => 'extensions:tickets:templates:manage',
                'alias' => 'Gestionar Plantillas',
                'description' => 'Permite gestionar plantillas de tickets y respuestas predefinidas. Incluye creación de templates para problemas comunes y respuestas rápidas.'
            ],

            // ===========================================
            // AUTOMATION (1 permiso)
            // ===========================================
            [
                'name' => 'extensions:tickets:automation:manage',
                'alias' => 'Gestionar Automatización',
                'description' => 'Permite configurar reglas de automatización para tickets. Incluye asignación automática, cambios de estado y notificaciones según condiciones.'
            ],

            // ===========================================
            // SLA (1 permiso)
            // ===========================================
            [
                'name' => 'extensions:tickets:sla:manage',
                'alias' => 'Gestionar SLA',
                'description' => 'Permite configurar acuerdos de nivel de servicio (SLA). Incluye tiempos de respuesta y resolución según prioridad del ticket.'
            ],
        ];
    }

    /**
     * Get permissions grouped by scope
     * 
     * @return array
     */
    public static function byScope(): array
    {
        $all = self::all();
        $grouped = [];

        foreach ($all as $permission) {
            // Format: extensions:tickets:scope:action
            $parts = explode(':', $permission['name']);
            $scope = $parts[2] ?? 'other';
            
            if (!isset($grouped[$scope])) {
                $grouped[$scope] = [];
            }
            
            $grouped[$scope][] = $permission;
        }

        return $grouped;
    }

    /**
     * Get permission names only
     * 
     * @return array
     */
    public static function names(): array
    {
        return array_column(self::all(), 'name');
    }
}
