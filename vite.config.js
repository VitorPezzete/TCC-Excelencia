import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/accessibility.css', 'resources/js/app.js', 'resources/js/login.js', 'resources/js/perfil.js', 'resources/js/carrinho.js', 'resources/js/admin.js', 'resources/js/accessibility.js', 'resources/js/cardapio.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
