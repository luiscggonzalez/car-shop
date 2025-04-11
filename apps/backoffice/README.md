# Car Shop Frontend

Este es el frontend de la aplicación Car Shop, desarrollado con React, TypeScript y Vite.

## Requisitos previos

- Node.js (versión 18 o superior)
- npm o yarn

## Instalación

1. Navega a la carpeta del frontend:
   ```
   cd apps/frontend
   ```

2. Instala las dependencias:
   ```
   npm install
   ```
   o
   ```
   yarn
   ```

## Desarrollo

Para iniciar el servidor de desarrollo:

```
npm run dev
```

o

```
yarn dev
```

Esto iniciará el servidor de desarrollo en http://localhost:3000.

## Construcción para producción

Para construir la aplicación para producción:

```
npm run build
```

o

```
yarn build
```

Los archivos de construcción se generarán en la carpeta `dist`.

## Estructura del proyecto

- `src/`: Contiene el código fuente de la aplicación
  - `assets/`: Recursos estáticos (imágenes, fuentes, etc.)
  - `routes/`: Rutas de la aplicación (usando TanStack Router)
    - `users/`: Rutas relacionadas con usuarios
      - `edit/`: Rutas para edición de usuarios
      - `create.tsx`: Ruta para crear usuarios
      - `list.tsx`: Ruta para listar usuarios
  - `sections/`: Organización por dominio
    - `users/`: Sección de usuarios
      - `components/`: Componentes específicos de usuarios
      - `pages/`: Páginas específicas de usuarios
      - `services/`: Servicios para comunicarse con la API de usuarios

## Tecnologías utilizadas

- React 19
- TypeScript
- TanStack Router para la navegación
- TanStack Table para tablas de datos
- React Hook Form para formularios
- Zod para validación de datos
- Tailwind CSS para estilos
- Radix UI para componentes accesibles

## Comunicación con el backend

La aplicación está configurada para comunicarse con el backend de Laravel a través de la URL base `/api`. Esto se configura en el archivo `vite.config.ts` mediante un proxy que redirige todas las solicitudes a `/api` al servidor de Laravel.
