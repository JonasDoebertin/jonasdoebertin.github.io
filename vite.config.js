import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import jigsaw from '@tighten/jigsaw-vite-plugin';

export default defineConfig({
    plugins: [
        tailwindcss(),
        jigsaw({
            input: ['source/_assets/js/main.js', 'source/_assets/css/main.css'],
            refresh: true,
        }),
    ],
});
